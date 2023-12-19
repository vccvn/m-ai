<?php

namespace App\Validators\Products;

use Gomee\Validators\Validator as BaseValidator;

class CategoryValidator extends BaseValidator
{
    public function extends()
    {
        

        $this->addRule('check_parent', function($prop, $value){
            if(!$value) return true;
            if($value == $this->id) return false;
            if($parent = $this->repository->findBy('id', $value)){
                if($parent->hasPost()) return false;
                // level con cua danh muc hiuen tai
                $sonLevel = $this->repository->getSonLevel($this->id);

                if($parent->getLevel() + $sonLevel < $this->repository->getMaxLevel()){
                    return true;
                }
            }
            return false;
        });

        $this->addRule('check_file', function($prop, $value){
            if(!$value) return true;
            if($file = get_media_file(['id' => $value])) return true;
            return false;
        });

        
        
        $this->addRule('check_slug', function($prop, $value){
            if(is_null($value) || strlen($value) == 0) return true;
            return preg_match('/^[A-z0-9\-]+$/', $value) && !in_array(strtolower($value), ['admin', 'api', 'manager', 'tai-khoan']);
        });
        $this->addRule('unique_slug', function($prop, $value){
            if(is_null($value) || strlen($value) == 0) return true;
            return $this->checkUniqueProp($prop, $value);
        });
        
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        $rules = [
            'name'                             => 'required|string|max:191|unique_prop:parent_id',
            'parent_id'                        => 'check_parent',
            'description'                      => 'mixed|max:300',
            'keywords'                         => 'mixed|max:180',
            // 'featured_image'                   => 'mimes:jpg,jpeg,png,gif',
            
            'first_content'                    => 'mixed',
            'second_content'                   => 'mixed',
            'page_title'                    => 'mixed',
            'meta_title'                    => 'mixed',
        ];

        // if(!$this->hasFile('featured_image') && is_numeric($this->featured_image)){
            $rules['featured_image'] = 'check_file';
        // }else{
        //     $rules['featured_image']         = 'mimes:jpg,jpeg,png,gif';
        // }

        return $rules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'name.required'                    => 'Tên danh mục không được bỏ trống',
            'name.string'                      => 'Tên danh mục không hợp lệ',
            'name.max'                         => 'Tên danh mục hơi... dài!',
            'name.unique_prop'                 => 'Tên danh mục bị trùng lặp',
            'featured_image.mimes'             => 'Định đạng file không được hỗ trợ',
            'featured_image.check_file'        => 'File không hợp lệ',
            'parent_id.check_parent'           => 'Danh mục cha không hợp lệ',
            
            
        ];
    }
}