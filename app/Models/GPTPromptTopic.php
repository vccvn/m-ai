<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GPRTromptTopic class
 *
 * @property integer $prompt_id Prompt Id
 * @property integer $topic_id Topic Id
 */
class GPTPromptTopic extends Model
{
    public $table = 'gpt_prompt_topics';
    public $fillable = ['prompt_id', 'topic_id'];

    public $timestamps = false;

    /**
     * Get the prompt that owns the GPRPromptTopic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(GPTPrompt::class, 'prompt_id', 'id');
    }

    /**
     * Get the topic that owns the GPRPromptTopic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(GPTTopic::class, 'topic_id', 'id');
    }

}
