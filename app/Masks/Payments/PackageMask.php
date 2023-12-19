<?php
namespace App\Masks\Payments;

use App\Models\ConnectPackage;
use Gomee\Masks\Mask;
/**
 * PackageMask class
 *
 */
class PackageMask extends Mask
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
     * @param ConnectPackage $connectPackage Tham số không bắt buộc phải khai báo.
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
        $this->currency_symbol = __('currency.' . ($this->currency??'VND'));
        $this->price_format = get_price_format($this->price, $this->currency);
    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
