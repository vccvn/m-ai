<?php

namespace App\Validators\Payments;

use Gomee\Engines\Helper;
use Gomee\Validators\Validator as BaseValidator;

class PackagePaymentValidator extends BaseValidator
{
    public $methods = [];
    public function extends()
    {
        $this->methods = Helper::getPaymentMethodOptions();
        $this->addRule('check_method', function($attr, $value){
            if(!$value) return false;

            $s = false;
            if($this->methods && count($this->methods)){
                foreach ($this->methods as $key => $method) {
                    if($key == $value || $method->method == $value) $s = true;
                }
            }
            return $s;
        });

    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        $rules = [
            'payment_method' => 'check_method'
        ];
        return $rules;
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'payment_method' => 'Phương thức thanh toán không hợp lệ',
            'vnpay_bank' => 'Ngân hàng / Ví diện tử không hợp lệ'

        ];
    }
}
