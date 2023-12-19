<?php

namespace App\Repositories\Products;

use Gomee\Repositories\BaseRepository;
/**
 * validator 
 * 
 */
use App\Validators\Products\FilterValidator;
use App\Masks\Products\ProductFilterMask;
use App\Masks\Products\ProductFilterCollection;
class FilterRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = FilterValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = ProductFilterMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = ProductFilterCollection::class;

    /**
     * @var \App\Models\ProductFilter
     */
    static $__Model__;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProductFilter::class;
    }

    public function init()
    {
        $this->setJoinable([
            ['leftJoin', 'categories', 'categories.id', '=', 'product_filters.category_id']
        ]);
        $raw = [
            'category_id', 'name' , 'description', 'search',
        ];
        $columns = [
            'category_name' => 'categories.name',
            'category_keywords' => 'categories.keywords',
        ];
        $this->setSelectable(array_merge($columns, ['product_filters.*']));
        $this->setSearchable(array_merge($columns, [
            'name' => 'product_filters.name',
            'search' => 'product_filters.search'
        ]));
        foreach ($raw as $col) {
            $columns[$col] = 'product_filters.' . $col;
        }
        $this->setSortable($columns);

    }
}