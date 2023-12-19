<?php

namespace App\Validators\Account;

class GeneralValidator extends BaseValidator
{
    public function extends()
    {
        parent::extends();

        $this->addRule('check_gender', function ($attr, $value) {
            if (!$value) return true;

            return in_array($value, ['male', 'female', 'other']);
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [
            'name'     => 'sometimes|max:191',
            'gender'   => 'sometimes|check_gender',
            'birthday' => 'sometimes|arrdate:str'


        ];

        return $this->parseRules($rules);
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'name.max'      => 'Họ tên dài vựa quá số ký tự',
            'name.required' => 'Bạn chưa nhập họ và tên',


            'first_name.required' => 'Tên không được bỏ trống',

            'last_name.required' => 'Họ và đệm không được bỏ trống',

            'gender.required'     => 'Giới tính không được bỏ trống',
            'gender.chevk_gender' => 'Giới tính không hợp lệ',

            'birthday.required' => 'Ngày sinh không được bỏ trống',
            'birthday.strdate'  => 'Ngày sinh không hợp lệ',

            'address.max' => 'Địa chỉ không hợp lệ',

        ];
    }
}