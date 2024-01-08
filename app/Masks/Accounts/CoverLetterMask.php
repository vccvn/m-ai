<?php
namespace App\Masks\Accounts;

use App\Models\CoverLetter;
use Gomee\Masks\Mask;
/**
 * CoverLetterMask class
 * 
 * @property integer $user_id User Id
 * @property string $message Message
 * @property integer $status Status
 * @property-read CoverLetter $model
 */
class CoverLetterMask extends Mask
{

    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        # code...
    }

    /**
     * lấy data từ model sang mask
     * @param CoverLetter $model Tham số không bắt buộc phải khai báo. 
     * Xem thêm ExampleMask
     * @return array>string, miced>
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
        # code...
    }
 
}