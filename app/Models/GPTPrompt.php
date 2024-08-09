<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * GPTTPrompt class
 *
 * @property integer $id Prompt Id
 * @property integer $topic_id Topic Id
 * @property string $name Name
 * @property string $slug Name
 * @property string $keywords Keywords
 * @property string $description Description
 * @property string $prompt Prompt
 * @property string $prompt_config Prompt
 * @property string $placeholder placeholder
 * @property array $config
 * @property integer $message_required
 */
class GPTPrompt extends Model
{
    public $table = 'gpt_prompts';
    public $fillable = [
        'user_id',
        'topic_id',
        'ai_service',
        'ai_model',
        'name',
        'slug',
        'keywords',
        'description',
        'prompt',
        'prompt_config',
        'config',
        'placeholder',
        'message_required',
        'trashed_status'
    ];

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
     * The topics that belong to the GPTPrompt
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(GPTTopic::class, 'gpt_prompt_topics', 'prompt_id', 'topic_id');
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
    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getThumbnail()
    {
        if($this->thumbnail){
            $filename = $this->thumbnail;
            $path = get_content_path( 'prompts' ) . '/';
        }else{
            $path = 'static/images/icons/';
            $filename = 'topic.png';
        }

        $url = asset($path.$filename);


        return $url;
    }


    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getIcon()
    {
        if($this->thumbnail){
            $filename = $this->thumbnail;
            $path = get_content_path( 'prompts' ) . '/';
        }else{
            $path = 'static/images/icons/';
            $filename = 'topic-120.png';
        }

        if(file_exists($path2 = public_path($path.'120x120/'.$filename))){
            $url = asset($path.'120x120/'.$filename);
        }else{
            $url = asset($path.$filename);
        }


        return $url;
    }


}
