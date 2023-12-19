<?php

namespace App\Validators\Users;

use App\Repositories\Locations\RegionRepository;
use App\Repositories\MBTI\DetailRepository;
use App\Validators\Common\ParseInputData;
use App\Validators\Common\RemoveScriptTag;
use Gomee\Validators\Validator as BaseValidator;

class AccountUpdateValidator extends BaseValidator
{
    use ParseInputData, RemoveScriptTag;
    public function extends()
    {
        $this->activeRemoveAction();
        $this->addRule('unique_attr', function ($name, $value, $parameters) {
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
                if ($this->user()->id == $result->id) {
                    return true;
                }
                return false;
            }
            return true;
        });
        $this->addRule('phone_exists', function ($name, $value) {
            if (!$value) return true;
            if ($user = $this->repository->first(['phone_number' => $value])) {
                if ($this->user()->id == $user->id) {
                    return true;
                }
                return false;
            }
            return true;
        });

        $this->addRule('phone_number', function ($attr, $value) {
            if (!$value) return true;
            return preg_match('/^[0-9\+\-\.]{9,14}$/si', $value);
        });
        $this->addRule('mbti', function ($attr, $value) {
            if (!$value) return true;
            return is_string($value) && app(DetailRepository::class)->count(['mbti' => strtoupper($value)]) == 1;
        });

        $this->addRule('username', function ($attr, $value) {
            return preg_match('/^[A-z]+[A-z0-9_\.]*$/si', $value);
        });

        $this->addRule('check_gender', function ($attr, $value) {
            if (!$value) return true;
            return in_array(strtolower($value), ['male', 'female', 'other']);
        });

        $this->addRule('address_data', function ($attr, $value) {
            if (!$value || !is_array($value)) {
                return true;
            }
            if (array_key_exists('region_id', $value) && $value['region_id']) {
                if (!app(RegionRepository::class)->first(['id' => $value['region_id']])) return false;
            }
            return true;
        });
        $this->addRule('check_reg', function ($attr, $value) {
            if (!$value)
                return true;
            if (!app(RegionRepository::class)->first(['id' => $value])) return false;
            return true;
        });
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [
            'full_name'           => 'required|max:150',
            'gender'              => 'check_gender',
            'birthday'            => 'strdate',
            // 'address_data'        => 'address_data',
            'region_id'         => 'check_reg',
            'email'               => ['required', 'email', 'unique_attr', 'regex:/[a-z0-9]+@[a-z]+\.[a-z]{2,3}/'],
            // 'phone_number'               => 'phone_number|phone_exists',
            'mbti'                => 'mbti',
            'bio'                 => 'mixed|max:100000',
            'hobbies'             => 'mixed',
            'images'              => 'mixed',
            'delete_images'       => 'mixed',

            'reason'              => 'max:191',
        ];

        return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'full_name.max'                        => 'Họ tên dài vựa quá số ký tự',
            'full_name.required'                   => 'Bạn chưa nhập họ và tên',


            'username.required'                    => 'Bạn chưa nhập tên Đăng nhập',
            'username.min'                         => 'Tên người dùng phải có ít nhất 2 ký tự',
            'username.username'                    => 'Tên đăng nhập không hợp lệ',
            'username.unique_attr'                 => 'Bạn không thể dùng username này',

            'email.required'                       => 'Bạn chưa nhập email',
            'email.email'                          => 'Email không hợp lệ',
            'email.unique_attr'                    => 'Email của bạn đã tồn tại ytong hệ thống',

            'phone.phone_number'                   => 'Số điện thoại không hơp lệ',
            'phone.phone_exists'                   => 'Số điện thoại Đã được sử dụng',
            '*.phone_number'                       => 'Số điện thoại không hơp lệ',

            'gender.required'                      => 'Giới tính không được bỏ trống',
            'gender.check_gender'                  => 'Giới tính không hợp lệ',

            'birthday.required'                    => 'Ngày sinh không được bỏ trống',
            'birthday.strdate'                     => 'Ngày sinh không hợp lệ',
            'mbti.mbti'                            => 'MBTI không hợp lệ',

            'region_id'                          => 'Khu vực không hợp lệ',
            'address.max'                          => 'Địa chỉ không hợp lệ',

            'status.required'                      => 'Trạng thái không hợp lệ',
            'status.user_status'                   => 'Trạng thái không hợp lệ',


        ];
    }
}
