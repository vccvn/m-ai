<?php

namespace App\Repositories\Orders;

use App\Masks\Orders\OrderCollection;
use App\Masks\Orders\OrderMask;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Promo;
use App\Repositories\Products\ProductAttributeRepository;
use Gomee\Repositories\BaseRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Promos\PromoRepository;
use App\Repositories\Users\UserDiscountRepository;
use App\Services\Mailers\Mailer;
use App\Validators\Orders\OrderValidator;
use Gomee\Mailer\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Repository của đơn hàng
 * @method Order|\App\Masks\Orders\OrderMask detail(int|array $args) lấy về chi tiết đơn hàng
 * @method Order[]|\App\Masks\Orders\OrderCollection getResults(array $args) lấy danh sách orders
 * @method Order[]|\App\Masks\Orders\OrderCollection filter(Request $requuest, array $args) lấy danh sách orders
 *
 */
class OrderRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = OrderValidator::class;

    protected $maskClass = OrderMask::class;
    protected $maskCollectionClass = OrderCollection::class;

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
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

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
    /**
     * @var array $defaultSortBy Mảng key value là twen6 cộ và kiểu sắp xếp
     */
    protected $defaultSortBy = [
        'orders.id' => 'DESC'
    ];



    protected $currentAttrs = [];


    public $actionStatus = false;
    public $actionMessage = "Thao tác thành công";

    protected $isJoindBilling = false;
    protected $isJoindShipping = false;

    /**
     * get model
     * @return string tên class model
     */
    public function getModel()
    {
        return \App\Models\Order::class;
    }
    public function init()
    {
        $this->addDefaultParam('type', 'type', 'order');
        $this->addDefaultValue('type', 'order');

        $this->productAttributeRepository = app(ProductAttributeRepository::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productRepository->notTrashed();
        $this->orderItemRepository = app(OrderItemRepository::class);
        $this->promoRepository = app(PromoRepository::class);
        $this->userDiscountRepository = app(UserDiscountRepository::class);
        $this->ignoreValues['status'] = ['all'];
    }

    public function afterCreate(Order $result)
    {
        //

        $prefix = 'SP';
        // if($result->shipping && $result->shipping->region_id){
        //     $prefix .= str_pad($result->shipping->region_id, 2, '0', STR_PAD_LEFT);
        // }else{
        //     $prefix.= '00';
        // }
        if ($result->paymentMethod) {
            $prefix .= strtoupper(substr($result->paymentMethod->method, 0, 2));
        } else {
            $prefix .= 'AA';
        }

        $result->code = $prefix . str_pad($result->id, 6, "0", STR_PAD_LEFT);
        $result->save();
    }

    public function afterUpdate(Order $result)
    {
        if (!$result->code) {
            $this->afterCreate($result);
        }
    }

    /**
     * import
     *
     * @param Cart $cart
     * @param int $order_id
     * @return array<int, array<string, int>>
     */
    public function importFromCart($cart, $order_id)
    {

        if ($cart->items) {
            $products = [];
            $needRollback = false;
            foreach ($cart->items as $item) {
                $attr_values = explode('-', $item->attr_values);
                if ($product = $this->addItem($order_id, $item->product_id, $item->quantity, $attr_values)) {
                    $product->available_in_store -= $item->quantity;
                    $product->save();
                    $products[] = ['product_id' => $item->product_id, 'quantity' => $item->quantity];
                } else {
                    $needRollback = true;
                    break;
                }
            }
            if ($needRollback) {
                foreach ($products as $data) {
                    if ($product = $this->productRepository->find($data['product_id'])) {
                        $product->available_in_store += $data['quantity'];
                        $product->save();
                    }
                }
                return [];
            }
            return $products;
        }
        return [];
    }


    /**
     * thêm sản phẩm vào giỏ hàng
     *
     * @param int $product_id
     * @param int $quantity
     * @param array $attrs
     * @return Order
     */
    public function addItem($order_id, $product_id, $quantity = 1, $attrs = [])
    {
        /**
         * @var Product
         */
        if ($product = $this->productRepository->findBy('id', $product_id)) {
            // $order_id = static::$currentCartID;
            if (!is_numeric($quantity) || $quantity < 1) $quantity = 1;
            if ($quantity > $product->available_in_store) {
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                return false;
            }

            // lấy ra các id hợp lệ và mã hóa mảng json
            $attr_values = $this->getAttrKey($product->id, $attrs);
            // tạo biến data để uy cấn và cập nhật data
            $data = compact('order_id', 'product_id', 'attr_values');
            $data['quantity'] = $quantity;

            $this->currentAttrs = [];
            $data['list_price'] = $this->getProductPriceByOrigin($product->list_price, $product_id, $attrs);
            $data['final_price'] = $product->getFinalPrice($this->getProductPriceByOrigin($product->on_sale ? $product->sale_price : $product->list_price, $product_id, $attrs));
            $cartItem = $this->orderItemRepository->create($data);
            $this->currentAttrs = [];
            return $product;
        }
        $this->currentAttrs = [];
        return false;
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


    /**
     * upadte
     *
     * @param Order $order_id
     * @return $this
     */
    public function updateOrderData($order)
    {
        $sub_total = 0;
        $total_money = 0;
        $shipping_fee = 0;
        $promo_type = 0;
        $promo_total = 0;
        if (count($items = $this->orderItemRepository->getBy('order_id', $order->id))) {
            foreach ($items as $item) {
                $sub_total += ($item->final_price * $item->quantity);
            }
        }
        // thiết lập thêm tax hoặc gì đó
        $tax = 0;
        $total_money = $sub_total;
        if ($order->coupon && $promo = $this->promoRepository->checkPromo($order->coupon, $order->user_id ? $order->user_id : (($user = auth()->user()) ? $user->id : 0))) {
            if ($promo->type == Promo::TYPE_DOWN_PRICE) {
                $promo_total = $promo->down_price <= $total_money ? $promo->down_price : $total_money;
                $total_money -= $promo->down_price;
                if ($total_money < 0) $total_money = 0;
            } elseif ($promo->type == Promo::TYPE_DOWN_PERCENT) {
                $down = $promo->down_price * $total_money / 100;
                $promo_total = $down <= $total_money ? $down : $total_money;
                $total_money -= $down;
                if ($total_money < 0) $total_money = 0;
            } else {
                $shipping_fee = 0;
            }
        }





        $this->update($order->id, compact('sub_total', 'promo_total', 'total_money', 'tax', 'shipping_fee'));
        return $this;
    }

    public function withData()
    {
        return $this->with(['details', 'billing', 'shipping', 'paymentMethod']);
    }


    public function withFullData()
    {
        return $this->with([
            'details',
            'billing' => function ($query) {
                $query->with(['region', 'district', 'ward']);
            },
            'shipping' => function ($query) {
                $query->with(['region', 'district', 'ward']);
            }
        ]);
    }

    public function joinBilling()
    {
        $this->isJoindBilling = true;
        return $this->join('order_addresses as billings', function ($join) {
            $join->on('billings.order_id', '=', 'orders.id');
            $join->where('billings.type', '=', 'billing');
        });
    }

    public function joinShipping()
    {
        $this->isJoindShipping = true;
        return $this->join('order_addresses as shippings', function ($join) {
            $join->on('shippings.order_id', '=', 'orders.id');
            $join->where('shippings.type', '=', 'shipping');
        });
    }


    /**
     * lười nghĩ tên hàm
     *
     * @return void
     */
    public function setupX()
    {
        $columns = [
            'billing_name'                        => 'billings.name',
            'billing_email'                       => 'billings.email',
            'billing_phone_number'                => 'billings.phone_number',
            'billing_address'                     => 'billings.address',
            'billing_region_id'                   => 'billings.region_id',
            'billing_district_id'                 => 'billings.district_id',
            'billing_ward_id'                     => 'billings.ward_id',

            'shipping_name'                       => 'shippings.name',
            'shipping_email'                      => 'shippings.email',
            'shipping_phone_number'               => 'shippings.phone_number',
            'shipping_address'                    => 'shippings.address',
            'shipping_region_id'                  => 'shippings.region_id',
            'shipping_district_id'                => 'shippings.district_id',
            'shipping_ward_id'                    => 'shippings.ward_id',

            'payment_id'                          => 'payment_methods.id'
        ];
        $this->joinBilling()->joinShipping();
        $this->join('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id');
        $this->setWhereable($columns)->setSearchable($columns);
        $this->setSelectable(array_merge(['orders.*'], $columns));
    }

    /**
     * kiểm tra daterange trước khi filter
     * @param Request $request
     */
    public function beforeFilter($request)
    {
        // nếu có date range và date range hợp lệ thì sẽ thêm vào query
        if ($request->daterange && $date = get_date_range($request->daterange)) {
            $from = $date['from'];
            $to = $date['to'];
            $this->whereDate('orders.created_at', '>=', "$from[year]-$from[month]-$from[day]");
            $this->whereDate('orders.created_at', '<=', "$to[year]-$to[month]-$to[day]");
        }

        $this->setupX();
    }


    public function beforeGetFormData($args = [])
    {
        $this->setupX();
    }

    /**
     * lấy thông tin đơn hàng nằng thông tin đặt hàng
     * @param string $contact
     * @param int $order_id
     * @return Order
     */

    public function checkOrderByContact($contact, $order_code = '')
    {
        $this->joinBilling()
            ->where(function ($query) use ($contact) {
                $query->where('billings.email', $contact)->orWhere('billings.phone_number', $contact);
            });
        if ($order_code) {
            $this->where('orders.code', $order_code);
        }
        $this->where('payment_status', Order::PAYMENT_PENDING);
        return $this->select('orders.*')->withData()->first();
    }


    /**
     * get Product option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getSelectOptions($request, array $args = [], $valueBy = 'id')
    {
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('orders.id', $request->ignore);
        }
        $data = [];
        if ($list = $this->getFilter($request, $args)) {
            foreach ($list as $item) {
                $data[$item->{$valueBy}] = htmlentities('Đơn hàng #' . $item->{$valueBy});
            }
        }
        return $data;
    }

    public function getOrderSelectOption($args = [], $valueBy = 'id')
    {
        $data = [];
        if ($list = $this->get($args)) {
            foreach ($list as $item) {
                $data[$item->{$valueBy}] = htmlentities('Đơn hàng #' . $item->{$valueBy});
            }
        }
        return $data;
    }

    public function getOptionForTransaction($args = [])
    {
        $args = array_merge([
            // 'payment_status' => Order::PAYMENT_PENDING
        ], $args);
        return $this->notTrashed()->getOrderSelectOption($args, 'code');
    }

    /**
     * cập nhật trạng thái
     *
     * @param integer $id
     * @param integer $statusUpdate
     * @param integer $statusFind
     * @return \App\Models\Order|null
     */
    public function updateStatus($id, int $statusUpdate = 0, $statusFind = null)
    {
        if (!array_key_exists($statusUpdate, $this->statusList)) {
            return null;
        }
        $args = ['id' => $id];
        if (!is_null($statusFind)) {
            $args['status'] = $statusFind;
        }
        if ($id && $this->count($args) && $detail = $this->update($id, ['status' => $statusUpdate])) {
            return $detail;
        }
        return null;
    }


    /**
     * cập nhật trạng thái
     *
     * @param integer $id
     * @param integer $status
     * @return \App\Models\Order|null
     */
    public function updateStatusOnly($id, int $status = 0)
    {
        if (!array_key_exists($status, Order::getStatusLabels())) {
            return null;
        }
        if ($id && ($old = $this->getOrderWithItems($id)) && ($detail = $this->update($id, ['status' => $status]))) {
            if ($status == Order::CANCELED) {
                if ($old->delivery_status == Order::DELIVERY_PENDING) {
                    $this->restoreProductInStoreAvailable($old);
                }
            }
            elseif($old->status == Order::CANCELED){
                if ($old->delivery_status == Order::DELIVERY_PENDING) {
                    $this->restoreProductInStoreAvailable($old, 'dec');
                }
            }
            return $detail;
        }
        return null;
    }

    /**
     * cập nhật trạng thái
     *
     * @param integer $id
     * @param integer $status
     * @return \App\Models\Order|null
     */
    public function updatePaymentStatusOnly($id, int $status = 0)
    {
        if (!array_key_exists($status, Order::getPaymentStatusLabels())) {
            return null;
        }
        if ($id && $detail = $this->update($id, ['payment_status' => $status])) {
            return $detail;
        }
        return null;
    }
    /**
     * cập nhật trạng thái
     *
     * @param integer $id
     * @param integer $status
     * @return \App\Models\Order|null
     */
    public function updateDeliveryStatusOnly($id, int $status = 0)
    {
        if (!array_key_exists($status, Order::getDeliveryStatusLabels())) {
            return null;
        }
        if ($id && $detail = $this->update($id, ['delivery_status' => $status])) {
            return $detail;
        }
        return null;
    }
    /**
     * Xác thực đơn hàng
     * @param integer $id
     * @return \App\Models\Order|null
     */
    public function verify($id)
    {
        if ($order = $this->updateStatus($id, Order::VERIFIED, Order::PENDING_VERIFY)) {
            if ($order->paymentMethod->method == PaymentMethod::PAYMENT_COD) {
                return $this->updateStatusOnly($id, Order::PENDING);
            } else {
                return $this->updateStatusOnly($id, Order::PENDING);
            }
        }
        return null;
    }

    /**
     * Đã thanh toán
     * @param integer $id
     * @return \App\Models\Order|null
     */
    public function pay($id)
    {
        if ($order = $this->where('payment_status', '<=', Order::PAYMENT_PENDING_VERIFY)->where('status', '<', Order::COMPLETED)->findBy('id', $id)) {
            if ($order->paymentMethod->method == PaymentMethod::PAYMENT_COD) {
                return $this->updatePaymentStatusOnly($id, Order::PAYMENT_PAID);
            } else {
                return $this->updatePaymentStatusOnly($id, Order::PAYMENT_PAID);
            }
        }
        return null;
    }

    /**
     * Đã hoàn thành
     * @param integer $id
     * @return \App\Models\Order|null
     */
    public function compleate($id)
    {
        if ($this->where('status', '>', Order::PENDING_VERIFY)->countBy('id', $id)) {
            return $this->updateStatusOnly($id, Order::COMPLETED);
        }
        return null;
    }

    /**
     * Hủy
     * @param integer $id
     * @return \App\Models\Order|null
     */
    public function cancel($id)
    {
        if ($this->countBy('id', $id)) {
            return $this->updateStatusOnly($id, Order::CANCELED);
        }
        return null;
    }

    /**
     * chưa thanh toan
     * @param integer $id
     * @return \App\Models\Order|null
     */
    public function unpay($id)
    {
        if ($order = $this->where('payment_status', '>=', Order::PAYMENT_PAID)->where('status', '<', Order::PROCESSING)->withData()->findBy('id', $id)) {
            if ($order->paymentMethod->method == 'cod') {
                // return $this->updateStatusOnly($id, Order::PROCESSING);
            } else {
                // return $this->updateStatusOnly($id, Order::PAYMENT_PAID);
            }
        }
        return null;
    }



    public function sendMailAlertOrderStatus($id)
    {
        return true;
        if ($order = $this->withData()->findBy('id', $id)) {

            $sent = false;
            if ($order->status == Order::COMPLETED && $order->details) {
                $productIDs = [];
                foreach ($order->details as $orderProduct) {
                    if ($orderProduct->product_type == 'digital') {
                        $productIDs[] = $orderProduct->product_id;
                    }
                }

                if (count($productIDs)) {
                    $products = (new ProductRepository())->with('metadatas')->get(['id' => $productIDs]);
                    if (count($products)) {
                        $downloads = [];
                        foreach ($products as $product) {
                            $product->applyMeta();
                            if ($product->download_url) {
                                $downloads[] = $product;
                            }
                        }
                        if (count($downloads)) {
                            Mailer::to($order->billing->email)
                                ->subject(siteinfo('site_name', get_domain()) . ": Trạng thái đơn hàng #" . $order->id)
                                ->body('mails.order-status')
                                ->data([
                                    'order' => $order,
                                    'name' => $order->billing->name,
                                    'content' => "Đơn hàng $order->id của bạn đã được chuyển sang trạng thái: " . $this->statusLabels[$order->status],
                                    'downloads' => $downloads
                                ])
                                ->send();
                            $sent = true;
                        }
                    }
                }
            }
            if (!$sent) {
                Mailer::to($order->billing->email)
                    ->subject(siteinfo('site_name', get_domain()) . " Thông báo trạng thái đơn hàng #" . $order->id)
                    ->body('mails.simple-alert')
                    ->data([
                        'name' => $order->billing->name,
                        'content' => "Đơn hàng $order->id của bạn đã được chuyển sang trạng thái: " . $this->statusLabels[$order->status]
                    ])
                    ->send();
            }
        }
    }




    /**
     * lấy thông tin customer order
     *
     * @param integer $customer_id
     * @param integer $user_id
     * @return OrderRepository
     */
    public function getCustomerOrderQuery($customer_id = 0, $user_id = 0)
    {
        $this->where(function ($query) use ($customer_id, $user_id) {
            if ($customer_id) {
                $query->where('customer_id', $customer_id);
                if ($user_id) {
                    $query->orWhere('user_id', $user_id);
                }
            } else {
                $query->where('user_id', $user_id);
            }
        });
        return $this;
    }


    /**
     * lấy thông tin customer order
     *
     * @param integer $customer_id
     * @param integer $user_id
     * @param array $args
     * @return \App\Models\Order|null
     */
    public function getCustomerOrders($customer_id = 0, $user_id = 0, $args = [])
    {
        $this->getCustomerOrderQuery($customer_id, $user_id)->withData()->withCount('items')->orderBy('id', 'DESC');
        return $this->getData($args);
    }


    /**
     * lấy thông tin customer order
     *
     * @param integer $customer_id
     * @param integer $user_id
     * @param array $args
     * @return \App\Models\Order|null
     */
    public function getCustomerOrderDetail($customer_id = 0, $user_id = 0, $args = [])
    {
        $this->getCustomerOrderQuery($customer_id, $user_id)->withData();
        return $this->detail($args);
    }

    /**
     * kiểm tra user đã mua hàng hay chưa theo mã sản phẩm và thông tin user hiện tại
     *
     * @param int $product_id
     * @param array $customer_info
     * @return bool
     */
    public function checkProductBoughtCustomer($product_id, $customer_info = [])
    {
        if (!is_array($customer_info) || (!isset($customer_info['user_id']) && !isset($customer_info['customer_id']))) {
            return false;
        }
        $args = [];
        if (isset($customer_info['user_id']) && !isset($customer_info['customer_id'])) {
            $this->where(function ($query) use ($customer_info) {
                $query->where('orders.user_id', $customer_info['user_id']);
                if (is_array($customer_info['customer_id'])) {
                    $query->orWhereIn('orders.customer_id', $customer_info['customer_id']);
                } else {
                    $query->orWhere('orders.customer_id', $customer_info['customer_id']);
                }
            });
        } else {
            foreach ($customer_info as $key => $value) {
                if (in_array($key, ['name', 'phone_number', 'email'])) {
                    if (!$this->isJoindBilling) {
                        $this->joinBilling();
                    }
                    $args['billings.' . $key] = $value;
                } else {
                    $args[$key] = $value;
                }
            }
        }
        $this->join('order_items', 'prder_items.order_id', '=', 'orders.id')->where('order_items.product_id', $product_id);
        $args['status'] = Order::COMPLETED;
        return $this->count($args) > 0;
    }

    public function restoreProductInStoreAvailable(Order $prder, $type = 'inc')
    {
        if ($prder->delivery_status == Order::DELIVERY_PENDING) {
            if ($prder->items && count($prder->items)) {
                if($type == 'dec'){
                    foreach ($prder->items as $item) {
                        if ($product = $item->itemProduct) {
                            $product->available_in_store -= $item->quantity;
                            $product->save();
                        }
                    }
                }else{
                    foreach ($prder->items as $item) {
                        if ($product = $item->itemProduct) {
                            $product->available_in_store += $item->quantity;
                            $product->save();
                        }
                    }
                }

            }
        }
    }
    public function getOrderWithItems($id)
    {
        return $this->with(['items' => function ($q) {
            $q->with('itemProduct');
        }])->first(['id' => $id]);
    }
}
