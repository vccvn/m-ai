<?php

namespace App\Validators\Files;

use Gomee\Validators\Validator as BaseValidator;

class FileValidator extends BaseValidator
{
    public function extends()
    {

    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [
            'title'       => 'max:191',
            'description' => 'mixed|max:500',
            'file'        => 'mimes:jpg,jpeg,png,gif|max:512000',
            "privacy"     => "string",
            "sid"         => 'string',
            "date_path"   => "date",
            "ref"         => "string",
        ];

        return $rules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'file.mimes' => 'Định đạng file không được hỗ trợ'
        ];
    }
}