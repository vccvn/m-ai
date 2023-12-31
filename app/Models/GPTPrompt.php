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
 * @property string $prompt_config Prompt
 * @property string $placeholder placeholder
 * @property array $config
 */
class GPTPrompt extends Model
{
    public $table = 'gpt_prompts';
    public $fillable = ['topic_id', 'name', 'keywords', 'description', 'prompt', 'prompt_config', 'config', 'placeholder', 'trashed_status'];

    public $casts = [
        'config' => 'json'
    ];

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

    public function getConfigData(){
        $a = $this->config;
        if(is_array($a))
            return $a;
        $b = json_decode($a, true);
        return $b??[];
    }


}
