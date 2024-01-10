<?php

namespace App\Validators\Users;

use App\Models\User;
use App\Repositories\Policies\CommissionRepository;
use App\Validators\Common\ParseInputData;
use App\Validators\Common\RemoveScriptTag;
use Gomee\Validators\Validator as BaseValidator;

class UserValidator extends BaseValidator
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
            $this->repository->removeStaffQuery();
            $result = $this->repository->first($data);
            $this->repository->staffQuery();

            if ($result) {
                if ($this->id && $this->id == $result->id) {
                    return true;
                }
                return false;
            }
            return true;
        });
        $this->addRule('phone_exists', function ($name, $value) {
            if (!$value) return true;
            if ($user = $this->repository->first(['phone_number' => $value])) {
                if ($this->id) {
                    if ($this->id == $user->id) {
                        return true;
                    }
                }
                return false;
            }
            return true;
        });

        $this->addRule('phone_number', function ($attr, $value) {
            if (!$value) return true;
            return preg_match('/^(\+84|0)+[0-9]{9,10}$/si', $value);
        });

        $this->addRule('username', function ($attr, $value) {
            return preg_match('/^[A-z]+[A-z0-9_\.]*$/si', $value);
        });
        $this->addRule('user_status', function ($attr, $value) {
            if (!$value) return true;
            if ($status = get_user_config('status_list')) {
                return isset($status[$value]);
            }
            return in_array($value, [-1, 0, 1]);
        });

        $this->addRule('check_gender', function ($attr, $value) {
            if (!$value) return true;
            return in_array(strtolower($value), ['male', 'female', 'other']);
        });

        $this->addRule('user_type', function ($attr, $value) {
            return in_array(strtolower($value), User::ALL_TYPE);
        });

        $this->addRule('check_policy', function ($attr, $value) {
            return $value && (new CommissionRepository())->count(['id' => $value]) > 0;
        });

        $this->addRule('carbon_time', function ($attr, $value) {
            return get_carbon_time($value, 'time')?true:false;
        });
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        $id = $this->id;
        $password = $this->password;

        $rules = [
            'name'                => 'required|string|max:181',
            'gender'              => 'check_gender',
            'birthday'            => 'arrdate:str',
            'address'             => 'mixed|max:180',
            'email'               => ['required', 'email', 'unique_attr', 'regex:/[a-z0-9]+@[a-z]+\.[a-z]{2,3}/'],
            'phone_number'               => 'phone_number|phone_exists',
            'avatar'              => 'mimes:jpg,jpeg,png,gif',
            // thong tin tai khoan
            'username'            => 'required|username|unique_attr|min:4|max:64',

            'type'                => 'user_type',
            'status'              => 'user_status',
            // 'upload_count'       => 'numeric|min:0|max:9000000',  //

            'affiliate_code'      => 'required|string|max:16|unique_attr', //
            'ref_code'            => 'mixed', //
            // 'wallet_balance'      => 'numeric|min:0|max:99999999',  //
            // 'agent_discount'      => 'numeric|min:0|max:100',  //
            // 'agent_expired_at'    => 'datetime:datetime',
            // 'country_code'        => 'mixed',
            // 'locale'              => 'mixed', //
            // 'mbti'                => 'mixed',
            // 'bio'                 => 'mixed', //
            // 'reason'              => 'max:191',
            // 'region_id'         => 'mixed', //
            // 'district_id'       => 'mixed', //
            // 'ward_id'           => 'mixed',
            // 'identity_card_id'    => 'mixed', //
            // 'is_verified_phone'   => 'check_boolean', //
            // 'is_verified_email'   => 'check_boolean', //
            // 'is_verified_identity'=> 'check_boolean', //
            // "bank_name"           => "mixed",
            // "bank_account_name"   => "mixed",
            // "bank_account_number" => "mixed"
        ];


        if (!($this->id?? $this->id) || strlen($this->password)) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        if($this->type == 'agent'){
            $rules['agent_policy_id'] = 'check_policy';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.max'                             => 'Họ tên dài vựa quá số ký tự',
            'name.required'                        => 'Bạn chưa nhập họ và tên',


            'username.required'                    => 'Bạn chưa nhập tên Đăng nhập',
            'username.min'                         => 'Tên người dùng phải có ít nhất 2 ký tự',
            'username.username'                    => 'Tên đăng nhập không hợp lệ',
            'username.unique_attr'                 => 'Bạn không thể dùng username này',

            'email.required'                       => 'Bạn chưa nhập email',
            'email.email'                          => 'Email không hợp lệ',
            'email.unique_attr'                    => 'Email của bạn đã tồn tại ytong hệ thống',

            'password.required'                    => 'Bạn chưa nhập mật khẩu',
            'password.min'                         => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed'                   => 'Mật khẩu không khớp',

            'phone_number.required'                => 'Số điện thoại không được bỏ trống',
            'phone_number.phone_number'            => 'Số điện thoại không hơp lệ',
            'phone_number.phone_exists'            => 'Số điện thoại Đã được sử dụng',
            '*.phone_number'                       => 'Số điện thoại không hơp lệ',

            'type.user_type'                       => 'Loại user không hợp lệ',

            'first_name.required'                  => 'Tên không được bỏ trống',

            'last_name.required'                   => 'Họ và đệm không được bỏ trống',

            'gender.required'                      => 'Giới tính không được bỏ trống',
            'gender.chevk_gender'                  => 'Giới tính không hợp lệ',

            'birthday.required'                    => 'Ngày sinh không được bỏ trống',
            'birthday.strdate'                     => 'Ngày sinh không hợp lệ',

            'address.max'                          => 'Địa chỉ không hợp lệ',

            'status.required'                      => 'Trạng thái không hợp lệ',
            'status.user_status'                   => 'Trạng thái không hợp lệ',


            'avatar.required'                      => 'Ảnh đại diện không được bỏ trống',
            'avatar.mimes'                         => 'Ảnh đại diện không đúng dịnh dạng',

            'web_type.required'                    => 'Bạn chưa chọn gói dịch vụ',
            'web_type.check_web_type'              => 'Gói dịch vụ không hợp lệ',
            'subdomain.required'                   => 'Bạn chưa nhập tên miền',
            'subdomain.check_subdomain'            => 'Tên miền này đã được sử dụng',
            'ci_cart_number.*'                     => 'Số thẻ không hợp lệ',
            'ci_card_front_scan.mimes'             => 'Định dạng file ảnh mặt trước không hợp lệ (jpg,png,gif)',
            'ci_card_back_scan.mimes'              => 'Định dạng file ảnh mặt sau không hợp lệ (jpg,png,gif)',
        ];
    }
}
