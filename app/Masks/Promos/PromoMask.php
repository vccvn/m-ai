<?php
namespace App\Masks\Promos;

use App\Models\Promo;
use Gomee\Masks\Mask;

class PromoMask extends Mask
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
        $this->allow([
            'getDownTotalFLabel'
        ]);
    }

    /**
     * lấy data từ model sang mask
     * @param Promo $promo Tham số không bắt buộc phải khai báo. 
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
        // $this->down_total_label = $this->getDownTotalFLabel();
    }
    
    
    // khai báo thêm các hàm khác bên dưới nếu cần
}