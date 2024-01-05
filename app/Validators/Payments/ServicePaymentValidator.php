<?php

namespace App\Validators\Payments;

use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Gomee\Validators\Validator as BaseValidator;

class ServicePaymentValidator extends BaseValidator
{
    public function extends()
    {
        $this->addRule('check_payment_method', function($attr, $value){
            return !$value || app(MethodRepository::class)->count(['id' => $value]) == 1;
        });
        $this->addRule('check_package', function($attr, $value){
            return !$value || app(PackageRepository::class)->count(['id' => $value]) == 1;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [
            'order_id'        => 'check_package',
            'payment_method_id' => 'check_payment_method',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'order_id.*'        => 'Gói thanh toám không hợp lệ',
            'payment_method_id.*' => 'Phương thức thanh toán không hợp lệ',

        ];
    }
}
