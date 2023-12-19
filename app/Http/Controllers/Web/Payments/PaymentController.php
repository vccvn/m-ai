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


    /**
     * hien thị form hoặc redirect đến trang thanh toán
     *
     * @param Request $request
     * @return void
     */
    public function vnPay(Request $request)
    {
        $order_id = session('order_id', $request->order_id);
        $bank = session('vnpay_bank', $request->vnpay_bank);
        if (!$order_id) {
            return $this->checkOrderForm($request);
        } elseif (!($order = $this->orderRepository->mode('mask')->withData()->detail(['id' => $order_id, 'payment_status' => Order::PAYMENT_PENDING]))) {
            return redirect()->back()->with([
                'error' => 'Đơn hàng không tồn tại',
            ]);
        } elseif (!$bank) {
            $page_title = 'Thanh toan ATM qua cổng VN Pay';
            $submit_url = route('web.payments.vnpay.submit');
            return $this->viewModule('online', compact('order', 'page_title', 'submit_url'));
        }
        session([
            'order_id' => $order_id,
            'vnpay_bank' => $bank
        ]);

        return $this->vnPayCreate($request, $order, $bank);
    }

    /**
     * tạo đường dẫn thanh toán
     *
     * @param Request $request
     * @param Order|OrderMask $order
     * @param string $bank
     * @return RedirectResponse
     */
    public function vnPayCreate(Request $request, $order = null, $bank = null)
    {
        // $order_id = session('order_id', $request->order_id);
        // nếu không tồn tại order
        $order = $order ? $order : (
        ($id = session('order_id', $request->order_id)) ? $this->orderRepository->mode('mask')->withFullData()->detail([
            'id' => $id,
            'status' => Order::PAYMENT_PENDING_VERIFY
        ]) : null
        );
        if (!$order) {
            return redirect()->route('web.alert')->with([
                'type' => 'error',
                'message' => 'Đơn hàng không hợp lệ',
                'link' => route('web.orders.manager'),
                'text' => 'Quản lý đơn hàng'
            ]);
        }
        // nếu không có method dc hỗ trợ
       elseif (!($methods = Helper::getPaymentMethodOptions()) || !is_countable($methods) || !($vnpay = $methods->vnpay) || !($config = $vnpay->config)) {
           return redirect()->route('web.alert')->with([
               'type' => 'error',
               'message' => 'Phương thức thanh toán chưa được thiết lập',
               'link' => route('web.orders.manager'),
               'text' => 'Quãn lý đơn hàng'
           ]);
       }
        $bank = session('vnpay_bank', $request->vnpay_bank ? $request->vnpay_bank : $bank);
        $config['return_url'] = route('web.payments.vnpay.status');
        VNPay::config($config);


        $paymentData = [
            'bank' => $bank,
            'total' => $order->total_money,
            'order_id' => $order->id,
            "note" => $order->note
        ];

        return redirect(VNPay::create($paymentData));
    }

    public function vnPayCheck(Request $request)
    {
        $methods = Helper::getPaymentMethodOptions();
        if (!is_countable($methods) || !($vnpay = $methods->vnpay) || !($config = $vnpay->config)) abort(404);
        VNPay::config($config);
        $checkArr = VNPay::check($request->all(), function ($order_id, $transaction_code, $return_codes) {
            $order = $this->orderRepository->detail([
                'id' => $order_id,
                'payment_status' => Order::PAYMENT_PENDING
            ]);
            if (!$order) {
                return $return_codes['exists'] ?? false;
            }

            //
            $data = [
                'order_id' => $order->id,
                'amount' => $order->total_money,
                'time' => date('Y-m-d H:i:s'),
                'customer_id' => $order->customer_id,
                'type' => 'payment',
                'code' => $transaction_code
            ];
            $transaction = $this->repository->create($data);
            if ($transaction) {
                if ($result = $this->repository->approve($transaction->id, true)) {
                    try {
                        if (setting()->send_mail_notification && setting()->mail_notification) {
                            $customer = $order->customer;
                            MailAlert::send([
                                'subject' => "Thông báo: Có người vừa thanh toán đơn hàng",
                                'content' => ($customer ? $customer->name : 'Có người') . " vừa thanh toán đơn hàng.<br> ID:" . $order->id
                            ]);
                        }
                    } catch (NotReportException $th) {
                        //throw $th;
                    }
                    return $return_codes['success'] ?? true;
                }
            }

            return $return_codes['fail'] ?? false;
        });
        return $this->json($checkArr);
    }

    public function vnPayStatus(Request $request)
    {
        $methods = Helper::getPaymentMethodOptions();
        if (!is_countable($methods) || !($vnpay = $methods->vnpay) || !($config = $vnpay->config)) abort(404);

        VNPay::config($config);
        $status = VNPay::status($request->all());
        $alertType = 'error';
        $alertTitle = 'Thanh toán không thành công!';
        $alertMessage = 'Chữ ký không hợp lệ';
        $redirectLink = route('web.orders.manager');
        $btnText = 'Quản lý đơn hàng';

        if ($status) {
            $alertType = 'success';
            $alertTitle = 'Thanh toán không thành công!';
            $alertMessage = 'Chúc mừng bạn đã thanh toán dịch vụ thành công!';
        } elseif ($status === false) {
            $alertMessage = 'Đã xảy ra lỗi trong quá trình thanh toán';
        }

        return redirect()->route('web.alert')->with([
            'type' => $alertType,
            'title' => $alertTitle,
            'message' => $alertMessage,
            'link' => $redirectLink,
            'text' => $btnText
        ])->with('disableBack', 1);
    }

    /**
     * hien thị form hoặc redirect đến trang thanh toán
     *
     * @param Request $request
     * @return void
     */
    public function momo(Request $request)
    {
        $order_id = session('order_id', $request->order_id);
        if (!$order_id) {
            return $this->checkOrderForm($request);
        } elseif (!($order = $this->orderRepository->mode('mask')->withData()->detail(['id' => $order_id, 'payment_status' => Order::PAYMENT_PENDING]))) {
            return redirect()->back()->with([
                'error' => 'Đơn hàng không tồn tại',
            ]);
        }
        return $this->momoCreate($request, $order);
    }

    /**
     * tạo đường dẫn thanh toán
     *
     * @param Request $request
     * @param Order|OrderMask $order
     * @param string $bank
     * @return RedirectResponse
     */
    public function momoCreate(Request $request, $order = null)
    {
        // $order_id = session('order_id', $request->order_id);
        // nếu không tồn tại order
        $order = $order ? $order : (
        ($id = session('order_id', $request->order_id)) ? $this->orderRepository->mode('mask')->withFullData()->detail([
            'id' => $id,
            'status' => Order::PAYMENT_PENDING_VERIFY
        ]) : null
        );
//        $methods = Helper::getPaymentMethodOptions();
        if (!$order) {
            return redirect()->route('web.alert')->with([
                'type' => 'error',
                'message' => 'Đơn hàng không hợp lệ',
                'link' => route('web.orders.manager'),
                'text' => 'Quản lý đơn hàng'
            ]);
        }
        // nếu không có method dc hỗ trợ
        $methods = Helper::getPaymentMethodOptions();
        foreach ($methods as $method){
            if($method->method == 'momo'){
                $momo_config = $method->config;
                $config = $momo_config;//['config'];
                $config['redirectUrl'] = route('web.payments.momo.status');
                $config['ipnUrl'] = route('web.payments.momo.check');
                Momo::config($config);

                $paymentData = [
                    'total' => $order->total_money,
                    'orderId' => $order->id,
                    "note" => $order->note
                ];
                return redirect(Momo::create($paymentData));
            }
        }
        return redirect()->route('web.alert')->with([
            'type' => 'error',
            'message' => 'Phương thức thanh toán chưa được thiết lập',
            'link' => route('web.orders.manager'),
            'text' => 'Quản lý đơn hàng'
        ]);
    }

    public function momoStatus(Request $request)
    {
        $methods = Helper::getPaymentMethodOptions();
        foreach ($methods as $method) {
            if ($method->method == 'momo') {
                $momo_config = $method->config;
                $config = $momo_config;//['config'];
                $partnerCode = $config['partner_code'];
                $accessKey = $config['access_key'];
                $secretKey = $config['secret_key'];
            }
        }

        if (empty($partnerCode) || empty($accessKey) || empty($secretKey)){
            return redirect()->route('web.alert')->with([
                'type' => 'error',
                'message' => 'Thông tin thanh toán không hợp lệ',
                'link' => route('web.orders.manager'),
                'text' => 'Quãn lý đơn hàng'
            ])->with('disableBack', 1);
        }

        $order = $this->orderRepository->detail([
            'id' => $request->orderId,
            'payment_status' => Order::PAYMENT_PENDING
        ]);
        $inputData = [
            'partnerCode' => $partnerCode,
            'requestId' => $request->requestId,
            'orderId' => $order->id,
        ];
        $inputData['signature'] = Momo::generateSignature($inputData, $accessKey, $secretKey);
        $inputData["lang"] = "vi";
        $header = array(
            'Content-Type: application/json'
        );
        try {
            $result = curl_helper(Momo::VERIFY_URL, json_encode($inputData), $header);
            $jsonResult = json_decode($result, true);

        } catch (\Exception $exception) {
            $exception->getMessage();
        }
        $alertType = 'error';
        $alertTitle = 'Thanh toán không thành công!';
        $redirectLink = route('web.orders.manager');
        $btnText = 'Quản lý đơn hàng';
        $alertMessage = $request->message ? $request->message : 'Đã xảy ra lỗi trong quá trình thanh toán';

        if (!$order || (int)$order->total_money != $request->amount || $jsonResult['resultCode'] != $request->resultCode) {
            return redirect()->route('web.alert')->with([
                'type' => $alertType,
                'title' => $alertTitle,
                'message' => $alertMessage,
                'link' => $redirectLink,
                'text' => $btnText
            ])->with('disableBack', 1);
        }

        $data = [
            'order_id' => $request->orderId,
            'amount' => $order->total_money,
            'time' => date('Y-m-d H:i:s'),
            'customer_id' => $order->customer_id,
            'type' => $request->orderType,
            'code' => $request->transId
        ];
        $transaction = $this->repository->create($data);

        if ($transaction && $jsonResult['resultCode'] == 0) {
            if ($result = $this->repository->approve($transaction->id, true)) {
                try {
                    if (setting()->send_mail_notification && setting()->mail_notification) {
                        $customer = $order->customer;
                        MailAlert::send([
                            'subject' => "Thông báo: Có người vừa thanh toán đơn hàng",
                            'content' => ($customer ? $customer->name : 'Có người') . " vừa thanh toán đơn hàng.<br> ID:" . $order->id
                        ]);
                    }
                } catch (NotReportException $th) {
                    //throw $th;
                }
                $alertType = 'success';
                $alertTitle = 'Thanh toán không thành công!';
                $alertMessage = 'Chúc mừng bạn đã thanh toán dịch vụ thành công!';
                $request->session()->put('is_empty_cart', 1);
            } else {
                $alertMessage = 'Đã xảy ra lỗi trong quá trình thanh toán';
            }
        } else {
            $alertMessage = $request->message ? $request->message : 'Đã xảy ra lỗi trong quá trình thanh toán';
            $this->repository->decline($transaction->id, false);
        }
        return redirect()->route('web.alert')->with([
            'type' => $alertType,
            'title' => $alertTitle,
            'message' => $alertMessage,
            'link' => $redirectLink,
            'text' => $btnText
        ])->with('disableBack', 1);
    }
}
