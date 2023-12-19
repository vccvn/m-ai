<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    const TYPE_POST = 'post';
    const TYPE_PRODUCT = 'product';

    public $table = 'categories';
    public $fillable = ['dynamic_id', 'parent_id', 'name', 'type', 'slug', 'keywords', 'description', 'first_content', 'second_content', 'featured_image', 'trashed_status'];



    public function parent()
    {
        return $this->belongsTo(static::class,'parent_id','id');
    }

    /**
     * Get all of the refs for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refs(): HasMany
    {
        return $this->hasMany(CategoryRef::class, 'category_id');
    }

    public function refProducts(): HasMany
    {
        return $this->refs()->where('ref', Product::REF_KEY);
    }

    public function products()
    {
        return $this->refProducts()->join('products', 'products.id', '=', 'category_refs.ref_id')->select('category_refs.ref_id', 'category_refs.category_id', 'products.*');
    }

    /**
     * Get all of the relativeProducts for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relativeProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }


    /**
     * The products that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function untrashedProducts()
    {
        return $this->products()->where('trashed_status', 0);
    }
    public function notTrashedProducts()
    {
        return $this->untrashedProducts();
    }

    public function refPosts(): HasMany
    {
        return $this->refs()->where('ref', 'post');
    }

    /**
     * The products that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->refPosts()->join('posts', 'posts.id', '=', 'category_refs.ref_id')->select('category_refs.ref_id', 'category_refs.category_id', 'posts.*');
    }

    /**
     * Get all of the relativePosts for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relativePosts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }

    public function untrashedPosts()
    {
        return $this->posts()->where('trashed_status', static::UNTRASHED);
    }


    public function refAttributes()
    {
        return $this->refs()->where('ref', Attribute::REF_KEY);
    }

    /**
     * The products that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->refAttributes()->join('attributes', 'attributes.id', '=', 'category_refs.ref_id')->select('category_refs.ref_id', 'category_refs.category_id', 'attributes.*');
    }



    // public $resources = [];
    /**
     * ket noi voi bang user_meta
     * @return queryBuilder
     */
    public function metadatas()
    {
        return $this->hasMany(Metadata::class, 'ref_id', 'id')->where('ref', 'category');
    }


    public function getParentName($default = '')
    {
        return $this->parent?$this->parent->name:$default;
    }

    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $this->applyMeta();
        $data = $this->toArray();
        if($this->featured_image){
            if(preg_match('/^@media\:[0-9]+/i', $this->featured_image)){
                $data['featured_image'] = substr($this->featured_image, 7);
            }

        }
        return $data;
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
     * xoa anh
     */
    public function deleteFeatureImage()
    {

        if(preg_match('/^@media\:[0-9]+/i', $this->featured_image)){
            return true;
        }
        if($this->featured_image){
            $fd = $this->getSecretPath('categories/');
            if(file_exists($path = public_path($fd.$this->featured_image))){
                unlink($path);
            }
            if(file_exists($path2 = public_path($fd.'thumbs/'.$this->featured_image))){
                unlink($path2);
            }
        }
    }

     /**
     * ham xóa file cũ
     * @param int $id
     *
     * @return boolean
     */
    public function deleteAttachFile()
    {
        return $this->deleteFeatureImage();
    }

    /**
     * lấy tên file đính kèm cũ
     */
    public function getAttachFilename()
    {
        return $this->featured_image;
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

    /**
     * lấy danh mục con
     */

    public function getChildren()
    {
        return self::where('parent_id',$this->id)->where('trashed_status',$this->trashed_status)->get();
    }
    /**
     * kiểm tra có danh mục con hay ko
     */
    public function hasChild()
    {
        return self::where('parent_id',$this->id)->count();
    }

    /**
     * map category
     */
    public function getMap($list = [], $n = 0)
    {
        if(!is_array($list)) $list = [];
        if(!is_integer($n)) $n = 0;
        array_unshift($list,$this->id);
        $n++;
        if($parent = $this->getParent()){
            return $parent->getMap($list,$n);
        }
        return $list;
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
    public function getLevel($n = 0)
    {
        if(!is_integer($n)) $n = 0;
        $n++;
        if($parent = $this->getParent()){
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
        if(!is_integer($n)) $n = 0;
        // số lớp con lớn nhất
        $max = 0;

        if(count($children = $this->getChildren())){
            // nếu có danh mục con tự dộng tăng level lên 1 đơn vị
            $n++;
            $max = $n;
            // duyệt qua các thằng con để lấy level con của từng thằng
            foreach ($children as $child) {
                $k = $child->getSonLevel($n);
                // nếu có thằng nào đó có level con lón hơn max thì gán max bằng nó
                if($k > $max){
                    $max = $k;
                }
            }
            $n = $max;
        }
        return $n;
    }


    /**
     * danh mục con
     */
    public function children():HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }


    public function orderProducts()
    {
        return $this->products();
    }


    public function childrenDetail()
    {
        $query = $this->children();
        if($this->type == 'product'){
            $query->withCount('untrashedProducts as products_count');
            $query->withCount('attributes as attributes_count');

        }
        elseif($this->type == 'post'){
            $query->selectRaw("categories.*, count(
                select posts.id from posts where posts.category_map like concat('% ', categories.id, ',%')
            ) as posts_count");
        }

        return $query;
    }

    /**
     * danh mục con
     */
    public function activeChildren()
    {
        return $this->children()->where('trashed_status', 0);
    }

    /**
     * danh sach post chưa bị xóa hoặc ẩn
     */
    public function activePosts()
    {
        return $this->posts()->where('trashed_status', 0);
    }

    public function activeProducts()
    {
        return $this->products()->where('trashed_status', 0);
    }



    /**
     * danh mục con
     */
    public function hiddenChildren()
    {
        return $this->children()->where('trashed_status', -1);
    }

    public function hiddenPosts()
    {
        return $this->posts()->where('trashed_status', -1);
    }

    public function hiddenProducts()
    {
        return $this->products()->where('trashed_status', -1);
    }




    public function hasPost()
    {
        $model = ($this->type == 'product') ? 'App\Models\Product' : 'App\Models\Post';
        $query = $this->hasMany($model,'category_id','id');
        return $query->count()?true:false;
    }


    public function countProduct()
    {
        return Product::where('category_map', 'like', '% '.$this->id.',%')->where('trashed_status', $this->trashed_status)->count();
    }
    public function countPost()
    {
        return Post::where('category_map', 'like', '% '.$this->id.',%')->where('trashed_status', $this->trashed_status)->where('type', 'post')->count();
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
            // $title .= "Sãn phẩm";
        }elseif ($this->type == 'project') {
            $title .= "Dự án";
        }else{
            $title .= "Tin bài";
        }
        return $title;
    }


    /**
     * ẩn danh mục
     */
    public function hidden()
    {
        $this->hiddenPost();
        $this->trashed_status++;
        $this->save();
    }

    /**
     * ẩn danh mục
     */
    public function visible()
    {
        $this->visiblePost();
        $this->trashed_status--;
        $this->save();
    }

    /**
     * ẩn bài viết, sản phẩm
     */
    public function hiddenPost()
    {
        if($this->type == 'post'){
            if(count($this->posts)){
                foreach ($this->posts as $post) {
                    $post->hidden();
                }
            }
        }elseif ($this->type == 'product') {
            if(count($this->products)){
                foreach ($this->products as $product) {
                    $product->hidden();
                }
            }
        }
    }


    /**
     * ẩn danh mục
     */
    public function visiblePost()
    {
        if($this->type == 'post'){
            if(count($this->posts)){
                foreach ($this->posts as $post) {
                    $post->visible();
                }
            }
        }elseif ($this->type == 'product') {
            if(count($this->products)){
                foreach ($this->products as $product) {
                    $product->visible();
                }
            }
        }
    }

    /**
     * kiểm tra có thể xóa hay không
     * @return boolean
     */
    public function canDelete()
    {
        return true;
    }



    /**
     * kiểm tra có thể xóa hay không
     * @return boolean
     */
    public function canForceDelete()
    {
        return true;
    }


    /**
     * kiểm tra có thể xóa hay không
     * @return boolean
     */
    public function canMoveToTrash()
    {
        if($this->type == self::TYPE_PRODUCT){
            if($this->product_count) return 'Vì tính toàn vẹn dữ liệu, bạn không thể xóa danh mục đang có sản phẩm';
            if($this->attribute_count) return 'Vì tính toàn vẹn dữ liệu, Bạn không thể xóa danh mục đang có thuộc tính sản phẩm';
        }
        return true;
    }


    /**
     * thủ tục trước khôi phục
     */
    public function beforeRestore()
    {
        if(count($this->hiddenChildren)){
            foreach ($this->hiddenChildren as $child) {
                $child->visible();
            }
        }

    }




    /**
     * thủ tục trước khi xoa
     */
    public function beforeMoveToTrash()
    {
        if(count($this->activeChildren)){
            foreach ($this->activeChildren as $child) {
                $child->hidden();
            }
        }
        $this->hiddenPost();
    }

    /**
     * thủ tục trước khi xóa
     */
    public function beforeDelete()
    {
        if(count($this->children)){
            foreach ($this->children as $child) {
                $child->delete();
            }
        }
        if(count($this->refs)){
            foreach ($this->refs as $child) {
                $child->delete();
            }
        }
        if($this->type == 'post'){
            if(count($this->relativePosts)){
                foreach ($this->relativePosts as $post) {
                    $post->delete();
                }
            }
        }
        elseif ($this->type == 'product') {
            if(count($this->relativeProducts)){
                foreach ($this->relativeProducts as $product) {
                    $product->delete();
                }
            }
        }

        $this->deleteFeatureImage();
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


}
