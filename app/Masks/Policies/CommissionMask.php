<?php
namespace App\Masks\Policies;

use App\Models\CommissionPolicy;
use Gomee\Masks\Mask;
/**
 * CommissionMask class
 * 
 * @property string $name Name
 * @property integer $level Level
 * @property string $description Description
 * @property float $commission_level_1 Commission Level 1
 * @property float $commission_level_2 Commission Level 2
 * @property float $commission_level_3 Commission Level 3
 * @property float $commission_level_4 Commission Level 4
 * @property-read CommissionPolicy $model
 */
class CommissionMask extends Mask
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
     * @param CommissionPolicy $model Tham số không bắt buộc phải khai báo. 
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