<?php

namespace App\Http\Controllers\Apis\Payments;

use App\Http\Controllers\Apis\ApiController;
use App\Models\ConnectPackage;
use App\Models\PaymentMethod;
use App\Models\PaymentRequest;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Encryptions\HashService;
use App\Services\Mailers\MailNotification;
use App\Services\Payments\AlePayResponse;
use App\Services\Payments\AlePayService;
use App\Services\Payments\PaymentService;
use App\Validators\Payments\ConnectPaymentValidator;
use Carbon\Carbon;
use Gomee\Files\Filemanager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @property-read UserRepository $userRepository
 * @property-read PackageRepository $packageRepository
 * @property-read MethodRepository $methodRepository
 * @property-read TransactionRepository $transactionRepository
 * @property-read AlePayService $alepayService
 * @property-read PaymentService $paymentService
 * @property-read Filemanager $filemanager
 */
class PaymentController extends ApiController
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
        RequestRepository $repository,
        UserRepository $userRepository,
        PackageRepository $packageRepository,
        MethodRepository $methodRepository,
        TransactionRepository $transactionRepository,
        AlePayService $alepayService,
        PaymentService $paymentService,
        Filemanager $filemanager
    ) {
        $this->repository             = $repository;
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

    /**
     * tạo thanh toán
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createConnectPayment(Request $request)
    {
        extract(self::$vars);
        $user = $this->userRepository->first(['uuid' => $request->user()->uuid]);

        $validator = $this->repository->validator($request, ConnectPaymentValidator::class);
        if (!$validator->success()) {
            $message = 'Dữ liệu gửi lên không hợp lệ';
            $errors = $validator->errors();
        } elseif (!($package = $this->packageRepository->first($request->package_uuid ? ['uuid' => $request->package_uuid] : ['@orderBy' => ['price', 'ASC']])))
            $message = 'Không có gói thanh toán nào dc cấu hình';
        elseif ($package->price == 0) {
            $user->connect_count += $package->connect_count;
            $user->save();
            $status = true;
            $data = [
                'action' => 'refresh',
                'user' => $this->userRepository->mode('mask')->detail(['uuid' => $request->user()->uuid])
            ];
        } elseif (!($method = $this->methodRepository->first($request->payment_method_uuid ? ['uuid' => $request->payment_method_uuid] : []))) {
            $message = 'Phương thức thanh toán không hợp lệ';
        }
        elseif($paymentData = $this->paymentService->createConnectPayment($package, $method, $user, ['success_redirect_url' => $request->success_redirect_url, 'cancel_redirect_url' => $request->cancel_redirect_url, 'error_redirect_url' => $request->error_redirect_url])){
            $status = true;
            $data = $paymentData;
        }
        else {
            $message = $this->paymentService->getErrorMessage();
        }

        return $this->json(compact(...self::$outVars));
    }

    public function alepayWebhook(Request $request)
    {
        extract(self::$vars);

        $status = $this->alepayService->alepayWebhook($request);

        if(!$status){
            $message = $this->paymentService->getErrorMessage();
        }

        return $this->json(compact(...self::$outVars));
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
        extract(self::$vars);

        extract($this->paymentService->checkPaymentStatus($request));

        return $this->json(compact(...self::$outVars));
    }





    /**
     * create ale pay request
     *
     * @param User $user
     * @param UploadPackage $package
     * @param PaymentMethod $method
     * @return array
     */
    public function createAlePayRequest($user, $package, $method)
    {
        $config = $method->getConfigData();
        $paymentData = new Arr([
            'orderCode' => strtoupper(uniqid()),
            'customMerchantId' => 'ca-' . $user->uuid,
            'amount' => (int) $package->price,
            'currency' => $package->currency,
            'orderDescription' => 'Đăng ký ' . $package->name,
            'totalItem' => $package->upload_count,
            'returnUrl' => route('api.payment.complete'),
            'cancelUrl' => route('api.payment.cancel'),
            'buyerName' => $user->full_name,
            'buyerEmail' => $user->email,
            'buyerPhone' => $user->phone_number ?? "0987123456",
            'buyerAddress' => $user->address ?? 'So 1 Dai Co Viet',
            'buyerCity' => $user->region ? $user->region->name : 'Hà Nội',
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
            $paymentRequest = $this->repository->create([
                'order_uuid' => $package->uuid,
                'user_uuid' => $user->uuid,
                'order_code' => $paymentData->orderCode,
                'amount' => $paymentData->amount ?? 0,
                'currency' => $paymentData->currency ?? 'VND',
                'promotion_code' => $paymentData->promotionCode,
                'transaction_code' => $aleResponse->transactionCode,
                'payment_method_uuid' => $method->uuid
            ]);
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


}
