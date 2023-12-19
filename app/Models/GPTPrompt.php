<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GPTTPrompt class
 *
 * @property integer $topic_id Topic Id
 * @property string $name Name
 * @property string $keywords Keywords
 * @property string $description Description
 * @property string $prompt Prompt
 */
class GPTPrompt extends Model
{
    public $table = 'gpt_prompts';
    public $fillable = ['topic_id', 'name', 'keywords', 'description', 'prompt', 'trashed_status'];


    /**
     * Get the topic that owns the GPTPrompt
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(GPTTopic::class, 'topic_id', 'id');
    }


    /**
     * ẩn danh mục
     */
    public function hidden()
    {
        $this->trashed_status++;
        $this->save();
    }

    /**
     * ẩn danh mục
     */
    public function visible()
    {
        $this->trashed_status--;
        $this->save();
    }


}
