<?php

namespace App\Http\Controllers\Web\Payments;

// use App\Http\Controllers\Apis\ApiController;

use App\Http\Controllers\Web\WebController;
use App\Models\User;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Illuminate\Http\Request;

use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Payments\AlePayService;
use App\Services\Payments\PaymentService;
use App\Services\Users\UserService;
use App\Validators\Payments\ServicePaymentValidator;
use Gomee\Files\Filemanager;

/**
 * @property-read UserRepository $userRepository
 * @property-read PackageRepository $packageRepository
 * @property-read MethodRepository $methodRepository
 * @property-read TransactionRepository $transactionRepository
 * @property-read AlePayService $alepayService
 * @property-read PaymentService $paymentService
 * @property-read Filemanager $filemanager
 */
class PaymentServiceController extends WebController
{
    protected $module = 'payments';

    protected $moduleName = 'Payment';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var RequestRepository
     */
    public $repository;

    protected $errorMessage = null;

    protected $alepayConfig = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        PackageRepository $packageRepository,
        MethodRepository $methodRepository,
        TransactionRepository $transactionRepository,
        AlePayService $alepayService,
        PaymentService $paymentService,
        Filemanager $filemanager,
        protected UserService $userService
    ) {
        $this->alepayService          = $alepayService;
        $this->userRepository         = $userRepository;
        $this->methodRepository       = $methodRepository;
        $this->packageRepository      = $packageRepository;
        $this->transactionRepository  = $transactionRepository;
        $this->filemanager            = $filemanager;
        $this->paymentService         = $paymentService;
        $this->init();
        if ($alepay = $this->methodRepository->first(['method' => 'alepay'])) {
            $config = $alepay->getConfigData();
            $this->alepayConfig = $config;
            $this->alepayService->setToken($config->token_key(config('payment.alepay.token')));
            $this->alepayService->setChecksum($config->checksum_key(config('payment.alepay.checksum')));
            if ($baseURL = $config->base_url(config('payment.alepay.base_url')))
                $this->alepayService->setBaseURL($baseURL);
            if ($assetURL = $config->asset_url(config('payment.alepay.asset_url')))
                $this->alepayService->setAssetURL($assetURL);
        }
    }

    public function payOptions(Request $request)
    {
        $role = 'system';
        $agent_id = 0;
        $user = get_user_login();
        if($user->agent_id && ($agent = $this->userRepository->first(['id' => $user->agent_id, 'status' => User::ACTIVATED, 'type' => User::AGENT]))){
            $role = User::AGENT;
            $agent_id = $agent->id;
        }elseif ($user->ref_code && ($refAgent = $this->userRepository->first(['affiliate_code' => $user->ref_code, 'status' => User::ACTIVATED, 'type' => User::AGENT]))) {
            $role = User::AGENT;
            $agent_id = $refAgent->id;
        }
        $params = [
            'role' => $role
        ];
        if($agent_id){
            $params['agent_id'] = $agent_id;
        }
        $packages = $this->packageRepository->orderBy('retail_price','ASC')->get($params);

        return $this->viewModule('pay-options', ['user' => $user, 'packages' => $packages]);


    }

    public function packageDetail(Request $request, $id = null){
        $id = $id?$id:$request->id;
        if(!$id || !($package = $this->packageRepository->detail($id)))
            return redirect()->route('web.payments.pay-options')->with('warning', 'Gói bạn chọn không tồn tại');
        return $this->viewModule('pay-detail', ['package' => $package]);

    }

    public function pay(Request $request){
        extract($this->apiDefaultData);
        $user = $this->userRepository->first(['id' => $request->user()->id]);

        $validator = $this->transactionRepository->validator($request, ServicePaymentValidator::class);
        if (!$validator->success()) {
            $message = 'Dữ liệu gửi lên không hợp lệ';
            $errors = $validator->errors();
        } elseif (!($package = $this->packageRepository->first($request->order_id ? ['id' => $request->order_id] : ['@orderBy' => ['price', 'ASC']])))
            $message = 'Không có gói thanh toán nào dc cấu hình';
        elseif($package->role == User::AGENT && !$this->agentRepository->checkAgentMonthBalance($package->agent_id, $package->quantity))
            $message = 'Số dư của đại lý của bạn không đủ để thực hiện giao dịch';
        elseif ($package->wholesale_price == 0) {
            $this->agentRepository->updateMonthBalance($user->id, $package->quantity);
            return redirect()->route('web.account.info')->with('success', 'Chúc mừng bạn đã được cộng thêm ' . $package->quantity . ' tháng sử dụng');
        } elseif (!($method = $this->methodRepository->first($request->payment_method_id ? ['id' => $request->payment_method_id] : []))) {
            $message = 'Phương thức thanh toán không hợp lệ';
        }
        elseif($paymentData = $this->paymentService->createServicePayment($package, $method, $user, [
            'success_redirect_url' => route('web.account.info'),
            'cancel_redirect_url' => route('web.payments.detail', ['id' => $package->id]),
            'error_redirect_url' => route('web.payments.options'),
            'role' => User::USER
        ])){
            return redirect($paymentData['payment']['checkout_url']);
            $status = true;
            $data = $paymentData;
        }
        else {
            $message = $this->paymentService->getErrorMessage();
        }

        return redirect()->back()->withInput()->with('error', $message);
    }

    public function useMonthFromMyAccount(Request $request)
    {
        $redirect = function($key, $message){
            return redirect()->back()->withInput()->with($key, $message);
        };
        if (!($user = get_user_login()))
            return $redirect('error', 'Bạn chưa đăng nhập');
        if ($user->type != User::AGENT || !($agent = get_agent_account($user->id)))
            return $redirect('error', 'Bạn không phải đại lý. Không thể sử dụng chức năng này');
        if (!$request->month || !($month = to_number($request->month)) || $month < 1)
            return $redirect('error', 'Số tháng không hợp lệ');
        if($month > $agent->month_balance)
            return $redirect('error', 'Số tháng không được vượt quá số dư của bạn')->withErrors(['month' => 'Số dư của bạn chỉ còn ' . $agent->month_balance . ' tháng']);
        if(!$this->userService->addMonth($user->id, $month))
            return $redirect('error', 'Không thể gia hạn sử dụng AI');
        $agent->month_balance-=$month;
        $agent->save();
        return redirect()->route('web.account.info')->with('success', 'Chúc mừng bạn đã gia hạn thành công!');
    }

    public function buyPackage(Request $request){
        $redirect = function($key, $message){
            return redirect()->back()->withInput()->with($key, $message);
        };
        if (!($user = get_user_login()))
            return $redirect('error', 'Bạn chưa đăng nhập');

    }
    public function alepayWebhook(Request $request)
    {
        extract($this->apiDefaultData);

        $status = $this->alepayService->alepayWebhook($request);

        if (!$status) {
            $message = $this->paymentService->getErrorMessage();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }


    public function cancelTransaction(Request $request)
    {
        $return = $this->paymentService->cancelTransaction($request);
        return $return;
    }

    public function completeTransaction(Request $request)
    {
        return $this->paymentService->completeTransaction($request);
    }

    public function checkPaymentStatus(Request $request)
    {
        extract($this->apiDefaultData);
        extract($this->paymentService->checkPaymentStatus($request));

        return $this->json(compact(...$this->apiSystemVars));
    }
}
