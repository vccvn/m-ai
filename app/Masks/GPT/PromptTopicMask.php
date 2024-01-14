<?php
namespace App\Masks\GPT;

use App\Models\GPTPromptTopic;
use Gomee\Masks\Mask;
/**
 * PromptTopicMask class
 * 
 * @property-read GPTPromptTopic $model
 */
class PromptTopicMask extends Mask
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
     * @param GPTPromptTopic $model Tham số không bắt buộc phải khai báo. 
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