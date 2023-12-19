<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;
use App\Models\ProductFilter;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Products\FilterRepository;
use App\Repositories\Tags\TagRefRepository;

class ProductFilterController extends AdminController
{
    protected $module = 'products.filters';

    protected $moduleName = 'Product List Filter';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var FilterRepository
     */
    public $repository;
    
    /**
     * @var TagRefRepository $tagRefRepository
     */
    protected $tagRefRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FilterRepository $repository, TagRefRepository $tagRefRepository)
    {
        $this->repository = $repository;
        
        $this->tagRefRepository = $tagRefRepository;

        $this->init();
    }

    
    /**
     * lưu các dữ liệu liên quan như thuộc tính, meta, gallery
     * @param Request $request
     * @param ProductFilter $product
     * @param Crazy\Helpers\Arr $data dữ liệu từ input đã dược kiểm duyệt
     *
     * @return void
     */
    public function afterSave(Request $request, $product, $data)
    {
        $this->tagRefRepository->updateTagRef('product-filter', $product->id, $data->tags??[]);
    }

    

    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', get_product_category_map());
    }
}
