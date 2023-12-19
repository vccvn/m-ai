<?php

namespace App\Validators\GPT;

use Gomee\Validators\Validator as BaseValidator;

class PromptValidator extends BaseValidator
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
            'name'                             => 'required|string|max:191|unique_prop',
            'description'                      => 'mixed|max:500',
            'keywords'                         => 'mixed|max:180',
            'prompt'                           => 'mixed',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'topic_id.*' => 'Topic Id Không hợp lệ',
            'name.*' => 'Name Không hợp lệ',
            'keywords.*' => 'Keywords Không hợp lệ',
            'description.*' => 'Description Không hợp lệ',
            'prompt.*' => 'Prompt Không hợp lệ',

        ];
    }
}
