<?php

namespace App\Validators\Policies;

use Gomee\Validators\Validator as BaseValidator;

class CommissionValidator extends BaseValidator
{
    public function extends()
    {
        // Thêm các rule ở đây
        $this->addRule('unique_user_type', function($attr, $value){
            if($value == 'agent') return true;
            $policy = $this->repository->first(['type' => $value]);
            if(!$policy) return true;
            return $this->id && $this->id == $policy->id;
        });
        $this->addRule('unique_level', function($attr, $value){
            if($this->type == 'user') return true;
            $policy = $this->repository->first(['level' => $value, 'type' => $this->type]);
            if(!$policy) return true;
            return $this->id && $this->id == $policy->id;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        $rules = [

            'name' => 'required|string|max:191',
            'description' => 'mixed|max:800',
            'receive_times' => 'required|numeric|min:-1',
            'type' => 'in_list:user,agent|unique_user_type',
            'commission_level_1' => 'numeric|min:0|max:100',
            'commission_level_2' => 'numeric|min:0|max:100',
            'commission_level_3' => 'numeric|min:0|max:100',
            'commission_level_4' => 'numeric|min:0|max:100',

        ];
        if($this->type == 'agent'){
            $rules = array_merge($rules, [
                'level' => 'numeric|min:0|max:10|unique_level',
                'revenue_target' => 'numeric|min:0|max:999999999'
            ]);
        }
        return $rules;
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'name.*' => 'Tên Không hợp lệ',
            'description.*' => 'Mô tả Không hợp lệ',
            'receive_times' => [
                'required' => 'Số lần không được bỏ trống',
                'numeric' => 'Số lần phải là kiểu số',
                'min' => 'Số lần tối thiểu là -1',

            ],
            'type' => [
                'required' => 'Đối tượng không được bỏ trống',
                'in_list' => 'Đối tượng không hợp lệ',
                'unique_user_type' => 'Chính sách cho người giới thiệu chỉ đã tồn tại',

            ],
            'level' => [
                'numeric' => 'Cấp độ phải là kiểu số',
                'min' => 'Cấp độ tối thiểu là -1',
                'max' => 'Cấp độ tối thiểu là -1',
                'unique_level' => 'Chính sách này đã tồn tại',

            ],
            'revenue_target' => [
                'required' => 'Chỉ tiêu không được bỏ trống',
                'numeric' => 'Chỉ tiêu phải là kiểu số',
                'min' => 'Chỉ tiêu tối thiểu là 0',
                'max' => 'Chỉ tiêu có giá trị tối đa là 999.999.999',

            ],
            'commission_level_1.*' => 'Giá trị không hợp lệ. (0 - 100)',
            'commission_level_2.*' => 'Giá trị không hợp lệ. (0 - 100)',
            'commission_level_3.*' => 'Giá trị không hợp lệ. (0 - 100)',
            'commission_level_4.*' => 'Giá trị không hợp lệ. (0 - 100)',

        ];
    }
}
