<?php

namespace App\Validators\Payments;

use Gomee\Validators\Validator as BaseValidator;

class SystemPackageValidator extends BaseValidator
{
    public function extends()
    {
        $this->addRule('check_currency', function($attr, $value){
            if(!$value) return false;
            return array_key_exists(strtoupper($value), __('currency'));
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [

            'name' => 'required|string|max:191',
            'description' => 'mixed|max:800',
            'feature_text' => 'mixed',
            'quantity' => 'required|numeric|min:1',
            'wholesale_price' => 'required|numeric|min:0|max:999999999',
            'retail_price' => 'required|numeric|min:0|max:999999999',
            'currency' => 'check_currency',
            // 'discount' => 'numeric|min:0|max:100',
            // "is_default" => 'check_boolean'

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'name.*' => 'Tên gói Không hợp lệ',
            'description.*' => 'description Không hợp lệ',
            'quantity' => [
                'required' => 'Số tháng không được bỏ trống',
                'numeric' => 'Số tháng phải là kiểu số',
                'min' => 'Số tháng tối thiểu là 1',

            ],
            'wholesale_price' =>[
                'required' => 'Giá không được bỏ trống',
                'numeric' => 'Giá phải là kiểu số',
                'min' => 'Giá tối thiểu là 0',
                'max' => 'Giá cao nhất là 999999999'

            ],
            'retail_price' =>[
                'required' => 'Giá không được bỏ trống',
                'numeric' => 'Giá phải là kiểu số',
                'min' => 'Giá tối thiểu là 0',
                'max' => 'Giá cao nhất là 999999999'

            ],
            'currency.*' => 'Đơn vị tiền tệ Không hợp lệ',

        ];
    }
}
