<?php

namespace App\Http\Controllers\Admin\Payments;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\PackageRepository;
use App\Validators\Payments\SystemPackageValidator;

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
        $this->repository->addDefaultParam('role', 'system')->addDefaultValue('role', 'system')->setValidatorClass(SystemPackageValidator::class);

    }

    public function afterSave($request, $result)
    {

    }
}
