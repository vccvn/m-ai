<?php
namespace App\Masks\GPT;

use App\Models\GPTTopic;
use Gomee\Masks\Mask;
/**
 * TopicMask class
 * @property GPTTopic $model
 * @property-read PromptCollection $prompts
 * @property-read TopicMask $parent
 * @property-read TopicCollection $children
 */
class TopicMask extends Mask
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
        $this->map([
            'prompts' => PromptCollection::class,
            'parent' => self::class,
            'children' => TopicCollection::class
        ]);
        $this->allow('getViewUrl', 'getIcon', 'getThumbnail');
    }

    /**
     * lấy data từ model sang mask
     * @param GPTTopic $gPTTopic Tham số không bắt buộc phải khai báo.
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
        $this->view_url = $this->model->getViewUrl();

    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
