<?php

namespace App\Http\Controllers\Web\eCommerce;

use App\Http\Controllers\Web\WebController;

use App\Engines\MailAlert;
use App\Models\Order;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Orders\CartRepository;
use App\Repositories\Orders\OrderAddressRepository;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Payments\MethodRepository;
use App\Repositories\Promos\PromoRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Mailers\Mailer;
use App\Services\System\NoticeService;
use App\Validators\Orders\PlaceValidator;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use Gomee\Mailer\Email;
use Illuminate\Support\Facades\URL;

/**
 * @property CartRepository $cartRepository
 * @property PromoRepository $promoRepository
 */
class OrderController extends WebController
{
    protected $module = 'orders';

    protected $moduleName = 'Order';

    protected $flashMode = true;

    /**
     * customer
     *
     * @var \App\Repositories\Customers\CustomerRepository $customerRepository
     */
    protected $customerRepository = null;
    /**
     * customer
     *
     * @var \App\Repositories\Users\UserRepository $userRepository
     */
    protected $userRepository = null;

    /**
     * email token
     *
     * @var \App\Repositories\Emails\EmailTokenRepository
     */
    protected $emailTokenRepository = null;

    /**
     * order repository
     *
     * @var \App\Repositories\Orders\OrderRepository
     */
    public $repository = null;

    /**
     * quản lý địa chỉ của order
     *
     * @var \App\Repositories\Orders\OrderAddressRepository
     */
    protected $orderAddressRepository = null;

    /**
     * payment method
     *
     * @var MethodRepository
     */
    public $paymentMethodRepository = null;


    protected $cartRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository,
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        EmailTokenRepository $emailTokenRepository,
        OrderAddressRepository $orderAddressRepository,
        PromoRepository $promoRepository,
        MethodRepository $paymentMethodRepository
    )
    {
        $this->repository = $orderRepository->mode('mask');
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository->notTrashed();
        $this->emailTokenRepository = $emailTokenRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->cartRepository = $cartRepository;
        $this->promoRepository = $promoRepository->notTrashed()->mode('mask');
        $this->paymentMethodRepository = $paymentMethodRepository->addDefaultCondition('status', 'status', 1)->notTrashed();
        $this->setting = setting();
        $this->siteinfo = siteinfo();
        $this->init();
    }



    public function viewDetail(Request $request)
    {
        $user = $request->user();
        $customer = $this->customerRepository->getCurrentCustomer();
        // nếu không có thông tin người dùng và thông tin khách hàng hiện tại thì diều hướng đến trang đăng nhập khách hàng
        if(!$customer && $user){
            $customer = $this->customerRepository->findBy('user_id', $user->id);
        }
        if(!$user && !$customer){
            return redirect()->route('web.orders.customer.login');
        }

        $order = $this->repository->getCustomerOrderDetail($customer?$customer->id:0, $user?$user->id:0, ['code' => $request->code]);
        $page_title = 'Chi tiết đơn hàng';
        $this->breadcrumb->add($page_title);
        return $this->viewModule('detail', compact('order', 'page_title'));


    }

    /**
     * xem trang quản lý đơn hàng
     *
     * @param Request $request
     * @return View
     */
    public function manager(Request $request)
    {
        $user = $request->user();
        $customer = $this->customerRepository->getCurrentCustomer();
        // nếu không có thông tin người dùng và thông tin khách hàng hiện tại thì diều hướng đến trang đăng nhập khách hàng
        if(!$customer && $user){
            $customer = $this->customerRepository->findBy('user_id', $user->id);
        }
        if(!$user && !$customer){
            // return redirect()->route('web.account.login', ['next' => URL::current()]);
            return redirect()->route('web.customers.login');
        }


        // lấy danh sách các tang thai cua don hang
        $statusList = get_order_status_list();

        $keys = get_order_status_keys();
        $key = strtolower($request->status_key);
        $allows = get_customer_access_alow_status_list();
        $args = [];
        $list_title = "Tất cả đơn hàng";
        if($key){
            if(array_key_exists($key, $keys)){
                $key = $keys[$key];
            }elseif (!in_array($key, $keys)) {
                return $this->view('errors.404');
            }
            if(!in_array($key, $allows) || !($status = $statusList[$key]??null)){
                return $this->view('errors.404');
            }
            if($key == 'pending-payment' || $key == 'paid'){
                $args['payment_status'] = $status['code'];
                $args['status'] = [
                    Order::PENDING_VERIFY,       // Đang chờ xác nhận
                    Order::VERIFIED,             // đã xác thực
                    Order::PENDING,              //
                    Order::PROCESSING,           // đang xử lý
                    Order::COMPLETED,            // đã hoàn thành
                ];
            }
            else{
                $args['status'] = $status['code'];
            }

            $list_title = $status['label'];
        }


        $orders = $this->repository->paginate(10)->getCustomerOrders(
            $customer?$customer->id:0,
            $user?$user->id:0,
            $args
        );

        $page_title = 'Quản lý đơn hàng';
        $this->breadcrumb->add($page_title);
        $data = [
            'key' => $key,
            'status_list' => $statusList->copy($allows),
            'orders' => $orders,
            'payment_methods' => get_order_payment_methods(),
            'status_keys' => array_flip($keys),
            'page_title' => $page_title,
            'list_title' => $list_title
        ];

        return $this->viewModule('list', $data);
    }



    /**
     * đặt hàng
     *
     * @param Request $request
     * @return Redirect
     */
    public function placeOrder(Request $request)
    {

        // kiểm tra cart
        $buy_now_cart_id = session('buy_now_cart_id');
        if($request->cart_type == 'buy-now' && $buy_now_cart_id){
            $this->cartRepository->setBuyNowCart($request);
        }
        $cart = $this->cartRepository->getOrderCart();

        if(!$cart || count($cart->items) == 0) return redirect()->back()->withInput();
        // validate
        $validator = $this->repository->validator($request, PlaceValidator::class);
        if(!$validator->success() || !($payment = $this->paymentMethodRepository->detail(['method' => $request->payment_method]))){
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all())->with('warning_message', 'Một vài thông tin đặt hàng có vẻ không hợp lệ');
        }
        // $order = $this->repository->importFromCart($cart);
        // xóa điều kiện where cart
        // $this->repository->removeDefaultConditions();
        // lấy data
        $data = new Arr($validator->inputs());
        if($data->coupon_code){
            $data->coupon = $data->coupon_code;
        }
        // hóa đơn
        $billingData = $data->prefix('billing_', true, null, true);
        // thông tin giao hàng
        $shippingData = $data->ship_to_different_address ? $data->prefix('shipping_', true, null, true) : $billingData;



        $billing = new Arr($billingData);
        $shipping = new Arr($shippingData);

        $data->type = 'order';
        $data->status = Order::VERIFIED;
        $data->ordered_at = date('Y-m-d H:i:s');
        $data->payment_method_id = $payment->id;
        $user = $request->user();
        $orderConfirm = false;
        $customerFields = ['name', 'email', 'phone_number', 'address'];
        $confirmName = null;
        $confirmEmail = null;
        // lấy khách hàng hiện tại
        $customer = $this->customerRepository->getCurrentCustomer();
        $createCustomer = false;
        $userConfirm = false;
        $customerData = $billingData;
        $confirmShipping = $data->ship_to_different_address && ($billing->email != $shipping->email || $billing->phone_number != $shipping->phone_number);
        $confirmODR = ecommerce_setting()->confirm_order;
        $user_id = null;
        if(!$user){
            if($payment->method == "cod") {
                $orderConfirm = $confirmODR;

                $confirmName = $confirmShipping ? $shipping->name : $billing->name;
                $confirmEmail = $confirmShipping ? $shipping->email : $billing->email;
            }
            // $createCustomer = true;
            // nếu có yêu cầu tạo tài khoản
            if($data->create_account && !$this->userRepository->countBy('email', $billing->email)){
                $userData = $billingData;
                $userData['username'] = $this->userRepository->getUsernameByEmail($billing->email);
                $userData['password'] = $data->password;
                $user = $this->userRepository->create($userData);
                $userConfirm = true;
            }
            $customer = $this->customerRepository->createDataIfNotExists($customerData);
        }
        else{
            // nếu đã có tài khoản khách hàng thì nên liên kết
            $data->user_id = $user->id;
            $user_id = $user->id;
            if($userCustomer = $this->customerRepository->findBy('user_id', $user->id)){
                $customer = $userCustomer;
            }
            // nếu (không) có một tài khoản khách hàng trùng vs email người dùng
            elseif(!($cus = $this->customerRepository->findBy('email', $user->email))){
                $customerData['user_id'] = $user->id;
                $customerData['email'] = $user->email;
                $customerData['name'] = $user->name;

                $customer = $this->customerRepository->createDataIfNotExists($customerData);
            }
            // nếu có và chưa có user id
            elseif(!$cus->user_id){
                $customer = $this->customerRepository->update($cus->id, ['user_id' => $user->id]);
            }

            if(!$orderConfirm && $payment->method == "cod" && $confirmShipping && $confirmODR) {
                $orderConfirm = true;
                $confirmName = $shipping->name;
                $confirmEmail = $shipping->email;
            }
        }


        if($customer) $data->customer_id = $customer->id;

        // nếu ko phải xác thực đơn hàng thì sẽ chuyển trạng thái đơn hàng tùy thuộc phương thúc thanh toán
        // if(!$orderConfirm && $payment->method != 'cod'){
        $data->payment_status = Order::PAYMENT_PENDING;
        // }
        $data->delivery_status = Order::DELIVERY_PENDING;

        $order = $this->repository->create($data->all());

        $products = $this->repository->importFromCart($cart, $order->id);
        // lưu thông tin hóa đơn và thông tin giao hàng
        if(!$products){
            return redirect()->back()->withInput($request->all())->with('warning_message', 'Hệ thông gặp chút sự cố. bạn thử lại sau giây lát');
        }
        $billing = $this->orderAddressRepository->updateAddress($order->id, 'billing', $billingData);
        $shipping = $this->orderAddressRepository->updateAddress($order->id, 'shipping', $shippingData);
        // dd($this->repository->getCartDetail($cart->id));
        // chuyển giỏ hàng thành đơn hàng
        $order = $this->repository->updateOrderData($order)->withData()->detail($order->id);


        if($products){
            $this->cartRepository->empty();
        }

        if($buy_now_cart_id){
            $request->session()->forget('buy_now_cart_id');
        }


        if($order->coupon){
            $this->promoRepository->downPromoTotal($order->coupon, $user_id);
        }



        $alert = [];

        $mailFrom = $this->siteinfo->email('no-reply@' . get_non_www_domain());
        $company = $this->siteinfo->company($this->siteinfo->site_name('Crazy Support'));

        if($orderConfirm){
            $order->status = Order::PENDING_VERIFY;
            $order->save();
            $alert = [
                'type' => 'success',
                'message' => 'Bạn đã đặt hàng thành công! tuy nhiên chúng tôi cần xác minh thông tin của bạn. Vui lòng kiểm tra hộp thư đến để xác minh và hoàn tất quá trình dặt hàng'
            ];
            $emailToken = $this->emailTokenRepository->createToken($confirmEmail, 'confirm', 'order', $order->id);
            $data = [
                'url' => route('web.orders.confirm', [
                    'token' => $emailToken->token
                ]),
                'code' => $emailToken->code,
                'email' => $confirmEmail,
                'name' => $confirmName,
                'order' => $order
            ];


            Mailer::from($mailFrom, $company)
                ->to($confirmEmail, $confirmName)
                ->replyTo($mailFrom, $company)
                ->subject("Xác thực đơn hàng")
                ->body('mails.order-confirm')
                ->data($data)
                ->send();
            // ->sendAfter(1);
        }
        elseif($payment->method == 'cod'){
            $alert = [
                'type' => 'success',
                'message' => 'Chúc mừng bạn đã đặt hàng thành công! Chúng tối sẽ liên hệ và giao hàng cho bạn trong thời gian sớm nhất',
                'link' => route('web.products'),
                'text' => 'Tiếp tục mua sắm'
            ];
            Mailer::from($mailFrom, $company)
                ->to($billing->email, $billing->name)
                ->replyTo($mailFrom, $company)
                ->subject("Thông báo: Đặt hàng thành công")
                ->body('mails.order-place-success')
                ->data([
                    'order' => $order,
                    'name' => $billing->name,
                    'email' => $billing->email,
                    'phone_number' => $billing->phone_number
                ])
                ->send();


            if($this->setting->send_mail_notification){

                $maillist = array_filter(explode(',', $this->setting->mail_notification), function($e){return is_email($e);});
                if($maillist){
                    NoticeService::sendNoticeByMails($maillist, [
                        'title' => 'Có đơn hàng mới! #'.$order->code,
                        'message' => $billing->name." vừa đặt hàng trên trang của bạn.\n Mã đơn hàng: ".$order->code,
                        'ref' => 'order',
                        'ref_id' => $order->id
                    ]);
                    Mailer::from($mailFrom, $company)
                        ->to($maillist)
                        ->replyTo($mailFrom, $company)
                        ->subject("Thông báo: Có người đặt hàng")
                        ->body('mails.simple-alert')
                        ->data(['content' => $billing->name . " vừa đặt hàng trên trang của bạn.\n Mã đơn hàng: " . $order->code])
                        ->sendAfter(1);
                }

            }
        }
        else{

            if($payment->method == 'vnpay'){
                $session = [
                    'vnpay_bank' => $data->vnpay_bank
                ];
            }else{
                $session = [

                ];
            }




            if($this->setting->send_mail_notification){

                $maillist = array_filter(explode(',', $this->setting->mail_notification), function($e){return is_email($e);});
                if($maillist){
                    NoticeService::sendNoticeByMails($maillist, [
                        'title' => 'Có đơn hàng mới! #'.$order->code,
                        'message' => $billing->name." vừa đặt hàng trên trang của bạn.\n Mã đơn hàng: ".$order->code,
                        'ref' => 'order',
                        'ref_id' => $order->id
                    ]);
                    Mailer::from($mailFrom, $company)
                        ->to($maillist)
                        ->replyTo($mailFrom, $company)
                        ->subject("Thông báo: Có người đặt hàng")
                        ->body('mails.simple-alert')
                        ->data(['content' => $billing->name . " vừa đặt hàng trên trang của bạn.\n Mã đơn hàng: " . $order->code])
                        ->sendAfter(1);
                }

            }


            $session['order_id'] = $order->id;
            $session['order_code'] = $order->code;
            $session['is_empty_cart'] = 1;
            session($session);
            return redirect()->route('web.payments.'.$payment->method)->with('disableBack', 1);
        }
        $request->session()->put('is_empty_cart', 1);
        if($this->cartRepository->useCookie){

            return redirect()->route('web.alert')->with($alert)->withCookie(cookie('cart_id', null, -1))->with('disableBack', 1);
        }
        return redirect()->route('web.alert')->with($alert)->with('disableBack', 1);

    }




    /**
     * hủy đơn hàng
     *
     * @param Request $request
     * @return json
     */
    public function cancel(Request $request)
    {
        extract($this->apiDefaultData);

        if(!$request->id || !($order = $this->repository->withFullData()->detail($request->id))){
            $message = "Đơn hàng không hợp lệ hoặc không được tìm thấy!";
        }elseif (!$order->canCancel() || !($cancelOrder = $this->repository->cancel($order->id))) {
            $message = "Đơn hàng này đang được vận chuyển nên không thể hủy được nữa!";
        }
        else{
            $data = $cancelOrder;
            $status = true;
            MailAlert::send([
                'subject' => "Thông báo: Có người hủy đặt hàng",
                'content' => $order->billing->name." vừa hủy đơn hàng.\n Mã đơn hàng:".$order->code
            ]);
        }

        return $this->json(compact($this->apiSystemVars));
    }

    /**
     * Xác thực đơn hàng
     *
     * @param Request $request
     * @param string $token
     * @return void
     */
    public function confirmOrder(Request $request, $token = null)
    {
        if (!$token) $token = $request->token;
        if (!($emailToken = $this->emailTokenRepository->checkRoken($token, 'confirm')) || $emailToken->ref !='order' || !$emailToken->ref_id || !($order = $this->repository->verify($emailToken->ref_id))) {
            return redirect()->route('web.alert')->with([
                'type' => 'warning',
                'message' => 'Token không hợp lệ'
            ]);
        }

        MailAlert::send([
            'subject' => "Thông báo: Có người đặt hàng",
            'content' => $order->billing->name." vừa đặt hàng trên trang của bạn.\n Mã đơn hàng:".$order->code
        ]);
        session(['customer_id' => $order->customer_id]);
        return redirect()->route('web.orders.manager');
        //
    }

}
