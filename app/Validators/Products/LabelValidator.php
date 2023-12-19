<?php

namespace App\Validators\Products;

use App\Validators\Common\CommonMethods;
use Gomee\Validators\Validator as BaseValidator;

class LabelValidator extends BaseValidator
{
    use CommonMethods;
    public function extends()
    {
        $this->addCheckColorRule();
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        return [
            
            'title'                             => 'required|string|max:150',
            'bg_color' => 'mixed|check_color',
            'text_color' => 'mixed|check_color',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'title.required'                   => 'Tiêu đề không được bỏ trống',
            'title.string'                     => 'Tiêu đề không hợp lệ',
            'title.max'                        => 'Tiêu đề hơi... dài!',
            'bg_color.check_color' => 'bg_color Không hợp lệ',
            'text_color.check_color' => 'text_color Không hợp lệ',

        ];
    }
}