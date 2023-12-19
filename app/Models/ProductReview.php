<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    
    const REF_KEY = 'product-review';

    public $table = 'product_reviews';
    public $fillable = ['product_id', 'customer_id', 'rating', 'name', 'email', 'comment', 'approved', 'approved_id'];

    /**
     * ket noi voi bang user_meta
     * @return HasMany
     */
    public function metadatas()
    {
        return $this->hasMany(Metadata::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }

    /**
     * lấy tên thư mục chứa ảnh thumbnail / feature image
     * @return string tên tư mục
     */
    public function getImageFolder(): string
    {
        return 'products';
    }

    
    public function timeFormat($format = 'd/m/Y')
    {
        return date($format, strtotime($this->created_at));
    }

    public function getReviewerName()
    {
        return $this->name?$this->name:$this->customer_name;
    }
    public function getReviewerEmail()
    {
        return $this->email?$this->email:$this->customer_email;
    }


    /**
     * get image url
     * @param string $size
     * @return string
     */
    public function getFeaturedImage($size = false)
    {
        $fd = $this->getSecretPath() . '/' . $this->getImageFolder();
        if ($this->featured_image) {
            $featured_image = $this->featured_image;
            if ($size) {
                if (file_exists($f = $fd . '/' . $size . '/' . $featured_image)) return asset($f);
            }
            if (file_exists($f = $fd . '/' . $featured_image)) return asset($f);
        }
        return asset('static/images/product.png');
    }

    
    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string 
     */
    public function getAvatar($urlencode=false)
    {

        if($this->avatar){
            $avatar = content_path('users/' . $this->secret_id .'/avatar/' . $this->avatar);

        }else{
            $avatar = 'static/images/default/avatar.png';
        }
        $url = url($avatar);
        if($urlencode) return urlencode($url);
        return $url;
    }
    
}
