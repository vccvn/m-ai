<?php

namespace App\Validators\AdminAuth;

use Gomee\Validators\Validator as BaseValidator;
use App\Repositories\Users\UserRepository;

use App\Repositories\Web\SettingRepository;
use App\Validators\Common\ParseInputData;
use App\Validators\Common\RemoveScriptTag;

class Register extends BaseValidator
{
    use ParseInputData, RemoveScriptTag;
    public function extends()
    {
        $this->activeRemoveAction();
        $this->addRule('unique_attr', function($name, $value, $parameters){
            if(!$value) return true;
            $data = [$name => $value];
            if(is_array($parameters) && count($parameters)){
                foreach ($parameters as $attr) {
                    if($a = trim($attr)){
                        $data[$attr] = $this->{$attr};
                    }
                }
            }
            $this->repository->removeStaffQuery();
            $result = $this->repository->first($data);
            $this->repository->staffQuery();

            if($result){
                if($this->id && $this->id == $result->id){
                    return true;
                }
                return false;
            }
            return true;
        });
        $this->addRule('phone_exists', function($name, $value){
            if(!$value) return true;
            if($user = $this->repository->first(['phone_number'=>$value])){
                if($this->id){
                    if($this->id == $user->id){
                        return true;
                    }
                }
                return false;
            }
            return true;
        });

        $this->addRule('phone_number', function($attr, $value){
            if(!$value) return true;
            return preg_match('/^(\+84|0)+[0-9]{9,10}$/si', $value);
        });

        $this->addRule('username', function($attr, $value){
            return preg_match('/^[A-z]+[A-z0-9_\.]*$/si', $value);
        });

        $this->addRule('check_ci_card_number', function($attr, $value){
            if($value == null || strlen($value) == 0) return true;
            return preg_match('/^[0-9]+[0-9]{7,15}$/si', $value);
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
        $rules = [
            'name'                => 'required|max:191',
            'email'               => 'unique_attr',
            'phone_number'        => 'phone_number|phone_exists',
            'avatar'              => 'mimes:jpg,jpeg,png,gif',
            'username'            => 'required|username|unique_attr|min:4|max:64',
            'password'            => 'required|string|min:8|password_safe|confirmed',
            'ci_card_number'      => 'required|check_ci_card_number',
            'ci_card_front_scan'  => 'required|mimes:jpg,jpeg,png,gif',
            'ci_card_back_scan'   => 'required|mimes:jpg,jpeg,png,gif',
            'agree'               => 'required|check_boolean',
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
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
            'name.max'                             => 'Họ tên dài vựa quá số ký tự',
            'name.required'                        => 'Bạn chưa nhập họ và tên',


            'username.required'                    => 'Bạn chưa nhập tên Đăng nhập',
            'username.min'                         => 'Tên người dùng phải có ít nhất 4 ký tự',
            'username.username'                    => 'Tên đăng nhập không hợp lệ',
            'username.unique_attr'                 => 'Bạn không thể dùng username này',

            'email.required'                       => 'Bạn chưa nhập email',
            'email.email'                          => 'Email không hợp lệ',
            'email.unique_attr'                    => 'Do vấn đề bảo mật, Bạn không thể sử dụng email',

            'password.required'                    => 'Bạn chưa nhập mật khẩu',
            'password.password_safe'               => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
            'password.min'                         => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
            'password.confirmed'                   => 'Mật khẩu không khớp',

            'phone_number.required'                => 'Số điện thoại không được bỏ trống',
            'phone_number.phone_number'            => 'Số điện thoại không hơp lệ',
            'phone_number.phone_exists'            => 'Số điện thoại Đã được sử dụng',
            '*.phone_number'                       => 'Số điện thoại không hơp lệ',

            'status.required'                      => 'Trạng thái không hợp lệ',


            'avatar.required'                      => 'Ảnh đại diện không được bỏ trống',
            'avatar.mimes'                         => 'Ảnh đại diện không đúng dịnh dạng',
            'ci_card_number.*'                     => 'Số thẻ không hợp lệ',
            'ci_card_front_scan.mimes'             => 'Định dạng file ảnh mặt trước không hợp lệ (jpg,png,gif)',
            'ci_card_back_scan.mimes'              => 'Định dạng file ảnh mặt sau không hợp lệ (jpg,png,gif)',
            'ci_card_front_scan.required'          => 'Thiếu ảnh mặt trước CCCD',
            'ci_card_back_scan.required'           => 'Thiếu ảnh mặt mặt sau CCCD',
            'agree.*'                              => 'Bạn chưa đồng ý với các điều khoản sử dụng',
            'g-recaptcha-response' => [
                'recaptchav3' => 'Captcha Không chính xác',
            ],
        ];
    }
}
