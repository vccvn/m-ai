<?php
namespace App\Masks\Categories;

use App\Models\CategoryRef;
use Gomee\Masks\Mask;

class CategoryRefMask extends Mask
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
        $this->map('category', CategoryMask::class);
    }

    /**
     * lấy data từ model sang mask
     * @param CategoryRef $categoryRef Tham số không bắt buộc phải khai báo. 
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