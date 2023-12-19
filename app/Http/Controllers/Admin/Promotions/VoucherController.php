<?php

namespace App\Http\Controllers\Admin\Promotions;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Promotions\VoucherRepository;

class VoucherController extends AdminController
{
    protected $module = 'promotions.vouchers';

    protected $moduleName = 'Voucher';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var VoucherRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VoucherRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function beforeGetListData($request)
    {
        // $this->repository->where('vouchers.status', '!=', Voucher::STATUS_IDLE);
    }

    public function allVoucher(Request $request){
        return $this->json($this->repository->getResults($request, ['@paginate'=> null]));

    }

}
