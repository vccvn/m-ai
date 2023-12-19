<?php

namespace App\Models;
use Gomee\Models\Model;
class Dynamic extends Model
{
    public $table = 'dynamics';
    public $fillable = ['name', 'slug', 'description', 'content', 'keywords', 'featured_image', 'post_type', 'use_category', 'use_gallery', 'trashed_status'];

    /**
     * @var array $jsonFields các cột dùng kiểu json
     */
    protected $jsonFields = ['prop_inputs', 'default_fields', 'advance_props', 'form_config'];

    /**
     * ket noi voi bang user_meta
     * @return queryBuilder
     */
    public function metadatas()
    {
        return $this->hasMany(Metadata::class,'ref_id','id')->where('ref', 'dynamic');
    }


    /**
     * liên kết tới danh mục
     */
    public function categories()
    {
        return $this->hasMany('App\Models\Category', 'dynamic_id', 'id')->where('type','post');
    }

    /**
     * liên kết tới bài viết
     */
    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'dynamic_id', 'id')->where('type','post');
    }


    /**
     * kiểm tra cột có phải json ko
     * @param string $columnName
     * @return boolean
     */
    public function isJson(string $columnName):bool
    {
        return in_array(strtolower($columnName), $this->jsonFields);
    }

    public function getViewUrl()
    {
        return get_dynamic_url($this);
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
            $data['featured_image'] = $this->getFeaturedImage();
        }
        if($this->post_config){
            $data['post_config'] = is_array($this->post_config)?$this->post_config:json_decode($this->post_config, true);
        }
        else{
            $data['post_config'] = [];
        }

        return $data;
    }

        /**
     * lấy tên thư mục chứa ảnh thumbnail / feature image
     * @return string tên tư mục
     */
    public function getImageFolder(): string
    {
        return $this->table;
    }




    /**
     * get image url
     * @param string $size
     * @return string
     */
    public function getFeaturedImage($size = false)
    {
        $fd = $this->getSecretPath($this->getImageFolder());
        if ($this->featured_image) {
            $featured_image = $this->featured_image;
            if($size){
                if(file_exists($f = $fd . '/' . $size . '/' . $featured_image)) return asset($f);
            }
            if(file_exists($f = $fd . '/' . $featured_image)) return asset($f);
        }
        return asset('static/images/post.png');
    }

    public function getThumbnail()
    {
        if ($this->featured_image) {
            $featured_image = $this->featured_image;
        } else {
            $featured_image = 'default.png';
        }

        $fd = $this->getSecretPath($this->getImageFolder());
        if (file_exists(public_path($fd . '/thumbs/' . $featured_image))) {
            return asset($fd . '/thumbs/' . $featured_image);
        }
        return $this->getImage();
    }

    // lấy hình ảnh theo kích thước

    public function getImage($size = null)
    {
        if (!$size) {
            return $this->getFeaturedImage();
        } elseif (in_array($size, ['thumb',  'thumbnail'])) {
            return $this->getThumbnail();
        } elseif (in_array($size, ['social',  '90x90'])) {
            return $this->getFeaturedImage($size);
        } else {
            if ($this->featured_image) {
                $featured_image = $this->featured_image;
            } else {
                $featured_image = 'default.png';
            }

            $fd = $this->getSecretPath($this->getImageFolder());
            if(file_exists($p = $fd . '/' . $size . '/' . $featured_image)){
                return asset('static/'. $this->getImageFolder(). '/' . $size . '/' . $featured_image);
            }
            return asset('static/images/default.png');
        }
    }
    /**
     * xoa image
     */
    public function deleteFeatureImage()
    {
        $fd = $this->getSecretPath($this->getImageFolder());
        if ($this->featured_image && file_exists($path = public_path($fd . '/' . $this->featured_image))) {
            unlink($path);
            if (file_exists($p = public_path($fd . '/90x90/' . $this->featured_image))) {
                unlink($p);
            }
            if (file_exists($p = public_path($fd . '/thumbs/' . $this->featured_image))) {
                unlink($p);
            }if (file_exists($p = public_path($fd . '/social/' . $this->featured_image))) {
                unlink($p);
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
     * xóa dữ liệu
     */
    public function beforeDelete()
    {
        // delete category
        if(count($this->categories)){
            foreach ($this->categories as $category) {
                $category->delete();
            }
        }
        // delete post
        if(count($this->posts)){
            foreach ($this->posts as $post) {
                $post->delete();
            }
        }
        // delete meta
        if(count($this->metadatas)){
            foreach ($this->metadatas as $metadata) {
                $metadata->delete();
            }
        }

        // delete image
        $this->deleteFeatureImage();
    }

}
