<?php

namespace App\Http\Controllers\Merchant\Location;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Merchant\MerchantController;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Locations\PlaceTypeRepository;

class PlaceTypeController extends MerchantController
{
    protected $module = 'location.place-types';

    protected $moduleName = 'Loại Place';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var PlaceTypeRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PlaceTypeRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

}
