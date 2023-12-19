<?php

namespace App\Http\Controllers\Merchant\Promotions;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Merchant\MerchantController;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Promotions\VoucherRepository;

class VoucherController extends MerchantController
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
        $this->repository->where('vouchers.status', '!=', Voucher::STATUS_IDLE);
        $user = $request->user();
        $vendor_id = get_web_data('merchant_id',  $user->type == User::MERCHANT ? $user->id : $user->owner_id);
        // $data->vendor_id = get_web_data('merchant_id',  $vendor_id);
        $this->repository->where('campaigns.vendor_id', $vendor_id);
    }

    public function allVoucher(Request $request){
        return $this->json($this->repository->getResults($request, ['@paginate'=> null]));

    }

}
