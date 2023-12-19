<?php

namespace App\Http\Controllers\Admin\Promotions;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Campaign;
use App\Repositories\Products\ProductUtilityRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Promotions\CampaignRepository;
use App\Repositories\Promotions\VoucherUtilityRepository;
use Carbon\Carbon;

class CampaignController extends AdminController
{
    protected $module = 'promotions.campaigns';

    protected $moduleName = 'Chiến dịch quảng cáo';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var CampaignRepository
     */
    public $repository;

    /**
     * Product Utility
     *
     * @var ProductUtilityRepository
     */
    public $productUtilityRepository = null;

    /**
     * Product Utility
     *
     * @var VoucherUtilityRepository
     */
    public $voucherUtilityRepository = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CampaignRepository $repository, ProductUtilityRepository $productUtilityRepository, VoucherUtilityRepository $voucherUtilityRepository)
    {
        $this->repository = $repository;
        $this->productUtilityRepository = $productUtilityRepository;
        $this->voucherUtilityRepository = $voucherUtilityRepository;
        $this->init();
    }


    /**
     * can thiệp xử lý trước khi lưu
     * @param Request $request
     * @param Arr $data
     * @param Campaign $old
     * @return void
     */
    public function beforeSave(Request $request, Arr $data, $old = null)
    {
        $times = $data->times;
        $to = $times['to'] ?? Carbon::now()->addDays(2)->toDateTimeLocalString('millisecond');
        $data->merge([
            'started_at' => $times['from'] ?? Carbon::now()->toDateTimeLocalString('millisecond'),
            'expired_at' => $to,

        ]);
        if ($old) {
            if (Carbon::parse($to)->getTimestamp() > Carbon::now()->getTimestamp() && $old->status == Campaign::STATUS_EXPIRED) {
                $data->status = Campaign::STATUS_RUNNING;
            }
        } else {
            $data->status = Campaign::STATUS_RUNNING;
        }

        $customer_config = [];
        $ages = [];
        $genders = [];
        $types = [];
        if ($cca = $data->customer_config_ages) {
            if (is_array($cca)) {
                $ages['from'] = $cca['from'] ?? ($cca[0] ?? Campaign::AGE_DEFAULT_FROM);
                $ages['to'] = $cca['to'] ?? ($cca[1] ?? Campaign::AGE_DEFAULT_TO);
            } elseif (count($ccp = explode(';', $cca)) == 2) {
                $ages['from'] = $ccp[0] ?? Campaign::AGE_DEFAULT_FROM;
                $ages['to'] = $ccp[1] ?? Campaign::AGE_DEFAULT_TO;
            } else {
                $ages['from'] = Campaign::AGE_DEFAULT_FROM;
                $ages['to'] = Campaign::AGE_DEFAULT_TO;
            }
        } else {
            $ages['from'] = Campaign::AGE_DEFAULT_FROM;
            $ages['to'] = Campaign::AGE_DEFAULT_TO;
        }
        if (is_array($gs = $data->customer_config_genders)) {
            $genders = array_values($gs);
        }
        if (is_array($ts = $data->customer_config_types)) {
            $types = array_values($ts);
        }
        $customer_config = [
            'ages' => $ages,
            'genders' => $genders,
            'types'=> $types,
        ];

        $data->customer_config = $customer_config;

        if ($data->address) {
            $data->keywords = $data->address . ' | ' . vntolower($data->address) . ' | ' . str_slug($data->address) . ' | ' . str_replace('-', ' ', str_slug($data->address)) . ' | ' . str_replace('-', '', str_slug($data->address));
        }
        // dd($data->all());
    }

    public function beforeGetCrudForm($request, $config, $inputs, $data, $attributes)
    {
        // dd($data);
    }

    public function afterSave($request, $result)
    {
        $this->voucherUtilityRepository->updateCampaignUtilities($result->id, is_array($a = $request->utilities) ? $a : []);
    }

    public function beforeGetListView($request, $data)
    {
        add_js_data('campaign_data', [
            'urls' => [
                'run' => $this->getModuleRoute('run'),
                'stop' => $this->getModuleRoute('stop'),
            ]
        ]);
    }

    public function stopCampaign(Request $request)
    {
        extract($this->apiDefaultData);
        if (!$request->id || !$this->repository->count(['id' => $request->id]))
            $message = 'Không có thông tin chiến chịch';
        elseif (!($campaign = $this->repository->update($request->id, ['status' => Campaign::STATUS_STOPPED])))
            $message = 'Không thể update chiến dịch ';
        else {
            $status = true;
            $data = $campaign;
            $data->status_label = $campaign->getStatusLabel();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function runCampaign(Request $request)
    {
        extract($this->apiDefaultData);
        if (!$request->id || !($c = $this->repository->first(['id' => $request->id])))
            $message = 'Không có thông tin chiến chịch';
        elseif ($c->status == Campaign::STATUS_EXPIRED)
            $message = 'Chiến dịch đã hệt thời hạn hoạt động';
        elseif (!($campaign = $this->repository->update($request->id, ['status' => Campaign::STATUS_RUNNING])))
            $message = 'Không thể update chiến dịch ';
        else {
            $status = true;
            $data = $campaign;
            $data->status_label = $campaign->getStatusLabel();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
}
