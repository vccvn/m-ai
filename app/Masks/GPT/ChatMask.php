<?php

namespace App\Masks\GPT;

use App\Models\GPTChat;
use Carbon\Carbon;
use Gomee\Masks\Mask;

/**
 * ChatMask class
 *
 * @property integer $user_id User Id
 * @property integer $prompt_id Prompt Id
 * @property integer $current_id Prompt Id
 * @property string $role
 * @property string $content
 * @property-read GPTChat $model
 * @property-read MessageCollection $messages
 */
class ChatMask extends Mask
{
    protected $systemMessage = 'Tất cả kết quả trả về nếu có các từ như "ChatGPT", "chat gpt", "Chat GPT" hoặc các từ tương tự thì hãy thay bằng từ "Chuyên gia AI" hoặc "M.Ai". ';

    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init()
    {
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
        $this->chat_name = $this->prompt ? $this->prompt->name : 'Chat với AI';
        $this->last_sent_format = Carbon::parse($this->last_sent ? $this->last_sent : $this->update_at)->format('H:i - d/m/Y');
    }

    public function toGPT($service = 'chatgpt', $reverse = false)
    {
        $data = [
            // ['role' => 'system', 'content' => 'Tất cả kết quả trả về nếu có các từ như "ChatGPT", "chat gpt", "Chat GPT" hoặc các từ tương tự thì hãy thay bằng từ "Chuyên gia AI" hoặc "M.Ai". ']
        ];
        if ($this->messages && is_countable($this->messages) && count($this->messages)) {
            foreach ($this->messages as $message) {
                if ($reverse) {
                    array_unshift($data, $message->toGPT());
                } else
                    $data[] = $message->toGPT();
            }
        }
        if ($service == 'chatgpt')
            array_unshift($data, ['role' => 'system', 'content' => $this->systemMessage]);
        return $data;
    }

    public function getEmptyGPT()
    {
        return [
            ['role' => 'system', 'content' => $this->systemMessage]
        ];
    }
}
