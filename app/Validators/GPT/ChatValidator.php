<?php

namespace App\Validators\GPT;

use Gomee\Validators\Validator as BaseValidator;

class ChatValidator extends BaseValidator
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
            
            'chat_id' => 'integer',
            'user' => 'string',
            'message' => 'mediumText',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'chat_id.integer' => 'Chat Id Không hợp lệ',
            'user.string' => 'User Không hợp lệ',
            'message.mediumText' => 'Message Không hợp lệ',

        ];
    }
}