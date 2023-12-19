<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * GPTChatMessage class
 * 
 * @property integer $chat_id Chat Id
 * @property string $user User
 * @property mediumText $message Message
 */
class GPTChatMessage extends Model
{
    public $table = 'gpt_chat_messages';
    public $fillable = ['chat_id', 'user', 'message'];

    
    
}
