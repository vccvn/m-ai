<?php

namespace App\Http\Controllers\Web\Payments;

use App\Engines\MailAlert;
use App\Engines\Payments\Momo;
use App\Engines\Payments\VNPay;
use App\Exceptions\NotReportException;
use App\Http\Controllers\Web\WebController;
use App\Masks\Orders\OrderMask;
use App\Models\Order;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Files\FileRepository;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Transactions\OrderTransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Validators\Transactions\TransferPaymentValidator;
use Gomee\Engines\Helper;
use Gomee\Helpers\Arr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * thanh toán
 * @property ProductRepository $productRepository
 */
class PaymentController extends WebController
{
    protected $module = 'payments';

    protected $moduleName = 'Thanh toán';

    protected $flashMode = true;

    /**
     * customer
     *
     * @var CustomerRepository $customerRepository
     */
    protected $customerRepository = null;
    /**
     * customer
     *
     * @var UserRepository $userRepository
     */
    protected $userRepository = null;

    /**
     * orderRepository
     *
     * @var OrderRepository
     */
    protected $orderRepository = null;

    /**
     * email token
     *
     * @var EmailTokenRepository
     */
    protected $emailTokenRepository = null;


    /**
     * file
     *
     * @var FileRepository
     */
    protected $fileRepository = null;


    /**
     * giao dịch
     *
     * @var OrderTransactionRepository
     */
    public $repository = null;


    /**
     * payment method
     *
     * @var MethodRepository
     */
    public $paymentMethodRepository = null;

    /**
     * order
     *
     * @var \App\Models\Order
     */
    protected $order = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        // OrderTransactionRepository $orderTransactionRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        EmailTokenRepository $emailTokenRepository,
        FileRepository $fileRepository
    )
    {
        // $this->repository = $orderTransactionRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository->notTrashed();
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository->notTrashed();
        $this->emailTokenRepository = $emailTokenRepository;
        $this->fileRepository = $fileRepository;

        // $this->repository->setValidatorClass(TransferPaymentValidator::class);
        $this->init();
    }

    /**
     * hiển thị form nhập thông tin order
     *
     * @param Request $request
     */
    public function checkOrderForm(Request $request)
    {
        $page_title = 'Thanh toán Đơn hàng';
        $this->breadcrumb->add($page_title, URL::current());
        return $this->viewModule('order', compact('page_title'));
    }

    /**
     * kiễm tra thông tin đơn hàng
     *
     * @param Request $request
     * @return void
     */
    public function checkOrderPayment(Request $request)
    {
        if (!$request->contact || !$request->order_code || !($order = $this->orderRepository->checkOrderByContact($request->contact, $request->order_code)) || !$order->paymentMethod || $order->paymentMethod->method == 'cod') {
            $mess = 'Thông tin đơn hàng hoặc Phương thức thanh toán không hợp lệ';
            return redirect()->back()->with(['error' => $mess])->withInput($request->all());
        }
        session(['order_code' => $order->code]);
        return redirect()->route('web.payments.' . $order->paymentMethod->method);
    }


    /**
     * xem trang thanh toan chuyễn khoản
     *
     * @param Request $request
     * @return void
     */
    public function transfer(Request $request)
    {
        $page_title = 'Thanh toán chuyển khoản';
        $this->breadcrumb->add($page_title, URL::current());


        if (!($order_code = session('order_code'))) {
            return $this->checkOrderForm($request);
        } elseif (!($order = $this->orderRepository->mode('mask')->withFullData()->detail(['code' => $order_code]))) {
            return redirect()->back()->with([
                'error' => 'Đơn hàng không tồn tại',
            ]);
        }


        return $this->viewModule('transfer', compact('page_title', 'order'));
    }

    /**
     * Xác thực giao dịch chuyển khoản
     *
     * @param Request $request
     * @return void
     */
    public function verifyTransfer(Request $request)
    {
        //$d = $this->repository->validate($request, TransferPaymentValidator::class);
        $data = new Arr();
        $order_code = $request->order_code ? $request->order_code : $request->session()->get('order_code');
        $order = $this->orderRepository->detail(['code' => $order_code]);

        $data->order_id = $order->id;
        $data->amount = $order->total_money;
        $data->time = date('Y-m-d H:i:s');
        $data->customer_id = $order->customer_id;
        $data->type = 'payment';


        $result = $this->repository->create($data->all());
        $date_path = date('Y/m/d');
        if ($file = $this->uploadImage($request, 'image', null, get_content_path('/files/' . $date_path))) {
            $upload_by = ($user = $request->user()) ? $user->id : 0;
            $this->fileRepository->create(array_merge($file->all(), [
                'upload_by' => $upload_by,
                'sid' => md5(microtime() . uniqid()),
                'original_filename' => $file->filename,
                'date_path' => $date_path,
                'privacy' => 'public',
                'ref' => 'transaction',
                'ref_id' => $result->id
            ]));
        }
        // Forget a single key...
        $request->session()->forget('order_code');

        if (setting()->send_mail_notification && setting()->mail_notification) {
            $customer = $result->customer;
            MailAlert::send([
                'subject' => "Thông báo: Có người vừa gửi thông tin thanh toán đơn hàng",
                'content' => ($customer ? $customer->name : 'Có người') . " vừa gửi thông tin thanh toán đơn hàng.\n ID:" . $result->id
            ]);
        }

        return redirect()->route('web.alert')->with([
            'type' => 'success',
            'message' => 'Chúc mừng bạn đã gửi biên lai thanh toán thành công! Vui lòng chờ giây lát trong khi chúng tôi xác thực giao dịch',
            'link' => route('web.products'),
            'text' => 'Tiếp tục mua sắm'
        ])->with('disableBack', 1);
    }


}
