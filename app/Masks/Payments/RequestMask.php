<?php
namespace App\Masks\Payments;

use App\Models\PaymentRequest;
use Gomee\Masks\Mask;
/**
 * RequestMask class
 *
 */
class RequestMask extends Mask
{

    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        $this->map(['method' => MethodMask::class]);
    }

    /**
     * lấy data từ model sang mask
     * @param PaymentRequest $paymentRequest Tham số không bắt buộc phải khai báo.
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
        $this->time_format = $this->dateFormat('H:i - d/m/Y');

        $this->status_text = $this->getStatusLabel();
    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
