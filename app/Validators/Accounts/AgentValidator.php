<?php

namespace App\Validators\Accounts;

use Gomee\Validators\Validator as BaseValidator;

class AgentValidator extends BaseValidator
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
            'policy_id' => 'integer',
            'month_balance' => 'integer',
            'commission_level_1' => 'float',
            'commission_level_2' => 'float',
            'commission_level_3' => 'float',
            'commission_level_4' => 'float',
            'bank_name' => 'string',
            'bank_brand' => 'string',
            'bank_account_name' => 'string',
            'bank_account_id' => 'string',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            
            'user_id.integer' => 'User Id Không hợp lệ',
            'policy_id.integer' => 'Policy Id Không hợp lệ',
            'month_balance.integer' => 'Month Balance Không hợp lệ',
            'commission_level_1.float' => 'Commission Level 1 Không hợp lệ',
            'commission_level_2.float' => 'Commission Level 2 Không hợp lệ',
            'commission_level_3.float' => 'Commission Level 3 Không hợp lệ',
            'commission_level_4.float' => 'Commission Level 4 Không hợp lệ',
            'bank_name.string' => 'Bank Name Không hợp lệ',
            'bank_brand.string' => 'Bank Brand Không hợp lệ',
            'bank_account_name.string' => 'Bank Account Name Không hợp lệ',
            'bank_account_id.string' => 'Bank Account Id Không hợp lệ',

        ];
    }
}