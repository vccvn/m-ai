<?php

namespace App\Validators\Products;

use App\Repositories\Products\CategoryRepository;
use App\Repositories\Products\LabelRepository;
use App\Repositories\Tags\TagRepository;
use App\Validators\Common\CommonMethods;
use Gomee\Validators\Validator as BaseValidator;

class CollectionValidator extends BaseValidator
{
    use CommonMethods;
    
    /**
     * tags
     *
     * @var LabelRepository
     */
    
    public $labels;

    /**
     * CategoryRepository
     *
     * @var CategoryRepository
     */
    public $categoryRepository = null;
    public function extends()
    {
        
        $this->labels = app(LabelRepository::class);
        $this->tags = app(TagRepository::class);
        $this->categoryRepository = app(CategoryRepository::class);
        $this->addCheckColorRule();
        
        $this->addRule('check_category', function ($prop, $value) {
            if (!$value) return true;
            return $this->categoryRepository->count(['id' => $value]) == 1;
        });
        $this->addRule('check_sorttype', function ($prop, $value) {
            if (!$value) return true;
            return array_key_exists($value, get_product_sortby_options());
        });

        // kiểm tra thẻ xem có hợp lệ hay ko
        $this->addRule('check_tags', function($prop, $value){
            if(!$value) return true;
            if($value && !is_array($value)) return false;
            return $this->tags->count(['id' => $value]) == count($value);
        });
        $this->addRule('check_categories', function($prop, $value){
            if(!$value) return true;
            if($value && !is_array($value)) return false;
            return $this->categoryRepository->count(['id' => $value]) == count($value);
        });
        
        $this->addRule('check_slug', function($prop, $value){
            if(is_null($value)) return true;
            if($this->custom_slug){
                if(!preg_match('/^[A-z0-9\-\_]+[A-z0-9\-\_]*$/i', $value)) return false;
                return $this->checkUniqueProp($prop, $value);
            }
            return true;
        });
        $this->addRule('check_labels', function($prop, $value){
            if(!$value) return true;
            if($value && !is_array($value)) return false;
            return count($value) == $this->labels->count(['id' => $value]);
        });
        
        $this->addRule('check_file', function($prop, $value){
            if(!$value) return true;
            if($file = get_media_file(['id' => $value])) return true;
            return false;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        return [
            'name'                             => 'required|string|max:150',
            'slug'                             => 'check_slug',
            'labels'                           => 'check_labels',
            'custom_slug'                      => 'mixed',
            'description'                      => 'mixed|max:300',
            'categories'                       => 'check_categories',
            'sorttype'                         => 'check_sorttype',
            'keywords'                         => 'mixed|max:180',
            'tags'                             => 'check_tags',
            'featured_image'                   => 'check_file'


        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'name.required'                    => 'Tên bộ sư tập không được bỏ trống',
            'name.string'                      => 'Tên bộ sư tập không hợp lệ',
            'name.max'                         => 'Tên bộ sư tập hơi... dài!',
            'slug.check_slugs'                 => 'Slug bị trùng hoặc không đúng chuẩn',
            'tags.check_tags'                  => 'Thẻ không hợp lệ',
            'description.mixed'                => 'description Không hợp lệ',
            'sorttype.*'                       => 'Kểu sắp xếp Không hợp lệ',
            'keywords.mixed'                   => 'search Không hợp lệ',
            'category_id.required'             => 'Danh mục không được bỏ trống',
            'category_id.numeric'              => 'Danh mục không hợp lệ',
            'category_id.exists'               => 'Danh mục không tồn tại',

        ];
    }
}