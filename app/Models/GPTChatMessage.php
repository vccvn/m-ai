<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GPTChatMessage class
 *
 * @property integer $chat_id Chat Id
 * @property string $user User
 * @property mediumText $message Message
 * @property string $role
 * @property string $content
 */
class GPTChatMessage extends Model
{
    public $table = 'gpt_chat_messages';
    public $fillable = ['chat_id', 'role', 'message', 'content'];


    /**
     * Get the chat that owns the GPTChatMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(GPTChat::class, 'chat_id', 'id');
    }
}
