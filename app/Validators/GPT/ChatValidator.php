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
            
            'user_id' => 'integer',
            'prompt_id' => 'integer',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'user_id.integer' => 'User Id Không hợp lệ',
            'prompt_id.integer' => 'Prompt Id Không hợp lệ',

        ];
    }
}