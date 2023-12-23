<?php

namespace App\Validators\GPT;

use Gomee\Validators\Validator as BaseValidator;

class CriteriaValidator extends BaseValidator
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

            'label' => 'required|string|max:150|unique_prop',
            'type' => 'required|string|in_list:text,textarea,number',
            'description' => 'max:150',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'label.*' => 'Label Không hợp lệ',
            'type.*' => 'Type Không hợp lệ',
            'description.*' => 'Description Không hợp lệ',

        ];
    }
}
