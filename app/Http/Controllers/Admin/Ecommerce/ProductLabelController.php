<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Products\LabelRepository;

class ProductLabelController extends AdminController
{
    protected $module = 'products.labels';

    protected $moduleName = 'Nhãn sản phẩm';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var LabelRepository
     */
    public $repository;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LabelRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

}
