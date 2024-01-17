<?php
namespace App\Masks\GPT;

use App\Models\GPTChat;
use Gomee\Masks\Mask;
/**
 * ChatMask class
 *
 * @property integer $user_id User Id
 * @property integer $prompt_id Prompt Id
 * @property string $role
 * @property string $content
 * @property-read GPTChat $model
 * @property-read MessageCollection $messages
 */
class ChatMask extends Mask
{

    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        $this->map([
            'messages' => MessageCollection::class,
            'prompt' => PromptMask::class
        ]);
    }

    /**
     * lấy data từ model sang mask
     * @param GPTChat $model Tham số không bắt buộc phải khai báo.
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

    public function toGPT(){
        $data = [
            ['role' => 'system', 'content' => 'Tất cả kết quả trả về nếu có các từ như "ChatGPT", "chat gpt", "Chat GPT" hoặc các từ tương tự thì hãy thay bằng từ "Chuyên gia AI" hoặc "M.Ai". ']
        ];
        if($this->messages && is_countable($this->messages) && count($this->messages)){
            foreach ($this->messages as $message) {
                $data[] = $message->toGPT();
            }
        }
        return $data;
    }

}
