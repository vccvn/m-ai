<?php

namespace App\Models;

use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * GPTTopic class
 *
 * @property integer $parent_id Parent Id
 * @property string $name Name
 * @property string $slug Slug
 * @property string $keywords Keywords
 * @property string $description Description
 */
class GPTTopic extends Model
{
    public $table = 'gpt_topics';
    public $fillable = ['parent_id', 'name', 'slug', 'keywords', 'description', 'thumbnail', 'trashed_status'];



    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id', 'id');
    }

    /**
     * Get all of the children for the GptTopic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id');
    }

    /**
     * Get all of the prompts for the GptTopic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prompts(): HasMany
    {
        return $this->hasMany(GPTPrompt::class, 'topic_id', 'id');
    }


    /**
     * lấy danh mục cha
     */

    public function getParent()
    {
        if (!$this->parent_id) return null;
        if ($this->relationLoaded('parent')) {
            $parent = $this->relations['parent'];
            if ($parent) {
                if (!check_model_data('gpt-topic', $this->parent_id)) {
                    set_model_data('gpt-topic', $this->parent_id, $parent);
                }
                return $parent;
            }
        }
        if (!($parent = get_model_data('gpt-topic', $this->parent_id))) {
            $parent = $this->parent;
            if ($parent) {
                set_model_data('gpt-topic', $this->parent_id, $parent);
            }
        }
        return $parent;
    }

    public function getParentName($default = 'Không') {
        return $this->parent_name??($this->parent?$this->parent->name : $default);
    }

    /**
     * lấy danh mục con
     */

    public function getChildren()
    {
        return self::where('parent_id', $this->id)->where('trashed_status', $this->trashed_status)->get();
    }
    /**
     * kiểm tra có danh mục con hay ko
     */
    public function hasChild()
    {
        return self::where('parent_id', $this->id)->count();
    }

    /**
     * map category
     */
    public function getMap($list = [], $n = 0)
    {
        if (!is_array($list)) $list = [];
        if (!is_integer($n)) $n = 0;
        array_unshift($list, $this->id);
        $n++;
        if ($parent = $this->getParent()) {
            return $parent->getMap($list, $n);
        }
        return $list;
    }

    public function getTree($list = [], $n = 0)
    {
        if (!is_array($list)) $list = [];
        if (!is_integer($n)) $n = 0;
        array_unshift($list, $this);
        $n++;
        if ($parent = $this->getParent()) {
            return $parent->getTree($list, $n);
        }
        return $list;
    }
    public function getLevel($n = 0)
    {
        if (!is_integer($n)) $n = 0;
        $n++;
        if ($parent = $this->getParent()) {
            return $parent->getLevel($n);
        }
        return $n;
    }


    /**
     * lấy level của các lớp con. ví dụ lớp con có n > 0 danh mục thì sẽ +1 level
     * nếu 1 trong các danh mục lại có một lớp con nữa thì tirp61 tục +1
     * @param int $n start level
     * @return int
     */
    public function getSonLevel($n = 0)
    {
        if (!is_integer($n)) $n = 0;
        // số lớp con lớn nhất
        $max = 0;

        if (count($children = $this->getChildren())) {
            // nếu có danh mục con tự dộng tăng level lên 1 đơn vị
            $n++;
            $max = $n;
            // duyệt qua các thằng con để lấy level con của từng thằng
            foreach ($children as $child) {
                $k = $child->getSonLevel($n);
                // nếu có thằng nào đó có level con lón hơn max thì gán max bằng nó
                if ($k > $max) {
                    $max = $k;
                }
            }
            $n = $max;
        }
        return $n;
    }


    public function getViewUrl(){
        if($this->slug){
            return route('web.ai.topics.detail-by-slug', ['slug' => $this->slug]);
        }
        return route('web.ai.topics.detail-by-id', ['id' => $this->id]);

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
            $path = get_content_path( 'topics' ) . '/';
        }else{
            $path = 'static/images/icons/';
            $filename = 'topic.png';
        }

        if(file_exists($path2 = public_path($path.'120x120/'.$filename))){
            $url = asset($path.'120x120/'.$filename);
        }else{
            $url = asset($path.$filename);
        }


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
            $path = get_content_path( 'topics' ) . '/';
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

    /**
     * ẩn danh mục
     */
    public function hidden()
    {
        $this->hiddenPrompt();
        $this->trashed_status++;
        $this->save();
    }

    /**
     * ẩn danh mục
     */
    public function visible()
    {
        $this->visiblePrompt();
        $this->trashed_status--;
        $this->save();
    }

    /**
     * ẩn bài viết, sản phẩm
     */
    public function hiddenPrompt()
    {
        if (count($this->prompts)) {
            foreach ($this->prompts as $product) {
                $product->hidden();
            }
        }
    }


    /**
     * ẩn danh mục
     */
    public function visiblePrompt()
    {
        if (count($this->prompts)) {
            foreach ($this->prompts as $post) {
                $post->visible();
            }
        }
    }



    public function beforeDelete(){
        if(count($this->children)){
            foreach ($this->children as $child) {
                $child->delete();
            }
        }
        if(count($this->prompts)){
            foreach ($this->prompts as $child) {
                $child->delete();
            }
        }
    }

}
