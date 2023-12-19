<?php

namespace App\Http\Controllers\Web\eCommerce;

use App\Http\Controllers\Web\WebController;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Categories\CategoryRefRepository;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Orders\CartRepository;
use App\Repositories\Orders\OrderAddressRepository;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Promos\PromoRepository;
use App\Repositories\Users\UserRepository;
use App\Validators\Orders\PlaceValidator;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use Gomee\Mailer\Email;

/**
 * @property CartRepository $repository
 * @property PromoRepository $promoRepository
 */
class CartController extends WebController
{
    protected $module = 'orders';

    protected $moduleName = 'Giỏ hàng';

    protected $flashMode = true;


    /**
     * customer
     *
     * @var \App\Repositories\Customers\CustomerRepository $customerRepository
     */
    protected $customerRepository = null;
    /**
     * customer
     *
     * @var \App\Repositories\Users\UserRepository $userRepository
     */
    protected $userRepository = null;

    /**
     * email token
     *
     * @var \App\Repositories\Emails\EmailTokenRepository
     */
    protected $emailTokenRepository = null;

    /**
     * quản lý địa chỉ của order
     *
     * @var \App\Repositories\Orders\OrderAddressRepository
     */
    protected $orderAddressRepository = null;

    /**
     * Cart Repository
     *
     * @var CartRepository
     */
    public $repository = null;

    /**
     * order repository
     *
     * @var \App\Repositories\Orders\OrderRepository
     */
    public $orderRepository = null;

    /**
     * payment method
     *
     * @var MethodRepository
     */
    public $paymentMethodRepository = null;
    /**
     * payment method
     *
     * @var ProductRepository
     */
    public $productRepository = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        CartRepository $CartRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        EmailTokenRepository $emailTokenRepository,
        OrderAddressRepository $orderAddressRepository,
        MethodRepository $paymentMethodRepository,
        PromoRepository $promoRepository
    )
    {
        $this->repository = $CartRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository->notTrashed();
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository->notTrashed();
        $this->emailTokenRepository = $emailTokenRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->paymentMethodRepository = $paymentMethodRepository->addDefaultCondition('status', 'status', 1)->notTrashed();
        $this->promoRepository = $promoRepository->notTrashed()->mode('mask');
        $this->setting = setting();
        $this->siteinfo = siteinfo();
        $this->init();
    }

    /**
     * xem giỏ hàng
     *
     * @param Request $request
     * @return View
     */
    public function viewCart(Request $request)
    {
        $cart = $this->repository->getCartWidthDetails();
        $page_title = "Giỏ hàng";
        $this->breadcrumb->add($page_title, route('web.orders.cart'));
        $customer = $this->customerRepository->getCurrentCustomerOrUser($request);
        $user = $request->user();
        $coupons = $user?$this->promoRepository->getUserAvailablePromos($user->id): [];
        set_web_data('__cart__form__config', $this->getJsonInputs('orders.checkout'));
        set_web_data('__cart__form__data', $customer?$customer->toArray():[]);
        return $this->viewModule('cart', compact('cart', "page_title", 'customer', 'coupons'));
    }


    /**
     * xem giỏ hàng
     *
     * @param Request $request
     * @return View
     */
    public function viewBuyNowCart(Request $request)
    {
        $cart = $this->repository->getBuyNowCart($request);
        if(!$cart) return redirect()->route('web.alert')->with('info', 'Hãy chọn một sản phảm bạn thích để thêm giỏ hàng');
        $page_title = "Mua ngay";
        $cart->type = 'buy-now';
        $this->breadcrumb->add($page_title);
        $customer = $this->customerRepository->getCurrentCustomerOrUser($request);
        $this->breadcrumb->add($page_title);
        $user = $request->user();
        $coupons = $user?$this->promoRepository->getUserAvailablePromos($user->id): [];
        set_web_data('__cart__form__config', $this->getJsonInputs('orders.checkout'));
        set_web_data('__cart__form__data', $customer?$customer->toArray():[]);
        return $this->viewModule('cart', compact('cart', "page_title", 'customer', 'coupons'));
    }



    /**
     * xem trang thanh toán
     *
     * @param Request $request
     * @return View
     */
    public function checkout(Request $request)
    {
        $cart = $this->repository->getCartWidthDetails();
        $page_title = "Đặt hàng và thanh toán";
        $customer = $this->customerRepository->getCurrentCustomerOrUser($request);
        $this->breadcrumb->add($page_title);
        set_web_data('__cart__form__config', $this->getJsonInputs('orders.checkout'));
        set_web_data('__cart__form__data', $customer?$customer->toArray():[]);
        return $this->viewModule('checkout', compact('cart', "page_title", 'customer'));
    }




    /**
     * kiểm tra giá sản phẩm theo thuộc tính nếu có
     *
     * @param Request $request
     * @return void
     */
    public function checkPrice(Request $request)
    {
        // return $request->all();
        extract($this->apiDefaultData);
        if($productData = $this->productRepository->checkPrice($request->product_id, is_array($request->attrs)?$request->attrs:[])){
            $status = true;
            $price = $productData['price'];
            $data = [
                'product' => $productData['product'],
                'price' => $price,
                'price_format' => get_currency_format($price),
                'available' => $this->repository->checkAvailable($request->product_id, $request->quantity??1, is_array($request->attrs)?$request->attrs:[])
            ];
        }

        return $this->json(compact($this->apiSystemVars));
    }

    /**
     * tinhn1 năng này sẻ làm sau
     *
     * @param Request $request
     * @return void
     */
    public function addToCart(Request $request)
    {
        return redirect()->back()->withInput($request->all());
    }

    /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function addItem(Request $request)
    {
        extract($this->apiDefaultData);
        if($cart = $this->repository->addItem($request->product_id, $request->quantity, $request->attrs??[])){
            $status = true;
            $data = $cart;
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }

        $message = 'Lỗi không xác định';
        if(!$this->repository->actionStatus){
            $message = $this->repository->actionMessage;
        }
        return $this->json(compact($this->apiSystemVars));

    }



    /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function addManyItem(Request $request)
    {
        extract($this->apiDefaultData);
        if($cart = $this->repository->addManyItem($request->items, $request->quantity, $request->attrs??[])){
            $status = true;
            $data = $cart;
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }

        $message = 'Lỗi không xác định';
        if(!$this->repository->actionStatus){
            $message = $this->repository->actionMessage;
        }
        return $this->json(compact($this->apiSystemVars));

    }


    /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function addSetCombo(Request $request)
    {
        extract($this->apiDefaultData);

        if($cart = $this->repository->addItemBySetCombo($request->combo_id)){
            $status = true;
            $data = $cart;
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));

        }

        $message = 'Lỗi không xác định';
        if(!$this->repository->actionStatus){
            $message = $this->repository->actionMessage;
        }
        return $this->json(compact($this->apiSystemVars));

    }

    /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function addItemBuyNow(Request $request)
    {
        extract($this->apiDefaultData);
        $this->repository->setBuyNowCart($request);
        if($cart = $this->repository->addItemBuyNow($request->product_id, $request->quantity, $request->attrs??[])){
            $status = true;
            $data = $cart;
            session(['buy_now_cart_id' => $cart->id]);
            return $this->json(compact($this->apiSystemVars));
        }

        $message = 'Lỗi không xác định';
        if(!$this->repository->actionStatus){
            $message = $this->repository->actionMessage;
        }
        return $this->json(compact($this->apiSystemVars));

    }


    /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function buyNowSetCombo(Request $request)
    {
        extract($this->apiDefaultData);
        $this->repository->setBuyNowCart($request);
        if($cart = $this->repository->buyNowSetCombo($request->combo_id)){
            $status = true;
            $data = $cart;
            session(['buy_now_cart_id' => $cart->id]);
            return $this->json(compact($this->apiSystemVars));
        }

        $message = 'Lỗi không xác định';
        if(!$this->repository->actionStatus){
            $message = $this->repository->actionMessage;
        }
        return $this->json(compact($this->apiSystemVars));

    }


    public function updateItem(Request $request)
    {
        extract($this->apiDefaultData);
        $buy_now_cart_id = session('buy_now_cart_id');
        if($request->cart_type == 'buy-now' && $buy_now_cart_id){
            $this->repository->setBuyNowCart($request);
        }
        if($cart = $this->repository->updateItem($request->item_id, $request->quantity, $request->attrs??[])){
            $status = true;
            if($couponCart = $this->repository->applyCouponToCart($request, $cart)){
                $cart = $couponCart;
            }
            else{
                $message = $this->repository->actionMessage;
            }
            $data = $cart;
            if($buy_now_cart_id) return $this->json(compact($this->apiSystemVars));
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }

        $message = 'Lỗi không xác định';
        return $this->json(compact($this->apiSystemVars));
    }

        /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function applyCoupon(Request $request)
    {
        extract($this->apiDefaultData);
        $buy_now_cart_id = session('buy_now_cart_id');
        if($request->cart_type == 'buy-now' && $buy_now_cart_id){
            $this->repository->setBuyNowCart($request);
        }

        if($cart = $this->repository->checkPromo($request)){
            $status = true;
            $data = $cart;
            if($buy_now_cart_id) return $this->json(compact($this->apiSystemVars));
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }

        $message = $this->repository->actionMessage;
        return $this->json(compact($this->apiSystemVars));

    }
    /**
     * xóa item trong giỏ hàng
     *
     * @param Request $request
     */
    public function removeItem(Request $request)
    {
        extract($this->apiDefaultData);
        $buy_now_cart_id = session('buy_now_cart_id');
        if($request->cart_type == 'buy-now' && $buy_now_cart_id){
            $this->repository->setBuyNowCart($request);
        }
        if($cart = $this->repository->removeItem($request->id)){
            $status = true;
            if($couponCart = $this->repository->applyCouponToCart($request, $cart)){
                $cart = $couponCart;
            }
            else{
                $message = $this->repository->actionMessage;
            }
            $data = $cart;
            if($buy_now_cart_id) return $this->json(compact($this->apiSystemVars));
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }
        $message = 'Lỗi không xác định';
        return $this->json(compact($this->apiSystemVars));
    }
    /**
     * thêm giỏ hàng bằng ajax
     * @param Request $request
     */
    public function checkData(Request $request)
    {
        extract($this->apiDefaultData);
        if($cart = $this->repository->checkCartData()){
            $status = true;
            $data = $cart;
            $data->has_deleted_item = $this->repository->needDeleteItemStatus;
            $data->deteled_item_list = $this->repository->needDeleteItemList;
        }else{
            $message = 'Không có gì trong giỏ';
        }

        return $this->json(compact($this->apiSystemVars));

    }


    /**
     * làm trống giỏ hàng
     *
     * @param Request $request
     */
    public function empty(Request $request)
    {
        extract($this->apiDefaultData);
        $status = true;
        if($cart = $this->repository->empty()){
            $data = $cart;
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }
        else{
            $data = null;
            $message = 'Lỗi không xác định';
            return $this->json(compact($this->apiSystemVars));
        }
    }

    /**
     * cap nhat quan tity
     *
     * @param Request $request
     */
    public function updateCartQuantity(Request $request)
    {
        extract($this->apiDefaultData);
        // return die(json_encode($request->quantity));
        $buy_now_cart_id = session('buy_now_cart_id');
        if($request->cart_type == 'buy-now' && $buy_now_cart_id){
            $this->repository->setBuyNowCart($request);
        }

        if($request->quantity && $cart = $this->repository->updateCartQuantity($request->quantity)){
            $status = true;
            $message = $this->repository->actionMessage;
            if(!$this->repository->actionStatus) $status = false;
            elseif($request->coupon){
                if($couponCart = $this->repository->applyCouponToCart($request, $cart)){
                    $cart = $couponCart;
                }
                else{
                    $message = $this->repository->actionMessage;
                }

            }

            $data = $cart;

            $data->has_deleted_item = $this->repository->needDeleteItemStatus;
            $data->deteled_item_list = $this->repository->needDeleteItemList;
            if($buy_now_cart_id) return $this->json(compact($this->apiSystemVars));
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));

        }else{
            $data = $this->repository->getCartData();
            $message = $this->repository->actionMessage?$this->repository->actionMessage:'Lỗi không xác định';

            $data->has_deleted_item = $this->repository->needDeleteItemStatus;
            $data->deteled_item_list = $this->repository->needDeleteItemList;
            if($this->repository->useCookie){
                return $this->json(compact($this->apiSystemVars))->withCookie(cookie('cart_id', $cart->id, 365*24*60));
            }
            return $this->json(compact($this->apiSystemVars));
        }


    }



}
