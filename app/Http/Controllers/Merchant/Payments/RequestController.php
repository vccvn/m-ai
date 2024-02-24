<?php

namespace App\Http\Controllers\Merchant\Payments;

use App\Excels\ReportPaymentDownloader;
use App\Http\Controllers\Admin\CPanel\Filemanager;
use App\Http\Controllers\Merchant\MerchantController;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Payments\AlePayService;
use App\Services\Payments\PaymentService;
use App\Validators\Payments\UploadPaymentValidator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/**
 * @property-read UserRepository $userRepository
 * @property-read PackageRepository $packageRepository
 * @property-read MethodRepository $methodRepository
 * @property-read TransactionRepository $transactionRepository
 * @property-read AlePayService $alepayService
 * @property-read PaymentService $paymentService
 * @property-read Filemanager $filemanager
 */
class RequestController extends MerchantController
{
    protected $module = 'payments.requests';

    protected $moduleName = 'Giao dịch thanh toán';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var RequestRepository
     */
    public $repository;

    public $_data = [];


    public $createButtonText = 'Thanh Toán';
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
        $this->repository->setValidatorClass(UploadPaymentValidator::class);
        $this->init();
        $this->activeMenu('payments');
    }

    public function getIndex(Request $request)
    {
        return redirect()->route('merchant.payments.transactions.list');
    }

    public function getList(Request $request)
    {
        return redirect()->route('merchant.payments.transactions.list');

    }

    public function beforeGetListData($request)
    {
        $this->repository->where('user_id', $request->user()->id);
    }

    public function save(Request $request, $action = null)
    {
        extract($this->apiDefaultData);
        $user = $this->userRepository->first(['id' => $request->user()->id]);

        $validator = $this->repository->validator($request, UploadPaymentValidator::class);
        if (!$validator->success()) {
            $message = 'Dữ liệu gửi lên không hợp lệ';
            $errors = $validator->errors();
        } elseif (!($package = $this->packageRepository->first($request->order_id ? ['id' => $request->order_id] : ['@orderBy' => ['price', 'ASC']])))
            $message = 'Không có gói thanh toán nào dc cấu hình';
        elseif ($package->price == 0) {
            $user->upload_count += $package->upload_count;
            $user->save();
            return redirect()->route('merchant.3d.models.upload')->with('success', 'Chúc mừng bạn đã được cộng thêm ' . $package->upload_count . ' lượt tải file');
            $status = true;
            $data = [
                'action' => 'refresh',
                'user' => $this->userRepository->mode('mask')->detail(['id' => $user->id])
            ];
        } elseif (!($method = $this->methodRepository->first($request->payment_method_id ? ['id' => $request->payment_method_id] : []))) {
            $message = 'Phương thức thanh toán không hợp lệ';
        }
        elseif($paymentData = $this->paymentService->createUploadPayment($package, $method, $user, ['success_redirect_url' => $request->success_redirect_url, 'cancel_redirect_url' => $request->cancel_redirect_url, 'error_redirect_url' => $request->error_redirect_url])){
            return redirect($paymentData['payment']['checkout_url']);
            $status = true;
            $data = $paymentData;
        }
        else {
            $message = $this->paymentService->getErrorMessage();
        }

        return redirect()->back()->withInput()->with('error', $message);
    }


    /**
     * can thiep truoc khi tao
     *
     * @param Request $request
     * @param Arr $data
     * @return void
     */
    public function beforeCreate($request, $data)
    {
        //
        $user = $request->user();
        if (!($package = $this->packageRepository->first($request->order_id ? ['id' => $request->order_id] : ['@orderBy' => ['price', 'ASC']])))
            $message = 'Không có gói thanh toán nào dc cấu hình';
        elseif ($package->price == 0) {
            $user->upload_count += $package->upload_count;
            $user->save();
            $status = true;
            $data = [
                'action' => 'refresh',
                'user' => $this->userRepository->mode('mask')->detail(['id' => $request->user()->id])
            ];
        } elseif (!($method = $this->methodRepository->first($request->payment_method_id ? ['id' => $request->payment_method_id] : []))) {
            $message = 'Phương thức thanh toán không hợp lệ';
        } elseif ($paymentData = $this->paymentService->createConnectPayment($package, $method, $user, ['success_redirect_url' => $request->success_redirect_url, 'cancel_redirect_url' => $request->cancel_redirect_url, 'error_redirect_url' => $request->error_redirect_url])) {
            $status = true;
            $data = $paymentData;
        } else {
            $message = $this->paymentService->getErrorMessage();
        }
    }
}
