<?php

namespace App\Repositories\GPT;

use Gomee\Repositories\BaseRepository;
use App\Masks\GPT\ChatMask;
use App\Masks\GPT\ChatCollection;
use App\Models\GPTChat;
use App\Validators\GPT\ChatValidator;
use Illuminate\Http\Request;

/**
 * @method ChatCollection<ChatMask>|GPTChat[] filter(Request $request, array $args = []) lấy danh sách GPTChat được gán Mask
 * @method ChatCollection<ChatMask>|GPTChat[] getFilter(Request $request, array $args = []) lấy danh sách GPTChat được gán Mask
 * @method ChatCollection<ChatMask>|GPTChat[] getResults(Request $request, array $args = []) lấy danh sách GPTChat được gán Mask
 * @method ChatCollection<ChatMask>|GPTChat[] getData(array $args = []) lấy danh sách GPTChat được gán Mask
 * @method ChatCollection<ChatMask>|GPTChat[] get(array $args = []) lấy danh sách GPTChat
 * @method ChatCollection<ChatMask>|GPTChat[] getBy(string $column, mixed $value) lấy danh sách GPTChat
 * @method ChatMask|GPTChat getDetail(array $args = []) lấy GPTChat được gán Mask
 * @method ChatMask|GPTChat detail(array $args = []) lấy GPTChat được gán Mask
 * @method ChatMask|GPTChat find(integer $id) lấy GPTChat
 * @method ChatMask|GPTChat findBy(string $column, mixed $value) lấy GPTChat
 * @method ChatMask|GPTChat first(string $column, mixed $value) lấy GPTChat
 * @method GPTChat create(array $data = []) Thêm bản ghi
 * @method GPTChat update(integer $id, array $data = []) Cập nhật
 */
class ChatRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = ChatValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = ChatMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = ChatCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\GPTChat::class;
    }

    /**
     * lay thông tin chat
     *
     * @param int $user_id
     * @param int $chat_id
     * @return GPTChat|ChatMask|null
     */
    public function getUserChatDetail($user_id, $chat_id){
        return $this->with(['prompt', 'messages' => function($query){
            $query->orderBy('id', 'ASC');
        }])->mode('mask')->detail(['user_id' => $user_id, 'id' => $chat_id]);
    }
    /**
     * lay thông tin chat
     *
     * @param int $user_id
     * @param int $chat_id
     * @return GPTChat|ChatMask|null
     */
    public function createChatDetail($user_id, $prompt_id = 0){
        if($prompt_id && !app(PromptRepository::class)->count(['id' => $prompt_id]))
            return false;
        $chat = $this->create(compact('user_id', 'prompt_id'));
        /**
         * @var ChatMask
         */
        $chatMask = $this->mask($chat);
        $chatMask->__lock();
        return $chatMask;
    }

}
