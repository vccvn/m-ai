<?php

namespace App\Masks\Orders;

use App\Masks\Payments\MethodMask;
use App\Models\Order;
// use App\Models\Orders;
use Gomee\Masks\Mask;

class OrderMask extends Mask
{
    public static $status_list = [];
    public static $payment_methods = [];

    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init()
    {
        if (!static::$status_list) {
            static::$status_list = get_order_status_list();
        }
        if (!static::$payment_methods) {
            static::$payment_methods = get_order_payment_methods();
        }
        $this->map([
            'details' => OrderItemCollection::class,
            'billing' => OrderAddressMask::class,
            'shipping' => OrderAddressMask::class,
            'paymentMethod' => MethodMask::class,
            'items' => OrderItemCollection::class
        ]);
        $this->allow('canCancel', 'isTransferPayment', 'isStatus', 'isPaymentStatus', 'isDeliveryStatus', 'getDatetime','getPaymentMethodText', 'getPaymentStatusLabel', 'getDeliveryStatusLabel');
    }

    
    public function getStatusLabel()
    {

        $a = Order::getStatusLabels();
        
        return $a[$this->status] ?? "Không xác định";
    }
    public function getPaymentMethodText()
    {
        if ($this->paymentMethod) {
            return $this->paymentMethod->name;
        }
        return "Không xác định";
    }

    public function is($status)
    {
        return $this->isStatus($status);
    }

    public function isPaymentPending()
    {
        return $this->isPaymentStatus(Order::PAYMENT_PENDING);
    }
    public function isPaymentPendingVerify()
    {
        return $this->isPaymentStatus(Order::PAYMENT_PENDING_VERIFY);
    }
    public function isPaid()
    {
        return $this->isPaymentStatus(Order::PAYMENT_PAID);
    }

    /**
     * kiểm tra phương thức thanh toán
     *
     * @param string|int $method
     * @return boolean
     */
    public function isPaymentMethod($method = null)
    {
        if (is_null($method)) return false;
        if($this->paymentMethod){
            return $this->paymentMethod->method == $method;
        }
        return false;
    }
}
