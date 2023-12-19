<?php

namespace App\Http\Controllers\Merchant\General;

use App\Http\Controllers\Merchant\MerchantController;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property-read StatisticRepository $statisticRepository
 * @property-read ShowroomViewRepository $showroomViewRepository
 * @property-read ShowroomRepository $showroomRepository
 * @property-read MerchantTemplateRepository $merchantTemplateRepository
 */
class DashboardController extends MerchantController
{
    protected $module = 'dashboard';

    protected $moduleName = 'Dashboard';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $repository
    )
    {
        $this->repository = $repository;
        $this->init();
    }



    public function getIndex(Request $request)
    {
        return redirect()->route($this->routeNamePrefix . '3d.models.list');
        return $this->viewModule('default');
    }

    public function viewDefaultDashboard(Request $request)
    {
        return redirect()->route($this->routeNamePrefix . '3d.models.list');
        $data = [];
        return $this->viewModule('default', $data);
    }

}
