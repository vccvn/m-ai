<?php

namespace App\Validators\Products;

use App\Repositories\Products\CategoryRepository;
use App\Repositories\Tags\TagRepository;
use Gomee\Validators\Validator as BaseValidator;

class FilterValidator extends BaseValidator
{
    public function extends()
    {

        $this->tags = new TagRepository();

        $this->addRule('check_category', function ($prop, $value) {
            if (!$value) return true;
            return app(CategoryRepository::class)->count(['id' => $value]) == 1;
        });
        $this->addRule('check_sorttype', function ($prop, $value) {
            if (!$value) return true;
            return array_key_exists($value, get_product_sortby_options());
        });

        // kiểm tra thẻ xem có hợp lệ hay ko
        $this->addRule('check_tags', function($prop, $value){
            if(!$value) return true;
            if($value && !is_array($value)) return false;
            return $this->tags->count(['id' => $value]) == count($value);
        });
    }

    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {

        return [

            'name' => 'required|string|max:150',
            'description' => 'mixed|max:300',
            'category_id' => 'check_category',
            'sorttype' => 'check_sorttype',
            'search' => 'mixed|max:180',
            'tags'                             => 'check_tags',
        ];
    }

    /**
     * các thông báo
     */
    public function messages()
    {
        return [

            'name.required'                    => 'Tên sản phẩm không được bỏ trống',
            'name.string'                      => 'Tên sản phẩm không hợp lệ',
            'name.max'                         => 'Tên sản phẩm hơi... dài!',
            'tags.check_tags'                  => 'Thẻ không hợp lệ',
            'description.mixed' => 'description Không hợp lệ',
            'sorttype.*' => 'Kểu sắp xếp Không hợp lệ',
            'search.mixed' => 'search Không hợp lệ',

        ];
    }
}
