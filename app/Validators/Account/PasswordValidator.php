<?php

namespace App\Validators\Account;

class PasswordValidator extends BaseValidator
{
    public function extends()
    {
        parent::extends();
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        $rules = [
            'current_password'                     => 'required|password_match',
            'password'                     => 'required|string|min:6|confirmed'
        ];

        return $this->parseRules($rules);
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            
            'current_password.required'            => 'Bạn chưa nhập mật khẩu',
            'current_password.password_match'      => 'Mật khẩu không khớp',

            
            'password.required'                    => 'Bạn chưa nhập mật khẩu',
            'password.min'                         => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed'                   => 'Mật khẩu không khớp',
            
        ];
    }
}