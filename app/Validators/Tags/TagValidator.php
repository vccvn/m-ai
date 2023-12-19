<?php

namespace App\Validators\Tags;

use Gomee\Validators\Validator as BaseValidator;

class TagValidator extends BaseValidator
{
    public function extends()
    {
        // Thêm các rule ở đây
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        return [
            
            'name' => 'mixed',
            'name_lower' => 'mixed',
            'keyword' => 'mixed',
            'slug' => 'mixed',
            'tagged_count' => 'mixed',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'name.mixed' => 'name Không hợp lệ',
            'name_lower.mixed' => 'name_lower Không hợp lệ',
            'keyword.mixed' => 'keyword Không hợp lệ',
            'slug.mixed' => 'slug Không hợp lệ',
            'tagged_count.mixed' => 'tagged_count Không hợp lệ',

        ];
    }
}