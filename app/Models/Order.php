<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * Order class
 *
 * @property integer $user_id User Id
 * @property integer $customer_id Customer Id
 * @property integer $promo_id Promo Id
 * @property string $secret_id Secret Id
 * @property string $type Type
 * @property string $code Code
 * @property boolean $ship_to_different_address Ship To Different Address
 * @property integer $payment_method_id Payment Method Id
 * @property float $shipping_fee Shipping Fee
 * @property float $tax Tax
 * @property string $coupon Coupon
 * @property integer $promo_type Promo Type
 * @property float $sub_total Sub Total
 * @property float $promo_total Promo Total
 * @property float $total_money Total Money
 * @property string $note Note
 * @property integer $status Status
 * @property integer $payment_status Payment Status
 * @property integer $delivery_status Delivery Status
 * @property integer $trashed_status Trashed Status
 * @property dateTime $completed_at Completed At
 * @property string $ordered_at Ordered At
 */
class Order extends Model
{

    /**
     * Trạng thái chờ xử lý
     */
    const PENDING_VERIFY = 0;
    const VERIFIED = 100;
    const PENDING = 400;
    const PROCESSING = 500;
    const COMPLETED = 1000;
    const CANCELED = -1;
    const REFOUND = -2;


    const PAYMENT_PENDING = 0;
    const PAYMENT_PENDING_VERIFY = 100;
    const PAYMENT_CANCEL = 300;

    const PAYMENT_PAID = 200;


    const DELIVERY_PENDING = 0;
    const DELIVERY_SHIPPING = 100;
    const DELIVERY_SHIPPED = 200;




    const ALL_STATUS = [
        self::PENDING_VERIFY,       // Đang chờ xác nhận
        self::VERIFIED,             // đã xác thực
        self::PENDING,              //
        self::PROCESSING,           // đang xử lý
        self::COMPLETED,            // đã hoàn thành
        self::CANCELED,             // Bị hủy
    ];

    const STATUS_SLUGS = [
        self::PENDING_VERIFY    => "pending-verify",       // Đang chờ xác nhận
        self::VERIFIED          => "verified",             // đã xác thực
        self::PENDING           => "pending",              //
        self::PROCESSING        => "processing",           // đang xử lý
        self::COMPLETED         => "completed",            // đã hoàn thành
        self::CANCELED          => "canceled",             // Bị hủy
    ];

    // const STATUS_SLUG_MAP = array_flip(self::STATUS_SLUG_MAP);

    const STATUS_LABELS = [
        self::PENDING_VERIFY    => "Đang chờ xác nhận",
        // self::VERIFIED          => "Đã xác thực",
        self::VERIFIED          => "Chờ xác nhận",
        self::PENDING           => "Chờ xử lý",
        self::PROCESSING        => "Đang xử lý",
        self::COMPLETED         => "Đã hoàn thành",
        self::CANCELED          => "Bị hủy",

    ];


    public static function getAllStatus(){
        return static::ALL_STATUS;
    }
    public static function getStatusSlugs(){
        return static::STATUS_SLUGS;
    }
    public static function getStatusLabels(){
        return static::STATUS_LABELS;
    }


    public static function getPaymentStatusLabels()
    {
        return [
            self::PAYMENT_PENDING => 'Chờ thanh toán',
            self::PAYMENT_PENDING_VERIFY => "Chờ xác thực",
            self::PAYMENT_CANCEL => "Hủy thanh toán",
            self::PAYMENT_PAID    => 'Đã thanh toán'
        ];
    }


    public static function getDeliveryStatusLabels()
    {
        return [
            self::DELIVERY_PENDING  => 'Đang chuẩn bị giao',
            self::DELIVERY_SHIPPING => 'Đang giao hàng',
            self::DELIVERY_SHIPPED  => 'Đã hoàn tất giao hàng'
        ];
    }



    public static function getPaymentStatusSlugs()
    {
        return [
            self::PAYMENT_PENDING => 'payment-pending',
            self::PAYMENT_PENDING_VERIFY => 'payment-pending-verify',
            self::PAYMENT_CANCEL => 'payment-cancel',
            self::PAYMENT_PAID    => 'paid'
        ];
    }


    public static function getDeliveryStatusSlugs()
    {
        return [
            self::DELIVERY_PENDING  => 'delivery-pending',
            self::DELIVERY_SHIPPING => 'delivery-shipping',
            self::DELIVERY_SHIPPED  => 'delivery-shipped'
        ];
    }





    public $table = 'orders';
    public $fillable = [
        'user_id', 'customer_id', 'promo_id', 'secret_id', 'code', 'type', 'ship_to_different_address', 'payment_method_id',
        'shipping_fee', 'tax', 'coupon', 'promo_type', 'sub_total', 'promo_total', 'total_money', 'note',
        'status', 'payment_status', 'delivery_status', 'trashed_status',
        'completed_at', 'ordered_at'
    ];



    /**
     * @var array $jsonFields các cột dùng kiểu json
     */
    protected $jsonFields = ['shipping_data'];



    /**
     * kết nối với bảng order item
     * @return QueryBuilder
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function details()
    {
        return $this->items()->join('products', 'products.id', '=', 'order_items.product_id')
                    ->select(
                        'order_items.*',
                        'products.name as product_name',
                        'products.sku as product_sku',
                        'products.slug',
                        'products.type as product_type',
                        'products.featured_image as product_image'
                    );
    }


    public function productItems()
    {
        return $this->details();
    }

    public function orderAddress()
    {
        return $this->hasMany(OrderAddress::class, 'order_id', 'id');
    }

    public function billing()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id')->where('type', 'billing');
    }

    public function shipping()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id')->where('type', 'shipping');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }

    public function getPaymentMethodNameAttribute()
    {
        return $this->paymentMethod?$this->paymentMethod->name:null;
    }

    public function getStatusLabel()
    {
        return array_key_exists($this->status, self::STATUS_LABELS)?self::STATUS_LABELS[$this->status]:"";
    }
    public function getStatusLabelAttribute()
    {
        return $this->getStatusLabel();
    }
    public function getPaymentStatusLabel()
    {
        $labels = self::getPaymentStatusLabels();
        return array_key_exists($this->payment_status, $labels)?$labels[$this->payment_status]:"";
    }


    public function getDeliveryStatusLabel()
    {
        $labels = self::getDeliveryStatusLabels();
        return array_key_exists($this->delivery_status, $labels)?$labels[$this->delivery_status]:"";
    }



    /**
     * feedback
     */
    public function feedback()
    {
        return $this->hasMany('App\Models\OrderFeedback', 'order_id', 'id');
    }

    public function isTransferPayment()
    {
        return $this->paymentMethod && $this->paymentMethod->method == 'transfer';
    }

    /**
     * kiểm tra trạng thái
     *
     * @param string|int $status
     * @return boolean
     */
    public function isStatus($status)
    {
        $s = str_slug($status);
        $slug_map = array_flip(static::STATUS_SLUGS);

        if($status == $this->status || ($s && array_key_exists($s, $slug_map) && $slug_map[$s] == $this->status)) return true;
        return false;
    }

    /**
     * kiểm tra trạng thái
     *
     * @param string|int $status
     * @return boolean
     */
    public function isPaymentStatus($status)
    {
        $s = str_slug($status);
        $slug_map = array_flip(static::getPaymentStatusSlugs());

        if($status == $this->payment_status || ($s && array_key_exists($s, $slug_map) && $slug_map[$s] == $this->payment_status)) return true;
        return false;
    }




    /**
     * kiểm tra trạng thái
     *
     * @param string|int $status
     * @return boolean
     */
    public function isDeliveryStatus($status)
    {
        $s = str_slug($status);
        $slug_map = array_flip(static::getDeliveryStatusSlugs());

        if($status == $this->delivery_status || ($s && array_key_exists($s, $slug_map) && $slug_map[$s] == $this->delivery_status)) return true;
        return false;
    }




    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        $items = [];
        if(count($this->items)){
            foreach ($this->items as $item) {
                $idata = $item->toFormData();
                $items[] = $idata;

            }
        }
        $data['items'] = $items;
        return $data;
    }

    public function canCancel()
    {
        return ($this->delivery_status < self::DELIVERY_SHIPPING && $this->status > self::CANCELED && $this->status < self::COMPLETED);
    }

    public function canDelete()
    {
        return false;
    }


    public function beforeDelete()
    {
        $this->items()->delete();
        $this->feedback()->delete();
        $this->orderAddress()->delete();
    }

    // De tam
    public function getPaymentMethodText()
    {
        if ($this->paymentMethod) {
            return $this->paymentMethod->name;
        }
        return "Không xác định";
    }

    public function isPaymentMethod($method = null)
    {
        if (is_null($method)) return false;
        if($this->paymentMethod){
            return $this->paymentMethod->method == $method;
        }
        return false;
    }
    // De tam
}
