<?php

namespace App\Validators\Accounts;

use Gomee\Validators\Validator as BaseValidator;

class WalletValidator extends BaseValidator
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
            'balance' => 'float',
            'money_in' => 'float',
            'money_out' => 'float',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'user_id.integer' => 'User Id Không hợp lệ',
            'balance.float' => 'Balance Không hợp lệ',
            'money_in.float' => 'Money In Không hợp lệ',
            'money_out.float' => 'Money Out Không hợp lệ',

        ];
    }
}