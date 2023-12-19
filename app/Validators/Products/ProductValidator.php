<?php

namespace App\Validators\Products;

use App\Repositories\Products\AttributeRepository;
use App\Repositories\Products\AttributeValueRepository;
use App\Repositories\Products\LabelRepository;
use App\Repositories\Tags\TagRepository;
use App\Validators\Common\CommonMethods;
use Gomee\Validators\Validator as BaseValidator;

class ProductValidator extends BaseValidator
{
    use CommonMethods;
    protected $attrRulees = [];
    protected $attrMessages = [];

    /**
     * attr val
     *
     * @var AttributeValueRepository
     */
    public $attributeValueRepository;

    /**
     * tags
     *
     * @var TagRepository
     */
    
    public $tags;

    /**
     * tags
     *
     * @var LabelRepository
     */
    
    public $labels;

    public $attributes = [
        'use_list' => [],
        'use_value' => [],
        'attrs' => [],
        'variants' => [],
        'variant_price' => [],
        'variant_colors' => [],
        'variant_images' => []
    ];

    public function extends()
    {
        $this->attributeValueRepository = app(AttributeValueRepository::class);
        $this->tags = app(TagRepository::class);
        $this->labels = app(LabelRepository::class);
        $this->addCheckColorRule();
        // kiểm tra slug xem có trùng lặp hay ko
        $this->addRule('check_slug', function($prop, $value){
            if(is_null($value)) return true;
            if(in_array(strtolower($value), ['admin', 'api', 'manager', 'tai-khoan'])) return false;
            if($this->custom_slug){
                return $this->checkUniqueProp($prop, $value);
            }
            return true;
        });

        // kiểm tra thẻ xem có hợp lệ hay ko
        $this->addRule('check_tags', function($prop, $value){
            if(!$value) return true;
            if($value && !is_array($value)) return false;
            return count($value) == $this->tags->count(['id' => $value]);
        });
        // kiểm tra thẻ xem có hợp lệ hay ko
        $this->addRule('check_labels', function($prop, $value){
            if(!$value) return true;
            if($value && !is_array($value)) return false;
            return count($value) == $this->labels->count(['id' => $value]);
        });

        $this->addRule('check_price', function($prop, $value){
            if(is_null($value)) return true;
            if(!is_numeric($value) || to_number($value) < -1 || strlen($value) > 14) return false;
            return true;
        });
        $this->addRule('check_sale_price', function($prop, $value){
            if(is_null($value)) return true;
            if(!$this->on_sale) return true;
            if(!is_numeric($value) || to_number($value) < -1 || strlen($value) > 12) return false;
            return true;
        });

        $this->addRule('check_value_id', function($prop, $value){
            if(!$value) return true;
            $total = is_array($value)?count($value) : 1;
            return $this->attributeValueRepository->count(['id' => $value]) == $total;
        });

        $this->addRule('option_number', function($prop, $value){
            if($value && !is_numeric($value) || strlen($value) > 10) return false;
            return true;
        });


        // Thêm các rule ở đây
        $this->addRule('check_total', function($prop, $value){
            if(!strlen($value)) return true;
            if(!is_numeric($value) || $value < 0 || strlen($value) > 10) return false;
            return true;

        });



        $attributeGroups = app(AttributeRepository::class)->getAttributeInput($this->category_id, true);
        $attrs = [
            'use_list' => [],
            'use_value' => [],
            'attrs' => [],
            'variants' => [],
            'variant_list' => [],
            'variant_price' => [],
            'variant_colors' => [],
            'variant_images' => [],
            'variant_thumbnails' => []
        ];
        if($attributeGroups['attributes']){
            $attributes = $attributeGroups['attributes'];
            if(count($attributes['required']) || count($attributes['optional'])){
                foreach ($attributes as $needed => $group) {
                    foreach ($group as $attribute) {
                        $id = $attribute->id;

                        $rule = $needed;
                        $name = $attribute->name;

                        $key = 'attributes.'.$name;
                        $label = $attribute->label??$name;
                        // thêm thông báo
                        if($needed == 'required'){
                            $this->attrMessages[$key.'.'.$needed] = $label .' không được vỏ trống';
                        }
                        if($attribute->input_type != 'default'){
                            $attrs['use_list'][] = $id;
                            $rule.= '|check_value_id';
                            $this->attrMessages[$key.'.check_value_id'] = $label .' không hợp lệ';
                        }else{
                            $attrs['use_value'][] = $id;
                            if(in_array($attribute->value_type, ['int', 'decimal'])){
                                $nk = $needed == 'required'?'numeric':'option_number';
                                $rule .= '|'.$nk;
                                $this->attrMessages[$key.'.'.$nk] = $label .' phải là số';
                            }
                        }
                        $this->attrRulees[$key] = $rule;
                        $attrs['attrs'][$id] = $attribute;
                    }
                }
            }
        }
        if($attributeGroups['variants']){
            $variants = is_array($this->variants)?$this->variants:[];

            foreach ($attributeGroups['variants'] as $key => $attribute) {
                $id = $attribute->id;
                $rule = 'check_value_id';
                $name = $attribute->name;
                $key = 'variants.'.$name;
                $label = $attribute->label??$name;
                $this->attrMessages[$key.'.check_value_id'] = $label .' không hợp lệ';
                $this->attrRulees[$key] = $rule;
                $attrs['variant_list'][] = $id;
                $attrs['variants'][$id] = $attribute;
                $avt = $attribute->advance_value_type;
                $useThumbnail = $attribute->use_thumbnail;

                if(isset($variants[$name]) && is_array($variants[$name])){
                    $ids = $variants[$name];
                    foreach($ids as $value_id){
                        $this->attrMessages['variant_price.'.$value_id.'.check_price'] = 'Giá không hợp lệ';
                        $this->attrRulees['variant_price.'.$value_id] = 'check_price';
                        $attrs['variant_price'][] = $value_id;
                        if($useThumbnail == 1){
                            $this->attrMessages['variant_thumbnails.'.$value_id.'.mimes'] = 'File ảnh thumbnail không hợp lệ';
                            $this->attrRulees['variant_thumbnails.'.$value_id] = 'mimes:jpg,jpeg,gif,png,svg';
                            $attrs['variant_thumbnails'][] = $value_id;
                        }
                        if($avt == 'image'){
                            $this->attrMessages['variant_images.'.$value_id.'.mimes'] = 'File ảnh không hợp lệ';
                            $this->attrRulees['variant_images.'.$value_id] = 'mimes:jpg,jpeg,gif,png,svg';
                            $attrs['variant_images'][] = $value_id;
                        }
                        elseif($avt == 'color'){
                            $this->attrMessages['variant_colors.'.$value_id.'.check_color'] = 'Mã màu không hợp lệ';
                            $this->attrRulees['variant_colors.'.$value_id] = 'check_color';
                            $attrs['variant_color'][] = $value_id;
                        }

                    }
                }
            }
        }

        $this->attributes = $attrs;

        

        // required nếu tạo mới
        $this->addRule('specification', function($name, $value){
            if(is_null($value)) return true;
            if(!is_array($value)) return false;
            if(count($value)){
                if(!isset($value['name']) || !strlen($value['name']) ) return false;
            }
            return true;
        });
        
        $this->addRule('check_features', function($attr, $value){
            if(!$value) return true;
            $data = text2array($value);
            return count($data) == count(array_filter($data, function($s){return strlen($s) < 64;}));
        });

        $this->addRule('check_ais', function($attr, $value){
            if(is_numeric($value)){
                if($value < 0 || strlen($value) > 10) return false;
            }
            elseif($value) return false;
            if(!$value) return true;
            return $this->total && to_number($this->total) - to_number($value) >= 0;
        });

        

    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [
            'name'                             => 'required|mixed|max:180',
            'category_id'                      => 'required|exists:categories,id',
            'slug'                             => 'check_slug',
            'custom_slug'                      => 'mixed',
            'detail'                           => 'mixed',
            'description'                      => 'mixed|max:500',
            'keywords'                         => 'mixed|max:180',
            'featured_image'                   => 'mimes:jpg,jpeg,png,gif',
            'featured_image_data'              => 'base64_file:image',
            'featured_image_keep_original'     => 'mixed',
            'tags'                             => 'check_tags',
            'labels'                           => 'check_labels',
            'sku'                              => 'required|unique_prop',
            'price_status'                     => 'in_list:-1,0,1',
            'privacy'                          => 'privacy',
            'status'                           => 'binary',
            'type'                             => 'in_list:standard,digital',
            'download_url'                     => 'mixed|max:180',
            'meta_title'                       => 'mixed|max:180',
            'meta_description'                 => 'max:300',
            'feature_description'              => 'mixed|max:5000',
            'features'                         => 'check_features',
            'total'                            => 'check_total',
            'available_in_store'               => 'check_ais',
            'gallery'                          => 'mixed',
            'attribute_default_selected'       => 'mixed',
            'focus_keyword'                    => 'mixed|max:180',
            'note'                             => 'mixed|max:5000',
            
        ];

        $rules = array_merge($rules, $this->attrRulees);

        if($this->price_status != -1){
            $rules['list_price']               = 'required|check_price';
        }
        if($this->specification){
            $rules['specification'] = 'array';
            $rules['specification.*'] = 'specification';
        }

        return $rules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return array_merge([
            'name.required'                    => 'Tên sản phẩm không được bỏ trống',
            'name.string'                      => 'Tên sản phẩm không hợp lệ',
            'name.max'                         => 'Tên sản phẩm hơi... dài! (180 ký tự thôi)',
            'name.unique_prop'                 => 'Tên sản phẩm bị trùng lặp',
            'featured_image.mimes'             => 'Định đạng file không được hỗ trợ',
            'category_id.required'             => 'Danh mục không được bỏ trống',
            'category_id.numeric'              => 'Danh mục không hợp lệ',
            'category_id.exists'               => 'Danh mục không tồn tại',
            'tags.check_tags'                  => 'Thẻ không hợp lệ',
            'gallery_data.base4_list'          => 'File không hợp lệ',
            'description.max'                  => 'Mô tả hơi... dài! (500 ký tự thôi)',
            'keywords.max'                     => 'Từ khóa hơi... dài! (150 ký tự thôi)',
            
            'price_status.*'                   => 'Trạng thái giá không hợp lệ',
            'list_price.required'              => 'Giá sản phẩm không được bỏ trống',
            'list_price.check_price'           => 'Giá sản phẩm không không hợp lệ',
            'sale_price.check_sale_price'      => 'Giá khuyến mãi không hop75 lệ',
            'privacy.privacy'                  => 'Tính riêng tư không hợp lệ',
            'total.check_total'                => 'Số lượng sản phẩm nhập kho không hợp lệ',
            'available_in_store.*'             => 'Số sản phẩm khả dụng phải lớn hơn hoặc bằng 0 và nhỏ hơn hoặc bằng tổng sản phẩm trong kho',
            'sku.required'                     => 'Mã sản phẩm không được bỏ trống',
            'sku.string'                       => 'Mã sản phẩm không hợp lệ',
            'sku.max'                          => 'Mã sản phẩm hơi... dài!',
            'sku.unique_prop'                  => 'Mã sản phẩm bị trùng lặp',
            'features.*'                       => 'Mổi đặc điểm chứa không quá 64 ký tự',
            
            '*.max'                            => 'Vượt quá số ký tự cho phép',
        ], $this->attrMessages);
    }
}