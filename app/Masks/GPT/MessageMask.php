<?php

namespace App\Masks\GPT;

use App\Models\GPTChatMessage;
use Gomee\Masks\Mask;

/**
 * MessageMask class
 *
 * @property integer $chat_id Chat Id
 * @property string $user User
 * @property mediumText $message Message
 * @property-read GPTChatMessage $model
 */
class MessageMask extends Mask
{
    protected $hidden = [
        'content'
    ];

    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init()
    {
        # code...
    }

    /**
     * lấy data từ model sang mask
     * @param GPTChatMessage $model Tham số không bắt buộc phải khai báo.
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

    public function toGPT()
    {
        return ['role' => $this->role, 'content' => trim(strip_tags($this->content))];
    }
}
