<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryRef extends Model
{
    public $table = 'category_refs';
    public $fillable = ['category_id', 'ref_id', 'ref'];

    public $timestamps = false;

    /**
     * Get the category that owns the CategoryRef
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getFeaturedImage($urlencode=false)
    {
        if(preg_match('/^@media\:[0-9]+/i', $this->featured_image) && $file = get_media_file(['id' => substr($this->featured_image, 7)])){
            $url = $file->url;
        }
        else{
            $path = $this->getSecretPath('categories');
            if($this->featured_image && file_exists($p = $path . '/'.$this->featured_image)){
                $featured_image = $p;

            }else{
                $featured_image = 'static/images/default.png';
            }
            $url = url($featured_image);
        }

        if($urlencode) return urlencode($url);
        return $url;
    }


    /**
     * lay duong danh danh muc
     */
    public function getViewUrl()
    {
        $url = null;
        if($this->type == 'post'){
            $url = get_post_category_url($this);
        }
        elseif($this->type == 'product'){
            $url = get_product_category_url($this);
        }
        elseif($this->type == 'project'){
            $url = get_project_category_url($this);
        }
        return $url;
    }



    public function getFullTitle()
    {
        $title = '';

        $tree = $this->getTree();
        foreach ($tree as $cate) {
            $title = $cate->name . ' | '.$title;
        }
        if($this->type == 'post'){
            if($dynamic = get_model_data('dynamic', $this->dynamic_id)){
                $title .= $dynamic->name;
            }else{
                $title .= "Blog";
            }
        }elseif ($this->type == 'product') {
            $title .= "Sãn phẩm";
        }elseif ($this->type == 'project') {
            $title .= "Dự án";
        }else{
            $title .= "Tin bài";
        }
        return $title;
    }


    public function getTree($list = [], $n = 0)
    {
        if(!is_array($list)) $list = [];
        if(!is_integer($n)) $n = 0;
        array_unshift($list,$this);
        $n++;
        if($parent = $this->getParent()){
            return $parent->getTree($list,$n);
        }
        return $list;
    }



    /**
     * lấy danh mục cha
     */

    public function getParent()
    {
        if(!$this->parent_id) return null;
        if ($this->relationLoaded('parent')) {
            $parent = $this->relations['parent'];
            if($parent){
                if(!check_model_data('category', $this->parent_id)){
                    set_model_data('category', $this->parent_id, $parent);
                }
                return $parent;
            }

        }
        if(!($parent = get_model_data('category', $this->parent_id))){
            $parent = $this->parent;
            if($parent){
                set_model_data('category', $this->parent_id, $parent);
            }
        }
        return $parent;

    }

}
