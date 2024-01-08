<?php

namespace App\Validators\Accounts;

use Gomee\Validators\Validator as BaseValidator;

class CoverLetterValidator extends BaseValidator
{
    public function extends()
    {
        // Thêm các rule ở đây
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
    
        return [
            
            'user_id' => 'integer',
            'message' => 'string',
            'status' => 'integer',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'user_id.integer' => 'User Id Không hợp lệ',
            'message.string' => 'Message Không hợp lệ',
            'status.integer' => 'Status Không hợp lệ',

        ];
    }
}