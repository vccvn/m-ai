<?php

namespace App\Validators\GPT;

use App\Repositories\GPT\TopicRepository;
use Gomee\Validators\Validator as BaseValidator;

/**
 * @property TopicRepository $repository
 */
class TopicValidator extends BaseValidator
{
    public function extends()
    {
        // Thêm các rule ở đây
        $this->addRule('check_parent', function($prop, $value){
            if(!$value) return true;
            return $this->repository->count(['id' => $value]) ? true : false;
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [

            'name'                             => 'required|string|max:191|unique_prop',
            'parent_id'                        => 'check_parent',
            'description'                      => 'mixed|max:500',
            'keywords'                         => 'mixed|max:180',

        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'name.required'                    => 'Tên danh mục không được bỏ trống',
            'name.string'                      => 'Tên danh mục không hợp lệ',
            'name.max'                         => 'Tên danh mục hơi... dài!',
            'name.unique_prop'                 => 'Tên danh mục bị trùng lặp',
            'featured_image.mimes'             => 'Định đạng file không được hỗ trợ',
            'parent_id.check_parent'           => 'Danh mục cha không hợp lệ',


        ];
    }
}
