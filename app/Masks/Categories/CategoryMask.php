<?php
namespace App\Masks\Categories;

use App\Masks\Metadatas\MetadataCollection;
use App\Masks\Posts\PostCollection;
use App\Masks\Products\ProductCollection;
use App\Masks\Projects\ProjectCollection;

use App\Models\Category;
use Gomee\Masks\Mask;

class CategoryMask extends Mask
{

    
    protected function init(){
        // khai báo các hàm được cho phép gọi trực tiếp 
        $this->allow([
            'hasChild', 'getMap', 'getLevel', 'getSonLevel', 'hasPost', 'countProduct', 'countPost',
            'getFullTitle', 'getViewUrl', // 'getFeaturedImage', 
            
            'getFeaturedImage' => 'getThumbnail',
            'getParent' => 'getCategory',
            'parent' => 'category'
        ]);
        $this->map([
            'metadatas'        => MetadataCollection::class,
            'parent'         => static::class,
            'posts'          => PostCollection::class,
            'products'       => ProductCollection::class,
            'projects'       => ProjectCollection::class,
            'children'       => CategoryCollection::class,
            'activeChildren' => CategoryCollection::class,
            'activePosts'    => PostCollection::class,
            'activeProducts' => ProductCollection::class
        ]);
    }
    /**
     * lấy data từ model sang mask
     * @param Category $category Tham số không bặt buộc phải khai báo. 
     * Xem thêm ExampleMask
     */
    // public function toMask()
    // {
    //     $data = $this->getAttrData();
    //     // thêm data tại đây.
    //     // Xem thêm ExampleMask
    //     return $data;
        
    // }

    protected function onLoaded()
    {
        if(!check_model_data('category', $this->id)){
            set_model_data('category', $this->id, $this);
        }
        $this->title = $this->name;
        if($this->first_content) $this->first_content = do_shortcode($this->first_content);
        if($this->second_content) $this->second_content = do_shortcode($this->second_content);
    }
    public function getParent()
    {
        if(!$this->parent_id) return null;
        if ($parent = $this->relation('parent')) {
            if($parent){
                if(!check_model_data('category', $this->parent_id)){
                    set_model_data('category', $this->parent_id, $parent);
                }
                return $parent;
            }
            
        }
        if(!($parent = get_model_data('category', $this->parent_id, false))){
            $parent = $this->relation('parent', true);
            if($parent){
                set_model_data('category', $this->parent_id, $parent);
            }
        }
        return $parent;
    }

    public function getChildren()
    {
        return new CategoryCollection($this->model->getChildren());
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
     * gán dự liệu meta cho product
     * @return void
     */
    public function applyMeta()
    {
        if (!$this->meta) {
            if ($metadatas = $this->relation('metadatas', true)) {
                $jsf = $this->model->getJsonFields();
                foreach ($metadatas as $m) {
                    if (in_array($m->name, $jsf)) {
                        $value = json_decode($m->value, true);
                    } else {
                        $value = $m->value;
                    }
                    $this->data[$m->name] = $value;
                    $this->meta[$m->name] = $value;
                }
            }
        } elseif ($this->meta) {
            foreach ($this->meta as $key => $value) {
                $this->data[$key] = $value;
            }
        }
        
    }

}