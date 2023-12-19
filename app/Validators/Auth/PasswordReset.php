<?php

namespace App\Validators\Auth;
use Gomee\Validators\Validator as BaseValidator;
class PasswordReset extends BaseValidator
{
    /**
     * thêm rules
     */
    public function extends()
    {
        $this->addRule('is_code', function($prop, $value){
            if($this->repository->checkOTP($value)) return true;
            return false;
        });

        $this->addRule('password_safe', function($attr, $value){
            if($value == null || strlen($value) == 0) return false;
            return preg_match('/[A-Z]/', $value) && preg_match('/[a-z]/', $value) && preg_match('/[0-9]/', $value) && preg_match('/[^A-z0-9\s]/', $value) && str_replace('/[A-zo-9]/', '', $value) != '';
        });
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [
            'otp'              => 'required|is_code',
            'password'         => 'required|min:6|confirmed|password_safe'
        ];
    }

    public function messages()
    {
        return [
            'code.required'                        => 'OTP không hợp lệ',
            'code.is_code'                         => 'OTP không hợp lệ',
            'password.required'                    => 'Mật khẩu không được bỏ trống',
            'password.min'                         => 'Mật khẩu từ 8-12 kí tự gồm chữ thường, chữ hoa , số và kí tự đặc biệt',
            'password.confirmed'                   => 'Mật khẩu không khớp',
            'password.password_safe'               => 'Mật khẩu từ 8-12 kí tự gồm chữ thường, chữ hoa , số và kí tự đặc biệt'

        ];
    }
}
