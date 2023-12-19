<?php

namespace App\Validators\Users;

use App\Repositories\Locations\RegionRepository;
use App\Repositories\MBTI\DetailRepository;
use Gomee\Validators\Validator as BaseValidator;

class EKYCValidator extends BaseValidator
{
    public function extends()
    {

    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [
            'ci_front_file'           => 'base64_file:jpeg,jpg,png,gif',
            'ci_back_file'           => 'base64_file:jpeg,jpg,png,gif',
        ];

    }

    public function messages()
    {
        return [

            'ci_front_file.*' => 'Ảnh mặt trước không hợp lệ',
            'ci_back_file.*' => 'Ảnh mặt trước không hợp lệ',



        ];
    }
}
