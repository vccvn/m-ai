<?php

namespace App\Http\Controllers\Admin\Payments;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\PackageRepository;

class PackageController extends AdminController
{
    protected $module = 'payments.packages';

    protected $moduleName = 'Package';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var PackageRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PackageRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
        $this->activeMenu('payments');

    }

    public function afterSave($request, $result)
    {
        if($result->is_default){
            $packages = $this->repository->where('id', '!=', $result->id)->where('is_default', true)->get();
            if(count($packages)){
                foreach ($packages as $package) {
                    $package->is_default = false;
                    $package->save();
                }
            }
        }
    }
}
