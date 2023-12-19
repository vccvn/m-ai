<?php

namespace App\Repositories\Orders;

use App\Masks\Orders\CartCollection;
use App\Masks\Orders\CartMask;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Promo;
use Gomee\Repositories\BaseRepository;
use App\Repositories\Products\ProductAttributeRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Promos\PromoRepository;
use App\Repositories\StyleSets\StyleSetItemRepository;
use App\Repositories\Users\UserDiscountRepository;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CartRepository extends BaseRepository
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Cart::class;
    }

    protected $maskClass = CartMask::class;
    protected $maskCollectionClass = CartCollection::class;
    protected $responseMode = 'mask';
    protected static $currentCartID = 0;
    /**
     * Cart
     *
     * @var Cart
     */
    protected $cart = null;
    protected $currentAttrs = [];
    public $actionStatus = false;
    public $actionMessage = "Thao tác thành công";


    /**
     * repo
     *
     * @var \App\Repositories\Products\ProductAttributeRepository
     */
    protected $productAttributeRepository;

    /**
     * repo
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * repo
     *
     * @var CartItemRepository
     */
    protected $cartItemRepository;

    /**
     * Undocumented variable
     *
     * @var PromoRepository
     */
    protected $promoRepository;
    /**
     * Undocumented variable
     *
     * @var UserDiscountRepository
     */
    protected $userDiscountRepository;

    public $needDeleteItemStatus = false;
    public $needDeleteItemList = [];

    public $useCookie = false;
    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        $this->addDefaultCondition('type', 'type', 'cart');

        $this->productAttributeRepository = app(ProductAttributeRepository::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productRepository->notTrashed();
        $this->cartItemRepository = app(CartItemRepository::class);
        $this->promoRepository = app(PromoRepository::class);
        $this->userDiscountRepository = app(UserDiscountRepository::class);

        // if (!static::$currentCartID) {
        //     $cookieCartId = Cookie::get('cart_id');
        //     if ($cookieCartId) {
        //         if (!is_numeric($cookieCartId)) {
        //             $cookieCartId = Crypt::decryptString($cookieCartId);
        //         }
        //         if ($cookieCartId) {
        //             static::$currentCartID = $cookieCartId;
        //         }
        //     }
        // }
    }

    public function makeCartExists()
    {
        if ($this->cart) return $this->cart;
        if (!static::$currentCartID) {
            if (!($user = auth()->user())) {
                $cookieCartId = Cookie::get('cart_id');
                $this->useCookie = true;
            } else {
                $cookieCartId = null;
                if ($cart = $this->first(['user_id' => $user->id])) {
                    $cookieCartId = $cart->id;
                    $this->cart = $cart;
                }
            }

            if ($cookieCartId) {
                if (!is_numeric($cookieCartId)) {
                    $cookieCartId = Crypt::decryptString($cookieCartId);
                }
                if ($cookieCartId) {
                    static::$currentCartID = $cookieCartId;
                }
            }
        }

        if (static::$currentCartID) {
            if (!$this->cart) {
                $cart = $this->first(['id' => static::$currentCartID]);
                $this->cart = $cart;
            }
            if (!$this->cart) {
                if ($user = auth()->user()) {
                    $cart = $this->create(['name' => $user->name, 'email' => $user->email, 'user_id' => $user->id]);
                } else {
                    $cart = $this->create(['name' => 'Customer', 'email' => 'example@gmail.com', 'user_id' => 0]);
                    $this->useCookie = true;
                }

                static::$currentCartID = $cart->id;
                $this->cart = $cart;
            }
            // cart
        } else {
            if ($user = auth()->user()) {
                $cart = $this->create(['name' => $user->name, 'email' => $user->email, 'user_id' => $user->id]);
            } else {
                $cart = $this->create(['name' => 'Customer', 'email' => 'example@gmail.com', 'user_id' => 0]);
                $this->useCookie = true;
            }

            static::$currentCartID = $cart->id;
            $this->cart = $cart;
        }
        return $this->cart;
    }

    /**
     * parse thuoc tinh
     *
     * @param Product $product
     * @param array $attrs
     * @return array<int>
     */
    public function parseAttrs(Product $product, $attrs = [])
    {
        $attributes = $product->getOrderOptionData();
        if ($attributes) {
            foreach ($attributes as $attributeID => $attribute) {
                $value_id = null;
                $isSelected = false;
                $i = 0;
                if ($attribute->values && is_array($attribute->values) && count($attribute->values)) {
                    foreach ($attribute->values as $vid => $attrValue) {
                        if (in_array($attrValue->value_id, $attrs)) {
                            $isSelected = true;
                        } elseif ($i == 0) {
                            $value_id = $attrValue->value_id;
                        } elseif ($attrValue->value_id) {
                            $value_id = $attrValue->value_id;
                        }

                        $i++;
                    }
                }
                if (!$isSelected) {
                    if ($value_id) {
                        $attrs[] = $value_id;
                    } elseif ($attribute->default) {
                        $attrs[] = $value_id;
                    }
                }
            }
        }
        return $attrs;
    }

    public $availableQuantityForCart = 0;

    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param int $product_id
     * @param int $quantity
     * @param array $attrs
     * @return Cart
     */
    public function checkAvailable($product_id, $quantity = 1, $attrs = [])
    {
        if ($product = $this->productRepository->detail($product_id)) {
            $this->makeCartExists();
            $cart_id = static::$currentCartID;
            if (!is_numeric($quantity) || $quantity < 1) $quantity = 1;
            $qty = $quantity;

            $totalQuantyty = $this->cartItemRepository->sum('quantity', ['product_id' => $product_id, 'cart_id' => $cart_id]);
            if ($totalQuantyty + $qty > $product->available_in_store) {
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                for ($i = $qty; $i >= 0; $i--) {
                    if ($totalQuantyty + $i <= $product->available_in_store) {
                        $this->availableQuantityForCart = $i;
                        break;
                    }
                }
                return false;
            } else {
                $this->availableQuantityForCart = $qty;
            }
            return true;
        }
        return false;
    }


    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param int $product_id
     * @param int $quantity
     * @param array $attrs
     * @return Cart
     */
    public function addItem($product_id, $quantity = 1, $attrs = [], $returnCartUpdated = true)
    {
        if ($product = $this->productRepository->detail($product_id)) {
            $this->makeCartExists();
            $cart_id = static::$currentCartID;
            if (!is_numeric($quantity) || $quantity < 1) $quantity = 1;
            // lấy ra các id hợp lệ và mã hóa mảng json

            $attrs = $this->parseAttrs($product, $attrs);
            $attr_values = $this->getAttrKey($product->id, $attrs);
            // tạo biến data để uy cấn và cập nhật data
            $data = compact('cart_id', 'product_id', 'attr_values');
            $qty = $quantity;
            if ($item = $this->cartItemRepository->first($data)) {
                $cartItem = $item;
                $cartItem->quantity += $quantity;
                $qty = $cartItem->quantity;
                $totalQuantyty = $this->cartItemRepository->where('id', '!=', $cartItem->id)->sum('quantity', ['product_id' => $product_id, 'cart_id' => $cart_id]);
            } else {
                $data['quantity'] = $quantity;

                $cartItem = new CartItem($data);
                $totalQuantyty = $this->cartItemRepository->sum('quantity', ['product_id' => $product_id, 'cart_id' => $cart_id]);
            }


            // echo "$totalQuantyty + $qty > $product->available_in_store"; die;
            if ($totalQuantyty + $qty > $product->available_in_store) {
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                return false;
            }
            $this->actionStatus = true;
            $cartItem->save();
            if (!$returnCartUpdated) return true;
            return $this->updateCartData()->getCartWidthDetails();
        }
        return false;
    }

    /**
     * thêm nhiều sản phẩm vào giỏ hàng
     *
     * @param array $items
     * @return Cart
     */

    public function addManyItem($items = [])
    {
        if (is_array($items)) {
            $success = 0;
            foreach ($items as $i => $item) {
                if (is_array($item) && array_key_exists('product_id', $item)) {
                    if ($this->addItem($item['product_id'], $item['quantity'] ?? 1, $item['attrs'] ?? [], false)) {
                        $success++;
                    }
                }
            }
            if ($success) {
                return $this->updateCartData()->getCartWidthDetails();
            }
        }
        return false;
    }

    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param int $product_id
     * @param int $quantity
     * @param array $attrs
     * @return Cart
     */
    public function updateItem($id, $quantity = null, $attrs = [])
    {
        $this->makeCartExists();
        $cart_id = static::$currentCartID;
        if (!$cart_id || !($item = $this->cartItemRepository->first(['cart_id' => $cart_id, 'id' => $id]))) return false;


        if ($product = $this->productRepository->findBy('id', $item->product_id)) {

            $attrs = $this->parseAttrs($product, $attrs);
            // lấy ra các id hợp lệ và mã hóa mảng json
            $attr_values = $this->getAttrKey($product->id, $attrs);
            // tạo biến data để uy cấn và cập nhật data
            $data = compact('attr_values');

            if (is_numeric($quantity) && $quantity > 0) {
                $data['quantity'] = $quantity;
                $totalQuantyty = $this->cartItemRepository->where('id', '!=', $item->id)->sum('quantity', ['product_id' => $product->id, 'cart_id' => $cart_id]);

                if ($totalQuantyty + $quantity > $product->available_in_store) {
                    $this->actionMessage = "Sản phẩm tạm hết hàng";
                    return false;
                }
            }

            $this->cartItemRepository->update($id, $data);
            return $this->updateCartData()->getCartWidthDetails();
        }
        return false;
    }



    public function removeItem(int $id = 0)
    {
        if ($id) {
            $this->makeCartExists();
            if ($item = $this->cartItemRepository->first(['cart_id' => $this->cart->id, 'id' => $id])) {
                $item->delete();
            }
            return $this->updateCartData()->getCartWidthDetails();
        }
        return false;
    }

    public function updateCartQuantity($quantityData = [])
    {

        $this->needDeleteItemStatus = false;
        $this->needDeleteItemList = [];
        $s = false;
        $this->actionStatus = true;
        if (is_array($quantityData)) {
            $this->makeCartExists();
            $this->actionMessage = '';
            foreach ($quantityData as $id => $qty) {
                if ($qty < 1) {
                    $this->cartItemRepository->delete($id);
                    $s = true;
                } elseif (
                    !($item = $this->cartItemRepository->find($id)) ||
                    !($product = $this->productRepository->detail($item->product_id))
                ) {
                    $this->actionStatus = false;
                } elseif (($inCartTotal = $this->cartItemRepository->where('id', '!=', $item->id)->sum('quantity', ['product_id' => $product->id, 'cart_id' => $item->cart_id])) < 0) {
                    $this->actionMessage = "Sản phẩm không hợp lệ";
                    $this->actionStatus = false;
                } elseif ($inCartTotal + $qty > $product->available_in_store) {

                    $this->actionMessage = "Số lượng " . (isset($product) && $product ? $product->name : 'sản phẩm') . " vượt quá số lượng trong kho có thể đặt hàng";
                    $this->actionStatus = false;
                    while ($inCartTotal + $qty > $product->available_in_store) {
                        $qty--;
                    }
                    if ($qty > 0 && $this->cartItemRepository->update($id, ['quantity' => $qty])) {
                        // update va lam gi do
                    }else{
                        $this->needDeleteItemStatus = true;
                        $this->needDeleteItemList[] = $id;
                        $this->cartItemRepository->delete($id);
                    }
                } elseif ($this->cartItemRepository->update($id, ['quantity' => $qty])) {
                    $s = true;
                }
            }
        }
        if ($s) {
            // $this->actionStatus = true;
            return $this->updateCartData()->getCartWidthDetails();
        }
        return false;
    }


    public function checkCartData()
    {
        $this->makeCartExists();
        if ($this->cart) {
            $this->needDeleteItemStatus = false;
            $this->needDeleteItemList = [];
            if (count($items = $this->cartItemRepository->with('product')->get(['cart_id' => $this->cart->id]))) {

                foreach ($items as $item) {
                    $id = $item->id;
                    if(!($product = $item->product) || !($qty = $item->quantity)){
                        $this->needDeleteItemStatus = true;
                        $this->needDeleteItemList[] = $id;
                        $this->cartItemRepository->delete($id);
                    }
                    if (($inCartTotal = $this->cartItemRepository->where('id', '!=', $item->id)->sum('quantity', ['product_id' => $product->id, 'cart_id' => $item->cart_id])) < 0) {
                        $this->actionMessage = "Sản phẩm không hợp lệ";
                        $this->actionStatus = false;
                    } elseif ($inCartTotal + $qty > $product->available_in_store) {
    
                        $this->actionMessage = "Có sản phẩm trong giỏ hiện đã hết hạn! Hãy kiểm tra lại giỏ hàng";
                        $this->actionStatus = false;
                        while ($inCartTotal + $qty > $product->available_in_store) {
                            $qty--;
                        }
                        if ($qty > 0 && $this->cartItemRepository->update($id, ['quantity' => $qty])) {
                            // update va lam gi do
                        }else{
                            $this->needDeleteItemStatus = true;
                            $this->needDeleteItemList[] = $id;
                            $this->cartItemRepository->delete($id);
                        }
                    } 
                }
            }
            return $this->updateCartData()->getCartWidthDetails();
     
        }
        return false;
    }
    public function getCartData()
    {
        return $this->updateCartData()->getCartWidthDetails();
    }
    /**
     * lấy chi tiết giỏ hàng
     *
     * @return \App\Masks\Orders\CartMask|null
     */
    public function getCartWidthDetails()
    {
        if ($this->cart) return $this->parseDetail($this->with('details')->first(['id' => $this->cart->id]));
        return static::$currentCartID ? $this->parseDetail($this->with('details')->first(['id' => static::$currentCartID])) : null;
    }

    public function getOrderCart()
    {
        $this->makeCartExists();
        return static::$currentCartID ? $this->with('items')->first(['id' => static::$currentCartID]) : null;
    }

    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param int $product_id
     * @param int $quantity
     * @param array $attrs
     * @return Cart
     */
    public function addItemBuyNow($product_id, $quantity = 1, $attrs = [], $returnCartUpdated = true)
    {
        if ($product = $this->productRepository->findBy('id', $product_id)) {
            if (!$this->cart) {
                $cart = $this->create([]);

                $cart_id = $cart->id;
                static::$currentCartID = $cart_id;
                $this->cart = $cart;
            } else {
                $cart_id = $this->cart->id;
            }

            if (!is_numeric($quantity) || $quantity < 1) $quantity = 1;
            // lấy ra các id hợp lệ và mã hóa mảng json
            $attr_values = $this->getAttrKey($product->id, $attrs);
            // tạo biến data để uy cấn và cập nhật data
            $data = compact('cart_id', 'product_id', 'attr_values');
            $qty = $quantity;
            if ($item = $this->cartItemRepository->first($data)) {
                $cartItem = $item;
                $cartItem->quantity += $quantity;
                $qty = $cartItem->quantity;
                $totalQuantyty = $this->cartItemRepository->where('id', '!=', $cartItem->id)->sum('quantity', ['product_id' => $product_id, 'cart_id' => $cart_id]);
            } else {
                $data['quantity'] = $quantity;

                $cartItem = new CartItem($data);
                $totalQuantyty = $this->cartItemRepository->sum('quantity', ['product_id' => $product_id, 'cart_id' => $cart_id]);
            }

            $totalQuantyty = $this->cartItemRepository->sum('quantity', ['product_id' => $product_id, 'cart_id' => $cart_id]);
            // dd($totalQuantyty);
            if ($totalQuantyty + $qty > $product->available_in_store) {
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                return false;
            }
            $this->actionStatus = true;
            $cartItem->save();
            if (!$returnCartUpdated) return true;
            return $this->updateCartData()->getCartWidthDetails();
        }
        return false;
    }


    public function addItemBySetCombo($set_id)
    {
        /**
         * @var StyleSetItemRepository
         */
        $setItemRepository = app(StyleSetItemRepository::class);
        if (!$set_id || !count($setItems = $setItemRepository->get(['style_set_id' => $set_id]))) {
            $this->actionMessage = "Style Set không tồn tại";
            return null;
        }

        $this->makeCartExists();
        foreach ($setItems as $item) {
            if (!$this->addItem($item->product_id, $item->quantity, explode('-', $item->attr_values), false)) {
                $this->actionStatus = false;
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                return false;
            }
        }
        $this->actionStatus = true;
        return $this->updateCartData()->getCartWidthDetails();
    }

    public function buyNowSetCombo($set_id)
    {
        /**
         * @var StyleSetItemRepository
         */
        $setItemRepository = app(StyleSetItemRepository::class);
        if (!$set_id || !count($setItems = $setItemRepository->get(['style_set_id' => $set_id]))) {
            $this->actionMessage = "Style Set không tồn tại";
            return null;
        }

        $this->makeCartExists();
        foreach ($setItems as $item) {
            if (!$this->addItemBuyNow($item->product_id, $item->quantity, explode('-', $item->attr_values), false)) {
                $this->actionStatus = false;
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                return false;
            };
        }

        return $this->updateCartData()->getCartWidthDetails();
    }




    public function getBuyNowCart(Request $request)
    {
        $buyNowCartID = $request->buy_now_cart_id ?? $request->session()->get('buy_now_cart_id');
        return $buyNowCartID ? $this->parseDetail($this->with('details')->first(['id' => $buyNowCartID])) : null;
    }

    public function setBuyNowCart(Request $request)
    {
        $buyNowCartID = $request->buy_now_cart_id ?? $request->session()->get('buy_now_cart_id');
        if ($buyNowCartID) {
            static::$currentCartID = $buyNowCartID;
            $this->cart = $this->first(['id' => $buyNowCartID]);
        } else {
            $user_id = 0;
            if ($user = auth()->user()) {
                $user_id = $user->id;
            }
            $this->cart = $this->create(['name' => 'Customer', 'email' => 'example@gmail.com', 'user_id' => $user_id]);
            session(['buy_now_cart_id' => $this->cart->id]);
        }

        return $this->cart;
    }

    /**
     * làm trống giỏ hảng
     *
     * @return \App\Masks\Orders\CartMask|null
     */
    public function empty()
    {
        if (static::$currentCartID && $this->makeCartExists() && $this->cart) {
            $this->cart->items()->delete();

            return $this->updateCartData()->getCartWidthDetails();
        }
        return false;
    }


    protected function updateCartData()
    {
        if ($this->cart) {
            $sub_total = 0;
            $total_money = 0;
            if (count($items = $this->cartItemRepository->mode('mask')->with('product')->getData(['cart_id' => $this->cart->id]))) {

                foreach ($items as $item) {
                    $sub_total += $item->total_price;
                }
            }
            // thiết lập thêm tax hoặc gì đó
            $total_money = $sub_total;
            $this->cart = $this->update($this->cart->id, compact('sub_total', 'total_money'));
        }
        return $this;
    }

    /**
     * lấy key của cart item
     *
     * @param integer $product_id
     * @param array $attrs
     * @return string
     */
    public function getAttrKey(int $product_id, array $attrs = [])
    {
        $arr = [];
        if ($attrs && $attrVals = $this->productAttributeRepository->getProductAttributeValues($product_id, $attrs, 1)) {
            foreach ($attrVals as $attrVal) {
                $arr[] = $attrVal->attribute_value_id;
            }
        }
        sort($arr);

        return implode('-', $arr);
    }


    /**
     * lấy thông tin giá bán dự tên giá và thông tin đầu vào
     *
     * @param integer $origin_price
     * @param integer $product_id
     * @param array $attrs
     * @return void
     */
    public function getProductPriceByOrigin($origin_price = 0, $product_id = 0, $attrs = [])
    {
        if (!$this->currentAttrs) {
            $this->currentAttrs = $this->productAttributeRepository->getProductAttributeValues($product_id, $attrs, 1, 1);
        }
        $price = $origin_price;
        if ($attrs) {
            $change = 0;
            if (count($this->currentAttrs)) {
                foreach ($this->currentAttrs as $key => $attr) {
                    if ($attr->price_type) {
                        if (!$change) {
                            $price = $attr->price;
                            $change = 1;
                        }
                    } else {
                        $price += $attr->price;
                    }
                }
            }
        }
        return $price;
    }

    public function getCartDetail($id)
    {
        return $this->with(['details'])->mode('mask')->detail($id);
    }


    public function setCurrentCartID($id = null)
    {
        if (is_numeric($id)) static::$currentCartID = $id;
    }

    public static function setCartID($id = null)
    {
        if (is_numeric($id)) static::$currentCartID = $id;
    }

    public function getCurrentCartID()
    {
        return static::$currentCartID;
    }



    public static function checkCartID(Request $request)
    {
        if (is_numeric($id = $request->cookie('cart_id'))) {
            static::$currentCartID = $id;
        }
    }


    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param Request $request
     * @return Order
     */
    public function checkPromo(Request $request)
    {

        $user_id = ($user = $request->user()) ? $user->id : null;
        if (!($promo = $this->promoRepository->checkPromo($request->coupon, $user_id))) {
            $this->actionMessage = $this->promoRepository->actionMessage;
            return false;
        } else {
            $this->makeCartExists();
            $this->cart->update();
            $this->actionStatus = true;
            $cart = $this->updateCartData()->getCartWidthDetails();
            $cart->prono_type = $promo->type;
            switch ($promo->type) {
                case Promo::TYPE_DOWN_PERCENT:
                    $cart->promo_total = $promo->down_price * $cart->total_money / 100;
                    break;

                case Promo::TYPE_DOWN_PRICE:
                    $cart->promo_total = $promo->down_price > $cart->total_money ? $cart->total_money : $promo->down_price;
                    break;

                default:
                    # code...
                    break;
            }
            return $cart;
        }


        return false;
    }


    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param Request $request
     * @param Cart $cart
     * @return Cart
     */
    public function applyCouponToCart(Request $request, $cart)
    {

        $user_id = ($user = $request->user()) ? $user->id : null;
        if (!($promo = $this->promoRepository->checkPromo($request->coupon, $user_id))) {
            $this->actionMessage = $this->promoRepository->actionMessage;
            return false;
        } else {
            $this->actionStatus = true;
            $cart->prono_type = $promo->type;
            switch ($promo->type) {
                case Promo::TYPE_DOWN_PERCENT:
                    $cart->promo_total = $promo->down_price * $cart->total_money;
                    break;

                case Promo::TYPE_DOWN_PRICE:
                    $cart->promo_total = $promo->down_price > $cart->total_money ? $cart->total_money : $promo->down_price;
                    break;

                default:
                    # code...
                    break;
            }
            return $cart;
        }


        return false;
    }
}
