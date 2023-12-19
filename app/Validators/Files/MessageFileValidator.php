<?php

namespace App\Validators\Files;

use Gomee\Validators\Validator as BaseValidator;

class MessageFileValidator extends BaseValidator
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
            'file_name' => 'mixed|max:191',
            'file_data' => 'base64_file:image,video,audio',
            'file_data_list' => 'base64_list:image,video,audio'

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [
            'file_data.*' => 'Dữ liệu file không hợp lệ',
            'file_data_list.*' => 'Dữ liệu file không hợp lệ'
        ];
    }
}
