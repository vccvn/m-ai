<?php

namespace App\Repositories\GPT;

use Gomee\Repositories\BaseRepository;
use App\Masks\GPT\MessageMask;
use App\Masks\GPT\MessageCollection;
use App\Models\GPTChatMessage;
use App\Validators\GPT\MessageValidator;
use Illuminate\Http\Request;

/**
 * @method MessageCollection<MessageMask>|GPTChatMessage[] filter(Request $request, array $args = []) lấy danh sách GPTChatMessage được gán Mask
 * @method MessageCollection<MessageMask>|GPTChatMessage[] getFilter(Request $request, array $args = []) lấy danh sách GPTChatMessage được gán Mask
 * @method MessageCollection<MessageMask>|GPTChatMessage[] getResults(Request $request, array $args = []) lấy danh sách GPTChatMessage được gán Mask
 * @method MessageCollection<MessageMask>|GPTChatMessage[] getData(array $args = []) lấy danh sách GPTChatMessage được gán Mask
 * @method MessageCollection<MessageMask>|GPTChatMessage[] get(array $args = []) lấy danh sách GPTChatMessage
 * @method MessageCollection<MessageMask>|GPTChatMessage[] getBy(string $column, mixed $value) lấy danh sách GPTChatMessage
 * @method MessageMask|GPTChatMessage getDetail(array $args = []) lấy GPTChatMessage được gán Mask
 * @method MessageMask|GPTChatMessage detail(array $args = []) lấy GPTChatMessage được gán Mask
 * @method MessageMask|GPTChatMessage find(integer $id) lấy GPTChatMessage
 * @method MessageMask|GPTChatMessage findBy(string $column, mixed $value) lấy GPTChatMessage
 * @method MessageMask|GPTChatMessage first(string $column, mixed $value) lấy GPTChatMessage
 * @method GPTChatMessage create(array $data = []) Thêm bản ghi
 * @method GPTChatMessage update(integer $id, array $data = []) Cập nhật
 */
class MessageRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = MessageValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = MessageMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = MessageCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\GPTChatMessage::class;
    }

}