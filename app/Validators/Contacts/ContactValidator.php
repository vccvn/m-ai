<?php

namespace App\Validators\Contacts;

use Gomee\Validators\Validator as BaseValidator;

class ContactValidator extends BaseValidator
{
    public function extends()
    {

        static::extend('email_null', function ($name, $value) {
            if (is_null($value)) return true;
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        });

    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [
            'merchant_id' => 'mixed',
            'name' => 'mixed|max:191',
            'subject' => 'mixed|max:191',
            'email' => 'email_null',
            'phone_number' => 'phone_number',
            'message' => 'mixed',
            'booking_time' => 'mixed',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'merchant_id.*' => 'Merchant Không hợp lệ',
            'name.*' => 'Tên Không hợp lệ',
            'email.*' => 'Email Không hợp lệ',
            'phone.*' => 'Số điện thoại Không hợp lệ',
            'message.*' => 'Message Không hợp lệ',
            'booking_time.*' => 'Booking Time Không hợp lệ',

        ];
    }
}
