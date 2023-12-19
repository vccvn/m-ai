<?php

namespace App\Repositories\Products;

use Gomee\Repositories\BaseRepository;
/**
 * validator 
 * 
 */
use App\Validators\Products\LabelValidator;
use App\Masks\Products\ProductLabelMask;
use App\Masks\Products\ProductLabelCollection;
class LabelRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = LabelValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = ProductLabelMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = ProductLabelCollection::class;

    /**
     * @var \App\Models\ProductLabel
     */
    static $__Model__;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProductLabel::class;
    }

    public function beforeSave($data)
    {
        $data['slug'] = str_slug($data['title']);
        return $data;
    }

    

    public function getSelectOptions(array $args = [])
    {
        $data = [];

        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        if ($list = $this->notTrashed()->get($params)) {
            foreach ($list as $item) {
                $data[$item->id] = htmlentities($item->title);
            }
        }
        return $data;
    }
}