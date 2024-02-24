<?php
namespace App\Masks\Verifications;

use App\Models\Verification;
use Gomee\Masks\Mask;
/**
 * VerificationMask class
 *
 * @property string $id ID của verification
 * @property string $type Loại verify
 * @property string $method Phương thức
 * @property string $received_by Nhận mã thông qua giá trị cụ thể
 * @property string $ref Bảng liên quan
 * @property string $ref_id id của bảng liên quan
 * @property string $code Mã xác minh
 * @property string $status trạng thái
 * @property string $expired_at Hạn xác thực
 */
class VerificationMask extends Mask
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
     * @param Verification $verification Tham số không bắt buộc phải khai báo.
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
