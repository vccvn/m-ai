<?php

namespace App\Http\Controllers\Merchant\Payments;

use App\Excels\ReportPaymentDownloader;
use App\Http\Controllers\Admin\CPanel\Filemanager;
use App\Http\Controllers\Merchant\MerchantController;
use App\Models\User;
use App\Repositories\Accounts\AgentRepository;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Payments\PackageRepository;
use App\Repositories\Payments\RequestRepository;
use App\Repositories\Payments\TransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Payments\AlePayService;
use App\Services\Payments\PaymentService;
use App\Validators\Payments\ServicePaymentValidator;
use App\Validators\Payments\UploadPaymentValidator;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

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
class TransactionController extends MerchantController
{
    protected $module = 'payments.transactions';

    protected $moduleName = 'Thanh toán';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var TransactionRepository
     */
    public $repository;


    /**
     * @var AgentRepository
     */
    protected $agentRepository = null;

    public $_data = [];


    public $createButtonText = 'Thanh Toán';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        TransactionRepository $repository,
        UserRepository $userRepository,
        PackageRepository $packageRepository,
        MethodRepository $methodRepository,
        AlePayService $alepayService,
        PaymentService $paymentService,
        Filemanager $filemanager,
        AgentRepository $agentRepository

    )
    {
        $this->repository = $repository;
        $this->alepayService          = $alepayService;
        $this->userRepository         = $userRepository;
        $this->methodRepository       = $methodRepository;
        $this->packageRepository      = $packageRepository;
        $this->filemanager            = $filemanager;
        $this->paymentService         = $paymentService;
        $this->agentRepository        = $agentRepository;
        $this->repository->setValidatorClass(UploadPaymentValidator::class);
        $this->init();
        $this->activeMenu('payments');

    }


    public function beforeGetListData($request)
    {
        $this->repository->where('user_id', $request->user()->id);
    }

    public function export(Request $request)
    {
        extract($this->apiDefaultData);
        $list = $request->ids?  $this->repository->mode('mask')->where('user_id', $request->user()->id)->buildDownloadQuery()->getData(['id' => $request->ids]):$this->repository->mode('mask')->getResults($request, ['@paginate' => false]);

        $times = [];
        if($request->from_date) $times[] = $request->from_date;
        if($request->to_date) $times[] = $request->to_date;

        if(!$times) $times[] = date('Ymd-his');

        $filename = 'Lich-su-' . implode('-', $times) . '.xlsx';
        $rpd = new ReportPaymentDownloader($filename);
        $rpd->loadTemplate();
        $rpd->addReport($list);
        if($rpd->save()){
            $status = true;
            $data = [
                'url' => route('merchant.payments.transactions.download', ['filename' => $filename])
            ];
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

    public function download(Request $request)
    {
        if(!$request->filename || !file_exists($path = storage_path('excels/downloads/' . $request->filename)))
            abort(404);
        $file     = File::get($path);
        $type     = File::mimeType($path);
        $response = Response::download($path, $request->filename,array(
            'Content-Type: ' . $type,
          ));
        return $response;
    }
    // public function beforeGetListData($request)
    // {
    //     $this->repository->where('user_id', $request->user()->id);
    // }

    public function save(Request $request, $action = null)
    {
        extract($this->apiDefaultData);
        $user = $this->userRepository->first(['id' => $request->user()->id]);

        $validator = $this->repository->validator($request, ServicePaymentValidator::class);
        if (!$validator->success()) {
            $message = 'Dữ liệu gửi lên không hợp lệ';
            $errors = $validator->errors();
        } elseif (!($package = $this->packageRepository->first($request->order_id ? ['id' => $request->order_id] : ['@orderBy' => ['price', 'ASC']])))
            $message = 'Không có gói thanh toán nào dc cấu hình';
        elseif($package->role == User::AGENT && !$this->agentRepository->checkAgentMonthBalance($package->agent_id, $package->quantity))
            $message = 'Số dư của đại lý của bạn không đủ để thực hiện giao dịch';
        elseif ($package->wholesale_price == 0) {
            $this->agentRepository->updateMonthBalance($user->id, $package->quantity);
            return redirect()->route('merchant.payments.transactions.list')->with('success', 'Chúc mừng bạn đã được cộng thêm ' . $package->quantity . ' tháng sử dụng');
        } elseif (!($method = $this->methodRepository->first($request->payment_method_id ? ['id' => $request->payment_method_id] : []))) {
            $message = 'Phương thức thanh toán không hợp lệ';
        }
        elseif($paymentData = $this->paymentService->createServicePayment($package, $method, $user, ['success_redirect_url' => $request->success_redirect_url, 'cancel_redirect_url' => $request->cancel_redirect_url, 'error_redirect_url' => $request->error_redirect_url])){
            return redirect($paymentData['payment']['checkout_url']);
            $status = true;
            $data = $paymentData;
        }
        else {
            $message = $this->paymentService->getErrorMessage();
        }

        return redirect()->back()->withInput()->with('error', $message);
    }
}
