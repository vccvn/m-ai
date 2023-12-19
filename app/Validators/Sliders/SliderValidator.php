<?php

namespace App\Validators\Sliders;

use Gomee\Validators\Validator as BaseValidator;

class SliderValidator extends BaseValidator
{
    public function extends()
    {
        // Thêm các rule ở đây
        $this->addRule('check_size', function($prop, $value){
            if(!is_numeric($value) || $value < 200 || strlen($value) > 10) return false;
            return true;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        $rules = [
            
            'name' => 'required|string',
            'slug' => 'mixed|max:180',
            'description' => 'mixed|max:500',
            'crop' => 'check_boolean',
            'status' => 'check_boolean',
            
        ];
        if($this->crop){
            $rules = array_merge($rules, [
                'width' => 'check_size',
                'height' => 'check_size',
            
            ]);
        }
        return $rules;
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'name.*'                        => 'Tên slider Không hợp lệ',
            'width.check_size'              => 'Chiều rộng Tối thiểu là 200px',
            'height.check_size'             => 'Chiều cao Tối thiểu là 200px',
            
        ];
    }
}