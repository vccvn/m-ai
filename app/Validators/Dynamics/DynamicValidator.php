<?php

namespace App\Validators\Dynamics;

use Gomee\Validators\Validator as BaseValidator;

class DynamicValidator extends BaseValidator
{
    public function extends()
    {
        $this->addRule('check_slug', function($prop, $value){
            if(is_null($value)) return true;
            if(in_array(strtolower($value), ['admin', 'api', 'manager', 'tai-khoan'])) return false;
            if($this->custom_slug){
                return $this->checkUniqueProp($prop, $value);
            }
            return true;
        });

        $this->addRule('post_type', function($prop, $value){
            if(is_null($value)) return true;
            return in_array(strtolower($value), ['article', 'news', 'gallery', 'video_embed', 'custom']);
        });
        $this->addRule('default_fields', function($prop, $value){
            if(is_null($value)) return true;
            if(is_array($value)){
                foreach ($value as $field) {
                    if(!in_array($field, ["title", "slug", "keywords", "description", "content", 'content_type', "featured_image", "tags", "privacy", "meta_title", "meta_description", 'seo'])) return false;
                }
            }
            return true;
        });

        $this->addRule('post_config', function($prop, $value){
            if(is_null($value)) return true;
            if(is_array($value)){
                foreach ($value as $field => $value) {
                    if(in_array($field, ["thumbail_width", "thumbail_height", "social_width", "social_height"]) && (!is_numeric($value) || $value <= 0)) return false;
                }
            }
            return true;
        });

        
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        $rules = [
            'name'                             => 'required|string|max:191|unique_prop',
            'slug'                             => 'check_slug',
            'custom_slug'                      => 'mixed',
            'description'                      => 'mixed|max:300',
            'content'                          => 'mixed',
            'keywords'                         => 'mixed|max:180',
            'featured_image'                    => 'mimes:jpg,jpeg,png,gif',
            
            'featured_image_data'               => 'base64_file:image',
            'post_type'                        => 'post_type',
            'use_category'                     => 'check_boolean',
            'use_gallery'                      => 'check_boolean',
            'default_fields'                   => 'default_fields',
            'post_config'                      => 'post_config',
        ];
        if($this->advance_props){
            $rules['advance_props'] = 'array';
            $rules['advance_props.*'] = 'prop_input';
        }


        return $rules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'name.required'                    => 'Tên mục không được bỏ trống',
            'name.string'                      => 'Tên mục không hợp lệ',
            'name.max'                         => 'Tên mục hơi... dài!',
            'name.unique_prop'                 => 'Tên mục bị trùng lặp',
            'slug.check_slug'                  => 'Đường dẫn không hợp lệ',
            'featured_image.mimes'              => 'Định đạng file không được hỗ trợ',
            'featured_image_data.base64_file'   => 'Định đạng file không được hỗ trợ',
            'post_type.post_type'              => 'Loại nội dung không hợp lệ',
            'default_fields.default_fields'    => 'Các thông tin mặc định không hợp lệ',
            'advance_props.array'              => 'Thông tin thuộc tính không hợp lệ',
            'advance_props.*.prop_input'       => 'Thông tin thuộc tính không hợp lệ',
            
            
        ];
    }
}