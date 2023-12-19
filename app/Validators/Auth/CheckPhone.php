<?php

namespace App\Validators\Auth;

use Gomee\Validators\Validator as BaseValidator;
use App\Repositories\Users\UserRepository;

use App\Repositories\Web\SettingRepository;

class CheckPhone extends BaseValidator
{
    public function extends()
    {

        $this->addRule('phone_exists', function($name, $value){
            if(!$value) return true;
            if($user = $this->repository->first(['phone_number'=>$value])){
                if($user->id == $this->user()->id){
                    return true;
                }
                return false;
            }
            return true;
        });

        $this->addRule('phone_number', function($attr, $value){
            if(!$value) return true;
            return preg_match('/[0-9_\.\-\+]{10,16}$/si', $value);
        });

    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        $rules = [
            'phone_number'               => 'phone_number|phone_exists',
        ];

        // if($this->hasFile('ci_card_front_scan')){
        //     $rules['ci_card_front_scan'] = 'mimes:jpg,jpeg,png,gif';
        // }
        // if($this->hasFile('ci_card_back_scan')){
        //     $rules['ci_card_back_scan'] = 'mimes:jpg,jpeg,png,gif';
        // }

        return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'full_name.max'                        => 'Họ tên dài vựa quá số ký tự',
            'full_name.required'                   => 'Bạn chưa nhập họ và tên',

            'email.required'                       => 'Bạn chưa nhập email',
            'email.email'                          => 'Email không hợp lệ',
            'email.unique_attr'                    => 'Do vấn đề bảo mật, Bạn không thể sử dụng email',

            'password.required'                    => 'Bạn chưa nhập mật khẩu',
            'password.password_safe'               => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
            'password.min'                         => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
            'password.confirmed'                   => 'Mật khẩu không khớp',

            'phone.required'                       => 'Số điện thoại không được bỏ trống',
            'phone.phone_number'                   => 'Số điện thoại không hơp lệ',
            'phone.phone_exists'                   => 'Số điện thoại Đã được sử dụng',
            '*.phone_number'                       => 'Số điện thoại không hơp lệ',

            'status.required'                      => 'Trạng thái không hợp lệ',


            'avatar.required'                      => 'Ảnh đại diện không được bỏ trống',
            'avatar.mimes'                         => 'Ảnh đại diện không đúng dịnh dạng',

        ];
    }
}
