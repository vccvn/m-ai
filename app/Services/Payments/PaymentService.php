<?php

namespace App\Services\Payments;

use App\Services\Service;
use App\Models\ConnectPackage;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\ServicePackage;
// use App\Models\PaymentRequest;
use App\Models\UploadPackage;
use App\Models\User;
use App\Models\Voucher;
use App\Repositories\Accounts\AgentRepository;
use App\Repositories\Accounts\WalletRepository;
use App\Repositories\Payments\AgentPaymentLogRepository;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

// use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
use App\Repositories\Policies\CommissionRepository;
use App\Repositories\Promotions\VoucherRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Mailers\Mailer;
use App\Services\Mailers\MailNotification;
use App\Services\Payments\AlePayResponse;
use App\Services\Payments\AlePayService;
use Carbon\Carbon;
use Gomee\Files\Filemanager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @property-read UserRepository $userRepository
 * @property-read MethodRepository $methodRepository
 * @property-read PackageRepository $packageRepository
 * @property-read VoucherRepository $voucherRepository
 * @property-read TransactionRepository $transactionRepository
 * @property-read AgentRepository $agentRepository
 * @property-read AlePayService $alepayService
 * @property-read Filemanager $filemanager CommissionRepository
 * @property-read CommissionRepository $commissionRepository CommissionRepository

 * @property-read WalletRepository $walletRepository
 */

class PaymentService extends Service
{
    protected $module = 'payment';

    protected $moduleName = 'Alepay';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var RequestRepository
     */
    public $repository;

    protected $voucherRepository;
    protected $errorMessage = null;

    protected $alepayConfig = null;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        // RequestRepository $repository,
        UserRepository $userRepository,
        PackageRepository $packageRepository,
        MethodRepository $methodRepository,
        TransactionRepository $transactionRepository,
        AlePayService $alepayService,
        Filemanager $filemanager,
        AgentRepository $agentRepository,
        CommissionRepository $commissionRepository,
        WalletRepository $walletRepository
    ) {
        // $this->repository                 = $repository;
        $this->userRepository             = $userRepository;
        $this->methodRepository           = $methodRepository;
        $this->packageRepository          = $packageRepository;
        $this->transactionRepository      = $transactionRepository;
        $this->alepayService              = $alepayService;
        $this->filemanager                = $filemanager;
        $this->agentRepository            = $agentRepository;
        $this->commissionRepository       = $commissionRepository;
        $this->walletRepository           = $walletRepository;
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

        $this->transactionRepository->on('completed', function ($transaction) {
            dump("on complete");
            // $this->onTransactionCompleted($transaction);
        });
    }




    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getPaymentMethod($uuid = null)
    {
        if ($uuid && $paymentMethod = $this->methodRepository->first(['id' => $uuid]))
            return $paymentMethod;
        return $this->methodRepository->first();
    }

    /**
     * tạo thanh toán
     *
     * @return JsonResponse
     */
    public function createPayment($user, $method, $type, $payment_data = [])
    {
        if ($method->method == PaymentMethod::PAYMENT_ALEPAY) {
            if ($paymentData = $this->createAlePayTransaction($user, $type, $payment_data)) {
                return [
                    'action' => 'payment',
                    'payment' => $paymentData
                ];
            } else {
                $this->errorMessage = 'Không thể khởi tạo thanh toán (1)';
            }
        } else {
            $this->errorMessage = 'Lỗi không xác định';
        }

        return false;
    }


    /**
     * tao yeu cầu thanh toán kết nối
     *
     * @param ServicePackage $package
     * @param PaymentMethod $method
     * @param User $user
     * @param array $advance
     * @return array
     */
    public function createServicePayment($package, $method, $user, $advance = [])
    {


        if ($method->method == PaymentMethod::PAYMENT_ALEPAY) {
            if ($paymentData = $this->createServicePaymentTransaction($user, $package, $method, $advance)) {
                return [
                    'status' => 'payment',
                    'action' => 'payment',
                    'payment' => $paymentData
                ];
            } else {
                $this->errorMessage = 'Không thể khởi tạo thanh toán (2)';
            }
        } else {
            $this->errorMessage = 'Lỗi không xác định';
        }

        return false;
    }



    public function alepayWebhook(Request $request)
    {

        $message = '';
        $status = false;
        $response = new AlePayResponse($request->all());
        if ($response->status == "005") {
            $message = 'Đang tiến hành';
        } elseif ($response->status == "001") {
            $message = 'Đang tiến hành';
        } elseif (substr($response->status, 0, 1) == '0') {
            $message = 'Đang tiến hành';
        } elseif ($response->status == '111') {
            $message = 'Giao dịch đã bị huỷ';
            $this->transactionRepository->updatePaymentStatus($response, false, $message);
        } elseif ($response->status != '000') {
            $message = 'Giao dịch đã bị huỷ';
            $this->transactionRepository->updatePaymentStatus($response, false, $message);
        } elseif (
            !($payment = $this->transactionRepository->first(['transaction_code' => $request->transactionCode]))
        ) {
            $message = 'Không tìm được thông tin gói';
        } elseif ($payment->status == PaymentTransaction::STATUS_PROCESSING && $response->isSuccess) {
            if (($transaction = $this->transactionRepository->updatePaymentStatus($response, true)) && $this->onTransactionCompleted($transaction)) {
                $status = true;
            } else {
                $message = 'Không thể lưu dữ liệu';
            }
        } elseif ($payment->status == PaymentTransaction::STATUS_COMPLETED) {
            $status = true;
        } else {
            $message = 'Lỗi không xác định';
        }
        $this->errorMessage = $message;
        return $status;
    }


    public function cancelTransaction(Request $request)
    {
        $data = $this->transactionRepository->updatePaymentStatus($request, false, $message = 'Giao dịch đã bị huỷ');
        $status = $data ? true : false;
        return redirect()->route('merchant.payments.transactions.create', ['transaction_code' => $request->transactionCode])->with('error', $message);


        if ($data && $data->method && $data->method->method == PaymentMethod::PAYMENT_ALEPAY) {

            $url = $data->cancel_redirect_url ? $data->cancel_redirect_url : ($this->alepayConfig && $this->alepayConfig->cancel_url ? $this->alepayConfig->cancel_url :  url('/'));
            return redirect(url_merge($url, 'transaction_code', $request->transactionCode));
        }

        return redirect(url('?transaction_code=' . $request->transactionCode));
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function completeTransaction(Request $request)
    {
        $message = '';
        $redirect = null;
        if (!$request->transactionCode || !($pr = $this->transactionRepository->with('method')->first(['transaction_code' => $request->transactionCode]))) {

        } elseif ($request->errorCode != '000') {
            $message = $this->alepayService->getMessage($request->errorCode);
            $this->transactionRepository->updatePaymentStatus($request, false, $message);
            $redirect = $pr->error_redirect_url;
        } elseif ($request->cancel == 'True') {
            $message = 'Yêu cầu thanh toán đã bị huỷ';
            $this->transactionRepository->updatePaymentStatus($request, false, $message);
            $redirect = $pr->cancel_redirect_url;
        } elseif (!($response = $this->alepayService->getTransactionInfo($request->transactionCode))) {
            $message = 'Không có thông tin giao dịch';
            $redirect = $pr->error_redirect_url;
        } elseif (!$response->isSuccess || $response->status != "000") {
            $message = $response->message;
            $this->transactionRepository->updatePaymentStatus($response, false, $message);
        }
        elseif($pr->status == PaymentTransaction::STATUS_COMPLETED){
            $package = $this->packageRepository->find($pr->order_id);

            // dd("1", $pr);
            if ($pr->method && $pr->method->method == PaymentMethod::PAYMENT_ALEPAY) {
                return redirect()->route('merchant.payments.transactions.create', ['transaction_code' => $request->transactionCode])->with('success', 'Bạn đã thanh toán thành công và được cộng thêm ' . $package->quantity . ' tháng sử dụng');
                // $url = url_merge($redirect ? $redirect : ($this->alepayConfig && $this->alepayConfig->return_url ? $this->alepayConfig->return_url :  url('/')), ['transaction_code' => $request->transactionCode]);
                // return redirect($url);
            }
            return redirect()->route('merchant.payments.transactions.create', ['transaction_code' => $request->transactionCode])->with('success', 'Bạn đã thanh toán thành công');
        }

        elseif (!($payment = $this->transactionRepository->updatePaymentStatus($response, true))) {
            $redirect = $pr->error_redirect_url;

            $message = $this->errorMessage ?? 'Lỗi hệ thống. chúng tôi sẽ xác minh giao dịch và thông báo cho bạn khi hoàn tất';
            // $this->transactionRepository->updatePaymentStatus($response, false, $message);
        }
        elseif (!$this->onTransactionCompleted($payment)) {
            # code...
            $message = $this->errorMessage ?? 'Lỗi hệ thống. chúng tôi sẽ xác minh giao dịch và thông báo cho bạn khi hoàn tất';

        }
        else {
            $redirect = $pr->success_redirect_url;
            $package = $this->packageRepository->find($payment->order_id);
            // dd("2", $payment);
            // $request->session()->put('success', 'Bạn đã thanh toán thành công và được cộng thêm ' . $package->quantity . ' tháng sử dụng');
            // $this->onTransactionCompleted($payment);
            if ($pr->method && $pr->method->method == PaymentMethod::PAYMENT_ALEPAY) {
                return redirect()->route('merchant.payments.transactions.create', ['transaction_code' => $request->transactionCode])->with('success', 'Bạn đã thanh toán thành công và được cộng thêm ' . $package->quantity . ' tháng sử dụng');
                // $url = url_merge($redirect ? $redirect : ($this->alepayConfig && $this->alepayConfig->return_url ? $this->alepayConfig->return_url :  url('/')), ['transaction_code' => $request->transactionCode]);
                // return redirect($url);
            }
        }
        // dd("3");

        if ($redirect)
            return redirect(url_merge($redirect, ['transaction_code' => $request->transactionCode]));

        return redirect()->route('merchant.payments.transactions.create', ['transaction_code' => $request->transactionCode])
            ->with('error', $message);
    }

    /**
     * check payment status
     *
     * @param Request $request
     * @return array
     */
    public function checkPaymentStatus(Request $request)
    {
        $status = [];
        $redirect = null;
        $data = [];
        $stop = false;
        $transaction_code = $request->transactionCode ?? $request->transaction_code;
        $message = '';
        if (
            !$transaction_code ||
            !($response = $this->alepayService->getTransactionInfo($transaction_code)) ||
            !($payment = $this->transactionRepository->first(['transaction_code' => $transaction_code]))
        ) {
            $message = 'Không tìm được thông tin gói';
            $stop = true;
        } elseif ($payment->status == PaymentTransaction::STATUS_COMPLETED) {
            $status = true;
            $stop = true;
            $data = $payment;
        } elseif ($payment->status == PaymentTransaction::STATUS_CANCELED) {
            $message = 'Giao dịch đã bị huỷ';
            $stop = true;
        } elseif ($response->status == null) {
            $message = 'chưa có thông tin';
        } elseif ($response->status == '111') {
            $this->transactionRepository->updatePaymentStatus($response, false);
            $message = 'Giao dịch đã bị huỷ';
            $stop = true;
        } elseif ($response->status != "000") {
            $message = 'Đang tiến hành';
        } elseif ($payment->status == PaymentTransaction::STATUS_PROCESSING && $response->isSuccess) {
            if ($pr = $this->transactionRepository->updatePaymentStatus($response, true)) {
                $status = true;
                $stop = true;
                $data['payment'] = $pr;
            } else {
                $message = $this->errorMessage ?? 'Không thể lưu lịch sử';
            }
        } elseif ($payment->status == PaymentTransaction::STATUS_COMPLETED) {
            $status = true;
            $data['payment'] = $payment;
        }

        $data['stop'] = $stop;
        return compact('status', 'data', 'message');
    }



    /**
     * create ale pay request
     *
     * @param User $user
     * @param ServicePackage $package
     * @param PaymentMethod $method
     * @param array $advance
     * @return array
     */
    public function createServicePaymentTransaction($user, $package, $method, $advance = [])
    {
        $paymentData = new Arr([
            'orderCode' => strtoupper(uniqid()),
            'customMerchantId' => 'm-ai-' . $user->id,
            'amount' => (int) ($user->type == User::AGENT ? $package->wholesale_price : $package->retail_price),
            'currency' => $package->currency,
            'orderDescription' => 'Đăng ký ' . $package->name,
            'totalItem' => $package->quantity,
            'returnUrl' => route('web.payments.complete'),
            'cancelUrl' => route('web.payments.cancel'),
            'buyerName' => $user->name,
            'buyerEmail' => $user->email,
            'buyerPhone' => $user->phone ?? "0987123456",
            'buyerAddress' => 'So 1 Dai Co Viet',
            'buyerCity' => 'Hà Nội',
            'buyerCountry' => 'Việt Nam',
            'paymentHours' => date('H'),
            'promotionCode' => '',
            'allowDomestic' => true,
            'language' => 'vi'
        ]);

        // tạo request của ale pay
        $aleResponse = $this->alepayService->createPaymentRequest($paymentData->all());
        if ($aleResponse->isSuccess) {
            // $paymentData->transactionCode = $aleResponse->transactionCode;
            // $paymentData->currentConnectCount = $user->connect_count;
            // $paymentData->packageConnectCount = $package->connect_count;
            $paymentRequest = $this->transactionRepository->create(array_merge((array) $advance, [
                'type' => PaymentTransaction::TYPE_BUY_SERVICE,
                'order_id' => $package->id,
                'user_id' => $user->id,
                'order_code' => $paymentData->orderCode,
                'amount' => $paymentData->amount ?? 0,
                'currency' => $paymentData->currency ?? 'VND',
                'promotion_code' => $paymentData->promotionCode,
                'transaction_code' => $aleResponse->transactionCode,
                'payment_method_id' => $method->id
            ]));
            if ($paymentRequest)
                return [
                    'transaction_code' => $aleResponse->transactionCode,
                    'price_format' => get_price_format($user->type == User::AGENT ? $package->wholesale_price : $package->retail_price, $package->currency),
                    'check_status_url' => route('api.payment.status'),
                    'checkout_url' => $aleResponse->checkoutUrl
                ];
        } else
            $this->errorMessage = $aleResponse->message;
        return [];
    }




    /**
     * create ale pay request
     *
     * @param User $user
     * @param string $type
     * @param array $payment_data
     * @param array $advance
     * @return array
     */
    public function createAlePayTransaction($user, $type = 'buy-connect', $payment_data = [], $advance = [])
    {
        $paymentData = new Arr([
            'orderCode' => $payment_data['order_code'] ??  strtoupper(uniqid()),
            'customMerchantId' => 'tk-' . $user->id,
            'amount' => (int) ($payment_data['amount'] ?? 0),
            'currency' => $payment_data['currency'] ?? 'VND',
            'orderDescription' => $payment_data['note'] ?? '',
            'totalItem' => $payment_data['total_item'] ?? 1,
            'returnUrl' => $payment_data['return_url'] ?? route('api.payment.complete'),
            'cancelUrl' => $payment_data['cancel_url'] ?? route('api.payment.cancel'),
            'buyerName' => $user->name,
            'buyerEmail' => $user->email,
            'buyerPhone' => $user->phone_number ?? "0987123456",
            'buyerAddress' => $user->address ?? 'So 1 Dai Co Viet',
            'buyerCity' => 'Hà Nội',
            'buyerCountry' => 'Việt Nam',
            'paymentHours' => date('H'),
            'promotionCode' => '',
            'allowDomestic' => true,
            'language' => 'vi'
        ]);

        // tạo request của ale pay
        $aleResponse = $this->alepayService->createPaymentRequest($paymentData->all());
        if ($aleResponse->isSuccess) {
            // $paymentData->transactionCode = $aleResponse->transactionCode;
            // $paymentData->currentConnectCount = $user->connect_count;
            // $paymentData->packageConnectCount = $package->connect_count;
            $paymentRequest = $this->transactionRepository->create(array_merge($advance, [
                'type' => $type,
                'order_id' => $payment_data['order_id'] ?? '',
                'user_id' => $user->id,
                'order_code' => $paymentData->orderCode,
                'amount' => $paymentData->amount ?? 0,
                'currency' => $paymentData->currency ?? 'VND',
                'promotion_code' => $paymentData->promotionCode,
                'transaction_code' => $aleResponse->transactionCode,
                'payment_method_id' => $payment_data['method_id'] ?? ($payment_data['payment_method_id'] ?? ''),
                'ref_data' => is_array($r = $payment_data['ref_data'] ?? []) ? $r : [],
                'note' => $paymentData->orderDescription
            ]));
            if ($paymentRequest)
                return [
                    'transaction_code' => $aleResponse->transactionCode,
                    'price_format' => get_price_format($payment_data['amount'] ?? 0, $payment_data['currency'] ?? 'VND'),
                    'check_status_url' => route('api.payment.status'),
                    'checkout_url' => $aleResponse->checkoutUrl
                ];
        } else
            $this->errorMessage = $aleResponse->message;
        return [];
    }






    /**
     * hoan thangh
     *
     * @param PaymentTransaction $payment
     * @return void
     */
    public function onTransactionCompleted($payment)
    {
        // dump($payment);
        if ($payment->type == PaymentTransaction::TYPE_BUY_SERVICE) {
            if (!($user = $this->userRepository->find($payment->user_id)))
                $message = 'Không thấy user';
            else{
                $package = $this->packageRepository->find($payment->order_id);
                if($user->type == User::AGENT && $agent = get_agent_account($user->id)){
                    $agent->month_balance += ($package)?$package->quantity:0;
                    // dd($agent, $package);
                    $agent->save();
                }else{
                    $time = strtotime($user->expired_at);
                    if($time < time())
                        $datetime = Carbon::now()->addMonths($package?$package->quantity:0);
                    else
                        $datetime = Carbon::parse($user->expired_at)->addMonths($package?$package->quantity:0);
                    $user->expired_at = $datetime->toDateTimeString();
                    $user->save();

                }
                $this->plusMoney($user, $payment->amount, 0);
                return true;
            }
        } else {
            $message = 'Giao dịch không hợp lệ';
        }
        $this->errorMessage = $message;
        return false;
    }


    /**
     * cong tien cho agent
     *
     * @param User $user
     * @param integer $amount
     * @param integer $level
     * @return void
     */
    public function plusMoney($user, $amount = 0, $level = 0)
    {
        $level++;
        if (!$user->agent_id && !$user->ref_code)
            return false;
        $ag = null;
        if ($user->agent_id && $a = $this->userRepository->first(['id' => $user->agent_id]))
            $ag = $a;
        elseif ($user->ref_code && $ap = $this->userRepository->first(['affiliate_code' => $user->ref_code]))
            $ag = $ap;
        if (!$ag) return false;
        if ($ag->type != User::AGENT) {
            if ($level > 1)
                return false;
            return $this->addMoneyToUser($user, $ag, $amount, $level);
        }
        return $this->addMoneyToAgent($user, $ag, $amount, $level);
    }

    /**
     * add to agent
     *
     * @param User $user
     * @param User $agentUser
     * @param int $amount
     * @param int $level
     * @return void
     */
    public function addMoneyToAgent($user, $agentUser, $amount = 0, $level = 1)
    {
        if (!($agent = $this->agentRepository->with('policy')->first(['user_id' => $agentUser->id])) || !($policy = $agent->policy))
            return false;
        if ($policy->receive_times >= 0 && $this->transactionRepository->count(['user_id' => $user->id, 'status' => PaymentTransaction::STATUS_COMPLETED]))
            return false;
        $percent = $policy->{'commission_level_' . $level};
        if (!$percent || $percent <= 0)
            return false;
        $wallet = $this->walletRepository->createDefaultWallet($agentUser->id);
        $wallet->balance += $percent * $amount / 100;
        $wallet->save();
        return $this->plusMoney($agent, $amount, $level + 1);
    }

    /**
     * cong rien cho user
     *
     * @param User $user
     * @param User $refUser
     * @param integer $amount
     * @return void
     */
    public function addMoneyToUser($user, $refUser, $amount = 0, $level = 1)
    {
        if (!($policy = $this->commissionRepository->first(['type' => 'user'])))
            return false;
        if ($policy->receive_times >= 0 && $policy->receive_times < $this->transactionRepository->count(['user_id' => $user->id, 'status' => PaymentTransaction::STATUS_COMPLETED]))
            return null;
        $percent = $policy->{'commission_level_' . $level};
        if (!$percent || $percent <= 0)
            return false;
        $wallet = $this->walletRepository->createDefaultWallet($refUser->id);
        $wallet->balance += $percent * $amount / 100;
        $wallet->save();
        return $this->plusMoney($refUser, $amount, $level + 1);
    }
}
