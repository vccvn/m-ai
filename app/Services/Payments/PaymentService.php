<?php

namespace App\Services\Payments;

use App\Services\Service;
use App\Models\ConnectPackage;
use App\Models\PaymentMethod;
use App\Models\PaymentRequest;
use App\Models\UploadPackage;
use App\Models\User;
use App\Models\Voucher;
use App\Repositories\Payments\AgentPaymentLogRepository;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
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
 * @property-read AlePayService $alepayService
 * @property-read Filemanager $filemanager
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

    /**
     * AgentPaymentLogRepository
     *
     * @var AgentPaymentLogRepository
     */
    protected $agentPaymentLogRepository;
    protected $voucherRepository;
    protected $errorMessage = null;

    protected $alepayConfig = null;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        RequestRepository $repository,
        UserRepository $userRepository,
        PackageRepository $packageRepository,
        MethodRepository $methodRepository,
        TransactionRepository $transactionRepository,
        AlePayService $alepayService,
        Filemanager $filemanager
    ) {
        $this->repository                 = $repository;
        $this->userRepository             = $userRepository;
        $this->methodRepository           = $methodRepository;
        $this->packageRepository          = $packageRepository;
        $this->transactionRepository      = $transactionRepository;
        $this->alepayService              = $alepayService;
        $this->filemanager                = $filemanager;
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
            if ($paymentData = $this->createAlePayRequest($user, $type, $payment_data)) {
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
     * @param UploadPackage $package
     * @param PaymentMethod $method
     * @param User $user
     * @param array $advance
     * @return array
     */
    public function createUploadPayment($package, $method, $user, $advance = [])
    {


        if ($method->method == PaymentMethod::PAYMENT_ALEPAY) {
            if ($paymentData = $this->createUploadAlePayRequest($user, $package, $method, $advance)) {
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
            $this->repository->updatePaymentStatus($response, false, $message);
        } elseif ($response->status != '000') {
            $message = 'Giao dịch đã bị huỷ';
            $this->repository->updatePaymentStatus($response, false, $message);
        } elseif (
            !($payment = $this->repository->first(['transaction_code' => $request->transactionCode]))
        ) {
            $message = 'Không tìm được thông tin gói';
        } elseif ($payment->status == PaymentRequest::STATUS_PROCESSING && $response->isSuccess) {
            if ($this->savePaymentHistory($response) && $this->repository->updatePaymentStatus($response, true)) {
                $status = true;
            } else {
                $message = 'Không thể lưu dữ liệu';
            }
        } elseif ($payment->status == PaymentRequest::STATUS_COMPLETED) {
            $status = true;
        } else {
            $message = 'Lỗi không xác định';
        }
        $this->errorMessage = $message;
        return $status;
    }


    public function cancelTransaction(Request $request)
    {
        $data = $this->repository->updatePaymentStatus($request, false, $message = 'Giao dịch đã bị huỷ');
        $status = $data ? true : false;
        return redirect()->route('merchant.payments.requests.create', ['transaction_code' => $request->transactionCode])->with('error', $message);


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
        if (!$request->transactionCode || !($pr = $this->repository->with('method')->first(['transaction_code' => $request->transactionCode]))) {
        } elseif ($request->errorCode != '000') {
            $message = $this->alepayService->getMessage($request->errorCode);
            $this->repository->updatePaymentStatus($request, false, $message);
            $redirect = $pr->error_redirect_url;
        } elseif ($request->cancel == 'True') {
            $message = 'Yêu cầu thanh toán đã bị huỷ';
            $this->repository->updatePaymentStatus($request, false, $message);
            $redirect = $pr->cancel_redirect_url;
        } elseif (!($response = $this->alepayService->getTransactionInfo($request->transactionCode))) {
            $message = 'Không có thông tin giao dịch';
            $redirect = $pr->error_redirect_url;
        } elseif (!$response->isSuccess || $response->status != "000") {
            $message = $response->message;
            $this->repository->updatePaymentStatus($response, false, $message);
        } elseif (!($transaction = $this->savePaymentHistory($response))) {
            $redirect = $pr->error_redirect_url;

            $message = $this->errorMessage ?? 'Lỗi hệ thống. chúng tôi sẽ xác minh giao dịch và thông báo cho bạn khi hoàn tất';
            $this->repository->updatePaymentStatus($response, false, $message);
        } else {
            $redirect = $pr->success_redirect_url;

            $payment = $this->repository->updatePaymentStatus($response, true);
            if ($pr->method && $pr->method->method == PaymentMethod::PAYMENT_ALEPAY) {
                return redirect()->route('merchant.3d.models.upload', ['transaction_code' => $request->transactionCode])->with('success', 'Bạn đã thanh toán thành công và được cộng thêm ' . $transaction->package_upload_count . ' lượt tải file lên!');
                $url = url_merge($redirect ? $redirect : ($this->alepayConfig && $this->alepayConfig->return_url ? $this->alepayConfig->return_url :  url('/')), ['transaction_code' => $request->transactionCode]);
                return redirect($url);
            }
        }
        if ($redirect)
            return redirect(url_merge($redirect, ['transaction_code' => $request->transactionCode]));

        return redirect()->route('merchant.payments.requests.create', ['transaction_code' => $request->transactionCode])
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
            !($payment = $this->repository->first(['transaction_code' => $transaction_code]))
        ) {
            $message = 'Không tìm được thông tin gói';
            $stop = true;
        } elseif ($payment->status == PaymentRequest::STATUS_COMPLETED) {
            $status = true;
            $stop = true;
            $data = $payment;
        } elseif ($payment->status == PaymentRequest::STATUS_CANCELED) {
            $message = 'Giao dịch đã bị huỷ';
            $stop = true;
        } elseif ($response->status == null) {
            $message = 'chưa có thông tin';
        } elseif ($response->status == '111') {
            $this->repository->updatePaymentStatus($response, false);
            $message = 'Giao dịch đã bị huỷ';
            $stop = true;
        } elseif ($response->status != "000") {
            $message = 'Đang tiến hành';
        } elseif ($payment->status == PaymentRequest::STATUS_PROCESSING && $response->isSuccess) {
            if ($this->savePaymentHistory($response) && $pr = $this->repository->updatePaymentStatus($response, true)) {
                $status = true;
                $stop = true;
                $data['payment'] = $pr;
            } else {
                $message = $this->errorMessage ?? 'Không thể lưu lịch sử';
            }
        } elseif ($payment->status == PaymentRequest::STATUS_COMPLETED) {
            $status = true;
            $data['payment'] = $payment;
        }

        $data['stop'] = $stop;
        return compact('status', 'data', 'message');
    }





    /**
     * create ale pay request


    /**
     * create ale pay request
     *
     * @param User $user
     * @param UploadPackage $package
     * @param PaymentMethod $method
     * @param array $advance
     * @return array
     */
    public function createUploadAlePayRequest($user, $package, $method, $advance = [])
    {
        $paymentData = new Arr([
            'orderCode' => strtoupper(uniqid()),
            'customMerchantId' => 'ca-' . $user->id,
            'amount' => (int) $package->price,
            'currency' => $package->currency,
            'orderDescription' => 'Đăng ký ' . $package->name,
            'totalItem' => $package->upload_count,
            'returnUrl' => route('api.payment.complete'),
            'cancelUrl' => route('api.payment.cancel'),
            'buyerName' => $user->full_name,
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
            $paymentRequest = $this->repository->create(array_merge((array) $advance, [
                'type' => PaymentRequest::TYPE_BUY_UPLOAD_FILES,
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
                    'price_format' => get_price_format($package->price, $package->currency),
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
    public function createAlePayRequest($user, $type = 'buy-connect', $payment_data = [], $advance = [])
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
            'buyerName' => $user->full_name,
            'buyerEmail' => $user->email,
            'buyerPhone' => $user->phone ?? "0987123456",
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
            $paymentRequest = $this->repository->create(array_merge($advance, [
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
     * lưu lịch sử
     *
     * @param AlePayResponse $response
     * @return PaymentTransaction|false
     */
    public function savePaymentHistory(AlePayResponse $response)
    {
        $message = '';
        DB::beginTransaction();
        if (
            !($payment = $this->repository->first(['transaction_code' => $response->transactionCode]))
            || !($user = $this->userRepository->first(['id' => $payment->user_id]))
        ) {
            $message = 'Thông tin tin không hợp lệ';
        } elseif ($payment->status == PaymentRequest::STATUS_PROCESSING) {
            try {
                $transaction = null;
                if ($payment->type == PaymentRequest::TYPE_BUY_UPLOAD_FILES) {
                    if ($trams = $this->completeBuyUploadFileTraction($user, $response, $payment)) {
                        $transaction = $trams;
                    } else {
                        $message = 'Gói không tồn tại';
                    }
                } else {
                    $message = 'Giao dịch không hợp lệ';
                }
                $this->errorMessage = $message;

                DB::commit();
                return $transaction;
            } catch (\Exception $exception) {
                $this->errorMessage = 'Không thể hoàn thành giao dịch';
                DB::rollback();
                $this->filemanager->json(storage_path('payments/logs/' . $response->transactionCode . '.json'), $response->toArray());
                $this->filemanager->save(storage_path('payments/logs/' . $response->transactionCode . '.log'), $exception->getMessage());
                MailNotification::subject('Có người giao dịch thành công nhưng đã xảy ra lỗi trong quá trình xử lý')
                    ->body('simple')
                    ->content('Có người thanh toán thành công nhưng xảy ra lỗi trong quá trình xử lý. Chi tiết xem file log đã được mã hoá. <br> Mã giao dịch: ' . $response->transactionCode)
                    ->sendAfter(1);
            }
        } elseif ($payment->status == PaymentRequest::STATUS_COMPLETED) {
            if ($payment->type == PaymentRequest::TYPE_BUY_UPLOAD_FILES) {

                if ($voucher = $this->packageRepository->find($payment->order_id)) {
                    return $voucher;
                }
            }
        }
        $this->errorMessage = $message;
        // die ($message);
        return false;
    }



    public function completeBuyUploadFileTraction($user, $response, $payment)
    {

        if (!($package = $this->packageRepository->find($payment->order_id))) {
            $this->errorMessage = 'Gói kết nối không tồn tại';
        } else {
            $payment->payment_method_name = $response->paymentMethod ?? $response->method;
            $payment->save();
            $transaction = $this->transactionRepository->create([
                'payment_method_name' => $payment->payment_method_name,
                'order_id' => $package->id,
                'order_code' => $response->orderCode,
                'transaction_code' => $payment->transaction_code,
                'money' => $response->amount,
                'amount' => $response->amount,
                'currency' => $response->currency,
                'user_id' => $user->id,
                'note' => $response->orderDescription,
                'description' => $response->orderDescription,
                'current_upload_count' => $user->upload_count,
                'package_upload_count' => $package->upload_count,
                'ref_code' => $user->ref_code,
                'type' => $payment->type,
                'is_reported' => false
            ]);

            $user->upload_count += $package->upload_count;
            $user->save();
            // $a = $this->addMoneyForAgent($user->id, $user->ref_code, $transaction->id, $response->amount, $response->currency);
            return $transaction;
        }
        return null;
    }
}
