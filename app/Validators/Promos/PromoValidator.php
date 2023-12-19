<?php

namespace App\Validators\Promos;

use App\Models\Promo;
use Carbon\Carbon;
use Gomee\Validators\Validator as BaseValidator;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Users\UserRepository;

class PromoValidator extends BaseValidator
{
    public function extends()
    {
        $this->addRule('check_type', function ($prop, $value) {
            return (!array_key_exists($value, get_promo_type_options())) ? false : (
                (($this->scope == 'product' && $value == Promo::TYPE_FREESHIP) || (in_array($this->scope, ['user', 'order']) && $value == Promo::TYPE_SAME_PRICE)) ? false : true
            );
        });
        $this->addRule('check_down_price', function ($prop, $value) {
            if (!$value) return true;
            if (!is_numeric($value) || strlen($value) > 10) return false;

            return ($this->type == Promo::TYPE_DOWN_PERCENT && $value > 100) ? false : true;
        });

        $this->addRule('check_products', function ($prop, $value) {
            if (!$value) return true;
            if (!is_array($value)) return false;

            return (count($value) == (new ProductRepository())->count(['id' => $value]));
        });

        $this->addRule('check_users', function ($prop, $value) {
            if (!$value) return true;
            if (!is_array($value)) return false;

            return (count($value) == (new UserRepository())->count(['id' => $value]));
        });

        $this->addRule('check_time_lt_now', function ($prop, $value) {
            if (is_null($value)) return true;
            // if (count($date = array_map('trim', explode(' - ', $value))) == 2) {
            //     $time = [
            //         'from' => $date[0],
            //         'to'   => $date[1]
            //     ];
            //     $now  = Carbon::now();
            //     if (Carbon::parse($time['from'])->lt($now)) {
            //         return false;
            //     }
            // }

            return true;
        });


    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [
            'name'        => 'required|string|max:191|unique_prop',
            'description' => 'max:500',
            'scope'       => 'in_list:order,product,user',
            'type'        => 'check_type',
            'is_activated'=> 'check_boolean',
            'down_price'  => 'required|numeric|min:0|max:100000000|check_down_price',
            'code'        => 'max:32',
            'times'       => 'required|datetimerange|check_time_lt_now'
        ];
        if ($this->scope == 'order') {
            $rules = array_merge($rules, [
                'limited_total' => 'required|numeric|min:1'
            ]);
        } elseif ($this->scope == 'user') {
            $rules = array_merge($rules, [
                'quantity_per_user' => 'required|numeric|min:1',
                'user_list'         => 'required|check_users'
            ]);
        } else {
            $rules = array_merge($rules, [
                'products' => 'check_products',

            ]);
        }

        if ($this->schedule) {
            $rules = array_merge($rules, [
                'schedule'       => 'required',
                'type_schedule'  => 'required|in:day,week,month,year',
                'value_schedule' => 'required|numeric|min:1|max:1000',
            ]);
        }


        return $rules;
        // return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'name.required'               => 'Tên chương trình khuyến mãi không được bỏ trống',
            'name.string'                 => 'Tên chương trình khuyến mãi không hợp lệ',
            'name.max'                    => 'Tên chương trình khuyến mãi hơi... dài!',
            'name.unique_prop'            => 'Tên chương trình khuyến mãi bị trùng lặp',
            'description.max'             => 'Mô tả hơi dài',
            'down_price.check_down_price' => 'Mức khuyến mãi',
            'code.max'                    => 'Mã khuyến mãi không được vượt quá 32 ký tự',
            'products.check_products'     => 'Hình như có một vài sản phẩm không hợp lệ',
            'times.datetimerange'         => 'Thời gian khuyến mãi không hợp lệ',
            'times.check_time_lt_now'     => 'Thời gian bắt đầu khuyến mại phải lớn hơn thời gian hiện tại',
            'limited_total.required'      => 'Vui lòng nhập số lượng khuyến mãi',
            'limited_total.numeric'       => 'Số lượng khuyến mãi phải là kiểu số',
            'limited_total.min'           => 'Số lượng khuyến mãi phải lớn hơn hoặc bằng 1',
            'quantity_per_user.required'  => 'Vui lòng nhập số lượng áp dụng cho mỗi người',
            'quantity_per_user.numeric'   => 'Số lượng áp dụng cho mỗi người phải là kiểu số',
            'quantity_per_user.min'       => 'Số lượng áp dụng cho mỗi người phải lớn hơn hoặc bằng 1',
            'user_list.*'                 => 'Danh sách người dùng ko hợp lệ',
            'type_schedule.required'      => 'Trường chọn chu kỳ là bắt buộc',
            'value_schedule.required'     => 'Trường giá trị chu kỳ là bắt buộc',
            'value_schedule.numeric'      => 'Trường giá trị chu kỳ phải là một số',
            'value_schedule.min'          => 'Trường giá trị chu kỳ phải phải lớn hơn hoặc bằng 1'
        ];
    }
}