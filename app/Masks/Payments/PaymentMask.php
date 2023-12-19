<?php
namespace App\Masks\Payments;

use App\Models\Payment;
use Gomee\Masks\Mask;
/**
 * PaymentMask class
 *
 * @property integer $user_id
 * @property string $order_id
 * @property integer $transaction_id
 * @property integer $payment_transactions_id
 * @property string $status
 * @property string $order_code
 * @property integer $amount
 * @property string $response_code
 * @property string $response_message
 * @property string $bank_type
 * @property string $bank_code
 * @property string $url
 */
class PaymentMask extends Mask
{

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
     * @param Payment $payment Tham số không bắt buộc phải khai báo.
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
    // protected function onLoaded()
    // {
    //     # code...
    // }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
