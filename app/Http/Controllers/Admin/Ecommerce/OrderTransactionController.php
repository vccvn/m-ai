<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use Gomee\Helpers\Arr;
use App\Repositories\Transactions\OrderTransactionRepository;
use App\Repositories\Files\FileRepository;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Customers\CustomerRepository;

class OrderTransactionController extends AdminController
{
    protected $module = 'transactions';

    protected $moduleName = 'Giao dịch';

    protected $flashMode = true;

    protected $statusValues = [
        'approve' => 1,
        'unapprove' => 0,
        'decline' => -1,
        'restore' => 0
    ];

    /**
     * @var FileRepository $fileRepository
     * Quản lý file upload
     */
    protected $fileRepository;

    /**
     * order transaction
     *
     * @var OrderTransactionRepository $repository
     */
    public $repository = null;

    /**
     * @var OrderRepository $orderRepository
     */
    public $orderRepository;
    /**
     * Create a new controller instance.
     *
     * @param OrderTransactionRepository $orderTransactionRepository
     * @param OrderRepository $orderRepository
     * @param FileRepository $fileRepository
     * @param CustomerRepository $customerRepository
     *
     * @return void
     */
    public function __construct(OrderTransactionRepository $orderTransactionRepository, OrderRepository $orderRepository, FileRepository $fileRepository, CustomerRepository $customerRepository)
    {
        $this->repository = $orderTransactionRepository;
        $this->fileRepository = $fileRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->init();
        $this->activeMenu('transactions');
    }

    /**
     * on start
     *
     * @return void
     */
    public function start()
    {
        add_js_data('transaction_order', [
            'urls' => [
                'get_order_options' => route($this->routeNamePrefix . 'orders.select-options'),
                'get_detail' => $this->getModuleRoute('resource-detail'),
                'approve' => $this->getModuleRoute('status', ['slug' => 'approve']),
                'unapprove' => $this->getModuleRoute('status', ['slug' => 'unapprove']),
                'decline' => $this->getModuleRoute('status', ['slug' => 'decline']),
                'restore' => $this->getModuleRoute('status', ['slug' => 'restore']),
                'delete' => $this->getModuleRoute('move-to-trash')
            ]
        ]);
        add_js_src('static/manager/js/transaction.order.js');
    }


    /**
     * xử lý data trước khi lưu
     *
     * @param Request $request
     * @param Arr $data
     * @return void
     */
    public function beforeSave(Request $request, Arr $data)
    {
        if (!$request->time) {
            $data->time = date('Y-m-d H:i:s');
        }

        if (!($order = $this->orderRepository->first(['code' => $data->order_code]))) {
            return redirect()->back()->with('error', 'Đơn hàng không tồn tại')->withErrors(['order_code.check_order' => 'Mã đơn hàng không hợp lệ']);
        }
        $data->ref_id = $order->id;
    }

    /**
     * lưu biên lai sau khi lưu giao dịch
     *
     * @param Request $request
     * @param \App\Models\Transaction $transaction
     * @param Arr $data
     * @return void
     */
    public function afterSave(Request $request, $transaction, Arr $data)
    {
        $date_path = date('Y/m/d');
        if ($file = $this->uploadImage($request, 'image', null, get_content_path('/files/' . $date_path))) {
            $this->fileRepository->deleteRefFileIgnoreList('transaction', $transaction->id, []);
            $upload_by = ($user = $request->user()) ? $user->id : 0;
            $this->fileRepository->create(array_merge($file->all(), [
                'upload_by' => $upload_by,
                'sid' => md5(microtime() . uniqid()),
                'original_filename' => $file->filename,
                'date_path' => $date_path,
                'privacy' => 'public',
                'ref' => 'transaction',
                'ref_id' => $transaction->id
            ]));
        }
    }

    /**
     * thay doi trang thnai giao dich
     *
     * @param Request $request
     * @param string $statusSlug
     * @return JSON
     */
    public function changeStatus(Request $request, $statusSlug)
    {
        extract($this->apiDefaultData);
        $sttKeys = array_flip($this->statusValues);
        $stt = array_key_exists($statusSlug, $sttKeys) ? ((int) $statusSlug) : (array_key_exists($k = strtolower($statusSlug), $this->statusValues) ? $this->statusValues[$k] : null
        );
        if (!is_null($stt) && $detail = $this->repository->updateStatusAndSendEmail($request->id, $stt)) {

            $data = $detail;
            $status = true;
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

    public function beforeGetResourceDetail($request)
    {
        $this->repository->mode('mask');
    }



    /**
     * thay đổi trạng thái đơn hàng
     * @param Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionDetail(Request $request)
    {
        extract($this->apiDefaultData);
        if ($request->id && $detail = $this->repository->mode('mask')->detail($request->id)) {
            $data = $detail;
            $status = true;
        } else {
            $message = "Không tìm thấy mục yêu càu";
        }
        return $this->json(compact(...$this->apiSystemVars));
    }
}
