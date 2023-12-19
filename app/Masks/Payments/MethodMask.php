<?php
namespace App\Masks\Payments;

use App\Models\PaymentMethod;
use Gomee\Masks\Mask;
/**
 * MethodMask class
 *
 */
class MethodMask extends Mask
{

    protected $hidden = [
        'config', 'hidden'
    ];
    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    // protected function init(){
    //     # code...
    // }

    /**
     * lấy data từ model sang mask
     * @param PaymentMethod $paymentMethod Tham số không bắt buộc phải khai báo.
     * Xem thêm ExampleMask
     */
    // public function toMask()
    // {
    //     $data = $this->getAttrData();
    //     // thêm data tại đây.
    //     // Xem thêm ExampleMask
    //     return $data;

    // }

    /**
     * sẽ được gọi sau khi thiết lập xong
     *
     * @return void
     */
    protected function onLoaded()
    {
        $this->logo =  asset('static/payments/portal/'. $this->icon);
    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
