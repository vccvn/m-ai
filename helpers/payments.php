<?php

use App\Models\UploadPackage;
use App\Models\User;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use App\Repositories\Users\UserRepository;
use Crazy\Helpers\Arr;
use Crazy\Html\Html;

if(!function_exists('get_payment_method_options')){
    /**
     * lấy danh sách phương thức thanh toán đơn hàng
     * @return Arr[]|\App\Models\PaymentMethod[]
     */
    function get_payment_method_options($args = [])
    {
        return (new MethodRepository())->getActionedMethodDetails($args);
    }
}

if(!function_exists('get_payment_method_select_options')){
    /**
     * lấy danh sách phương thức thanh toán đơn hàng
     * @return Arr[]|\App\Models\PaymentMethod[]
     */
    function get_payment_method_select_options($args = [])
    {
        $a = ["" => "Chọn phương thức thanh toán"];

        $methods = get_payment_method_options($args);
        foreach ($methods as $key => $method) {
            $a[$method->value] = $method->name;
        }
        return $a;
    }
}

if(!function_exists('get_payment_method_id_options')){
    /**
     * lấy danh sách phương thức thanh toán đơn hàng
     * @return Arr[]|\App\Models\PaymentMethod[]
     */
    function get_payment_method_id_options($args = [], $defaltText = null)
    {
        $a = $defaltText ? ["" => $defaltText] : [];

        $methods = get_payment_method_options($args);
        foreach ($methods as $key => $method) {
            $a[$method->id] = $method->name;
        }
        return $a;
    }
}



if(!function_exists('get_atm_bank_options')){
    /**
     * lấy danh sách phương thức thanh toán đơn hàng
     * @return array
     */
    function get_atm_bank_options($options, $asset = '')
    {
        $values = [];
        $a = rtrim($asset, '/') . '/';
        foreach ($options as $key => $value) {
            $values[$value->code] = '<span class="text-with-icon">'.
                '<span class="icon">'.
                    '<img class="img-icon" src="'.$a.$value->logo.'" />'.
                '</span>'.
                '<span class="text">'.$value->title.'</span>'.
            '</span>';
        }
        return $values;
    }
}

if(!function_exists('get_payment_packages')){
    /**
     * lay thong tin goi
     *
     * @param array $args
     * @return UploadPackage[]
     */
    function get_payment_packages($args = []){
        static $packages = [];
        if(!$packages){
            $user = auth()->user();
            if($user->agent_id && $agent = app(UserRepository::class)->first(['id'=>$user->agent_id, 'type' => User::AGENT])){
                $args = array_merge($args, ['role' => User::AGENT, 'agent_id' => $agent->id]);
            }
            else{
                $args = array_merge($args, ['role' => 'system']);
            }
            $packages = (new PackageRepository())->get($args);
        }
        return $packages;
    }
}

if(!function_exists('get_payment_package_options')){
    function get_payment_package_options($args = [], $defaltText = null, $defaultValue = ''){
        static $packages = [];
        if(!$packages){
            if($defaltText){
                if($defaultValue)
                    $packages[$defaultValue] = $defaltText;
                else
                    $packages[''] = $defaltText;
            }
            if(count($p = get_payment_packages($args))){
                foreach ($p as $key => $pk) {
                    $packages[$pk->id] = $pk->name;
                }
            }
        }
        return $packages;
    }
}


if(!function_exists('get_payment_package_option_docs')){
    function get_payment_package_option_docs($args = []){
        static $packages = [];
        if(!$packages){
            if(count($p = get_payment_packages($args))){
                foreach ($p as $key => $pk) {
                    $packages[$pk->id] = [
                        'title'=> $pk->name,
                        'label' => $pk->discount ? '-' . $pk->discount . '%': '',
                        'description' => '<div class="">' . $pk->quantity . ' tháng</div>'
                            .'<div class="text-danger">Giá: ' . get_price_format($pk->wholesale_price, $pk->currency) . '</div>'
                            . ($pk->description ? '<div class="mt-3">' .$pk->description. '</div>': '')
                    ];
                }
            }
        }
        return $packages;
    }
}



if (!function_exists('get_currency_format')) {
    /**
     * lấy thông tin tiền tệ
     * @param int|float|double $total
     * @return string
     */
    function get_currency_format($total)
    {

        // sau này thiết lập sau
        static $ecommerce = null;
        // if (!$ecommerce) $ecommerce = get_ecommerce_setting();
        if ($total < 0) return 'Liên Hệ';
        $decimals = 0;
        $dsc_point = ',';
        $thousands_sep = ',';
        $fm = number_format($total, $decimals, $dsc_point, $thousands_sep);
        $currency_type = 'Đ';
        return $currency_type ?  $fm . ' ' . $currency_type : $fm;
    }
}


if (!function_exists('get_currency_unit')) {
    /**
     * lấy thông tin đơn vị tiền tệ
     * @param int|float|double $total
     * @return string
     */
    function get_currency_unit($default = null)
    {
        // sau này thiết lập sau
        static $unit = null;
        if (!$unit) {
            $unit = get_ecommerce_setting()->currency_type;
            if (!$unit) $unit = $default;
        }
        return $unit;
    }
}

if(!function_exists('get_price_format')){
    function get_price_format($total, $currency = null){
        if(!$currency) $currency = 'VND';
        $price = $currency == 'VND' ? number_format($total, 0, ',', '.') : number_format($total, 2);
        if($currency == 'VND') return $price . '' . __('currency.'.$currency);
        return  __('currency.'.$currency) . $price;
    }
}
if(!function_exists('get_currency_options')){
    function get_currency_options(){

        $data = __('currency');
        $options = [];
        foreach ($data as $key => $value) {
            $options[$key] = "$key ($value)";
        }
        return $options;
    }
}
