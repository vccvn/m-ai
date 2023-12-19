<?php

namespace App\Validators\Auth;

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
        $this->addRule('unique_attr', function ($name, $value, $parameters) {
            if (!$value)
                return true;
            $data = [$name => $value];
            if (is_array($parameters) && count($parameters)) {
                foreach ($parameters as $attr) {
                    if ($a = trim($attr)) {
                        $data[$attr] = $this->{$attr};
                    }
                }
            }
            $result = $this->repository->first($data);

            if ($result) {
                return false;
            }
            return true;
        });
        $this->addRule('phone_exists', function ($name, $value) {
            if (!$value) return true;
            if ($user = $this->repository->first(['phone_number' => $value])) {
                return false;
            }
            return true;
        });

        $this->addRule('phone_number', function ($attr, $value) {
            if (!$value) return true;
            return preg_match('/[0-9_\.\-\+]{10,16}$/si', $value);
        });

        $this->addRule('check_email', function ($attr, $value) {
            if (!$value) return true;
            return is_email($value);
        });

        $this->addRule('username', function ($attr, $value) {
            return preg_match('/^[A-z]+[A-z0-9_\.]*$/si', $value);
        });

        $this->addRule('coupon_code', function ($attr, $value) {
            if (!$value) return true;
            return $this->repository->count(['affiliate_code' => $value]) > 0;
        });


        $this->addRule('password_safe', function ($attr, $value) {
            if ($value == null || strlen($value) < 8) return false;
            return preg_match('/[A-Z]/', $value) && preg_match('/[a-z]/', $value) && preg_match('/[0-9]/', $value) && preg_match('/[^A-z0-9\s]/', $value) && str_replace('/[A-z0-9]/', '', $value) != '';
        });
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        $rules = [
            'full_name'           => 'required|string|max:181',
            'phone_number'               => 'phone_number|phone_exists',
            'email'               => 'check_email|unique_attr|max:191',
            'password'            => 'required|string|min:8|password_safe',
            'coupon_code'         => 'coupon_code'


        ];
        return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'full_name.max'                        => 'Họ tên dài vựa quá số ký tự',
            'full_name.required'                   => 'Bạn chưa nhập họ và tên',

            'email.required'                       => 'Bạn chưa nhập email',
            'email.check_email'                          => 'Email không hợp lệ',
            'email.unique_attr'                    => 'Do vấn đề bảo mật, Bạn không thể sử dụng email',

            'password.required'                    => 'Bạn chưa nhập mật khẩu',
            'password.password_safe'               => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
            'password.min'                         => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
            'password.confirmed'                   => 'Mật khẩu không khớp',

            'phone.required'                       => 'Số điện thoại không được bỏ trống',
            'phone.phone_number'                   => 'Số điện thoại không hơp lệ',
            'phone.phone_exists'                   => 'Bạn không thể sử dụng số điện thoại này!',
            '*.phone_number'                       => 'Số điện thoại không hơp lệ',

            'status.required'                      => 'Trạng thái không hợp lệ',


            'avatar.required'                      => 'Ảnh đại diện không được bỏ trống',
            'avatar.mimes'                         => 'Ảnh đại diện không đúng dịnh dạng',

        ];
    }
}
