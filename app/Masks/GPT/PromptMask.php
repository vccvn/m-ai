<?php
namespace App\Masks\GPT;

use App\Models\GPTPrompt;
use Gomee\Masks\Mask;
/**
 * PromptMask class
 *
 */
class PromptMask extends Mask
{

    // xem thêm ExampleMask
    protected $hidden = [
        'prompt', 'prompt_config', 'config', 'trashed_status'
    ];
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
     * @param GPTPrompt $gPTPrompt Tham số không bắt buộc phải khai báo.
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
