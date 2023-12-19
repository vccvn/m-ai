<?php

namespace App\Validators\Auth;

use Gomee\Validators\Validator as BaseValidator;
class Login extends BaseValidator
{
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [
            'username'            => 'required',
            'password'            => 'required',
        ];

        if(session('login-fail', 0) >= 4){
            $rules = array_merge($rules, ['g-recaptcha-response' => 'required|recaptchav3:register,0.5']);

        }


        return $rules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'username.required'                    => 'Bạn chưa nhập tên Đăng nhập',
            'password.required'                    => 'Bạn chưa nhập mật khẩu',
            'g-recaptcha-response' => [
                'recaptchav3' => 'Captcha Không chính xác',
            ],


        ];
    }
}
