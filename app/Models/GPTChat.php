<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * GPTChat class
 *
 * @property integer $user_id User Id
 * @property integer $prompt_id Prompt Id
 */
class GPTChat extends Model
{
    public $table = 'gpt_chats';
    public $fillable = ['user_id', 'prompt_id'];


    /**
     * Get all of the messages for the GPTChat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(GPTChatMessage::class, 'chat_id', 'id');
    }
}
