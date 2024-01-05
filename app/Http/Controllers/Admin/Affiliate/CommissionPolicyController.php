<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Policies\CommissionRepository;

class CommissionPolicyController extends AdminController
{
    protected $module = 'policies.commissions';

    protected $moduleName = 'Chính sách hoa hầng';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var CommissionRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CommissionRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
        $this->activeMenu('policies');
    }

}
