<?php

namespace App\Validators\GPT;

use Gomee\Validators\Validator as BaseValidator;

class PromptImportValidator extends BaseValidator
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

            'topic_id'                      => 'required|numeric|exists:gpt_topics,id',
            'import_file'                   => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/excel,application/vnd.ms-excel,application/x-msexcel'


        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'topic_id.*' => 'Topic Id Không hợp lệ',
            'import_file.*' => 'File Không hợp lệ',
            'keywords.*' => 'Keywords Không hợp lệ',
            'description.*' => 'Description Không hợp lệ',
            'prompt.*' => 'Prompt Không hợp lệ',

        ];
    }
}
