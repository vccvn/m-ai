<?php

namespace App\Http\Controllers\Merchant\Payments;

use App\Http\Controllers\Merchant\MerchantController;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\PackageRepository;
use App\Validators\Payments\AgentPackageValidator;
use App\Validators\Payments\SystemPackageValidator;

class PackageController extends MerchantController
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
        $this->repository->addDefaultParam('role', 'agent')->addDefaultValue('role', 'agent')->setValidatorClass(AgentPackageValidator::class);
    }

    public function beforeGetListData($request)
    {
        $this->repository->where('agent_id', $request->user()->id);
    }
    public function beforeGetDetailData($request)
    {
        $this->repository->where('agent_id', $request->user()->id);
    }

    public function beforeGetListView($request, $data)
    {
        // dd($data);
    }

    public function beforeSave($request, $data, $old = null)
    {
        $data->agent_id = $request->user()->id;
    }
    public function afterSave($request, $result)
    {
    }
}
