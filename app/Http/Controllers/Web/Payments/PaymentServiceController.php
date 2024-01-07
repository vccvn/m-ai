<?php

namespace App\Http\Controllers\Web\Payments;

// use App\Http\Controllers\Apis\ApiController;

use App\Http\Controllers\Web\WebController;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Illuminate\Http\Request;

use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Payments\AlePayService;
use App\Services\Payments\PaymentService;
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
        Filemanager $filemanager
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

    public function checkout(Request $request) {
        $role = 'system';
        $agent_id = 0;
        $user = '';
    }

    public function alepayWebhook(Request $request)
    {
        extract($this->apiDefaultData);

        $status = $this->alepayService->alepayWebhook($request);

        if(!$status){
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
