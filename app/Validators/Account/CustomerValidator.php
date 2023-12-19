<?php

namespace App\Validators\Account;

use App\Repositories\Users\UserRepository;
use Gomee\Validators\Validator as BaseValidator;
class CustomerValidator extends BaseValidator
{
    public function extends()
    {
        // Thêm các rule ở đây
        $this->addRule('check_mail_unq', function($attr, $value){
            if($customer = $this->repository->first(['email' => $value])){
                if(($user = $this->request->user()) && $customer->user_id == $user->id) return true;
                return false;
            }
            return true;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        // $id = $this->user_id;
        return [
            // 'user_id'                              => 'check_user',
            'name'                                 => 'required|string|max:191',
            'address'                              => 'mixed|max:180',
            'email'                                => 'email|check_mail_unq',
            'phone_number'                         => 'phone_number',
            'balance'                              => 'check_balance'
        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'user_id.check_user'                   => 'Người dùng không hợp lệ',
            'name.max'                             => 'Họ tên dài vựa quá số ký tự',
            'name.required'                        => 'Bạn chưa nhập họ và tên',

            'email.required'                       => 'Bạn chưa nhập email',
            'email.email'                          => 'Email không hợp lệ',
            'email.check_mail_unq'                    => 'Email đã tồn tại',

            'phone_number.required'                => 'Số điện thoại không được bỏ trống',
            'phone_number.phone_number'            => 'Số điện thoại không hơp lệ',
            '*.phone_number'                       => 'Số điện thoại không hơp lệ',
        ];
    }
}