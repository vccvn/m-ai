<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Products\ProductUtilityRepository;

class ProductUtilityController extends AdminController
{
    protected $module = 'products.utilities';

    protected $moduleName = 'Tiện ích';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ProductUtilityRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductUtilityRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

}
