<?php

namespace App\Http\Controllers\Merchant\Products;

use App\Http\Controllers\Merchant\MerchantController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Products\ProductAffiliateRepository;

class ProductAffiliateController extends MerchantController
{
    protected $module = 'products.affiliates';

    protected $moduleName = 'San pham lien ket';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ProductAffiliateRepository
     */
    public $repository;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProductAffiliateRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

}
