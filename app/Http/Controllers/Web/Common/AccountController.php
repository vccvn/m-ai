<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Emails\EmailTokenRepository;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use Gomee\Html\Form;
use Gomee\Mailer\Email;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends WebController
{
    protected $module = 'account';

    protected $moduleName = 'Tài khoản';

    protected $flashMode = true;

    protected $setting;
    protected $siteinfo;

    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;

    /**
     * customer
     *
     * @var \App\Repositories\Customers\CustomerRepository $customerRepository
     */
    protected $customerRepository = null;

    /**
     * Undocumented variable
     *
     * @var EmailTokenRepository
     */
    protected $emailTokens = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $repository,
        EmailTokenRepository $EmailTokenRepository,
        CustomerRepository $customerRepository
    )
    {
        $this->repository = $repository;
        $this->emailTokens = $EmailTokenRepository;
        $this->customerRepository = $customerRepository;

        $this->init();
        $this->setting = system_setting();
        $this->siteinfo = siteinfo();
    }



    /**
     * hiển thĩ cập nhât
     *
     * @param Request $request
     * @return View
     */
    public function info(Request $request)
    {
        $settings = get_account_setting_configs();
        $user = $request->user();


        $account = new Arr(compact('settings', 'user'));

        $this->breadcrumb->add("Tài khoản", route('web.account'))
            ->add("My Service", route('web.account.info'));

        return $this->viewModule('info', compact('account', 'user'));
    }

    /**
     * hiển thĩ form cập nhât
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $settings = get_account_setting_configs();
        $user = $request->user();
        $webType = get_web_type();
        $false = (
            // nếu t ab không hợp lệ
            !($tab = $this->getTabKey($request->tab)) ||
            // nếu web không phải là thương mại điện tử thì xóa tab customer và so sánh tap có bàng ecommerce hay không
            // $settings->remove('customer') không ảnh hưởng dến diều kiện biểu thức vì nó luôn trả về giá trị
            ($webType != 'ecommerce' && $settings->remove('customer') && $tab == 'customer') ||
            // tệ nhất là không lấy dược form từ setting
            !($formConfig = $settings->get($tab))
        );
        if ($false) {
            return $this->view('errors.404');
        }
        // lấy data phù hợp với tab

        $data = ($tab == 'customer') ? (
            ($customer = $this->customerRepository->findBy('user_id', $user->id)) ? $customer->toFormData() : []
        ) : $user->toFormData();

        if ($tab == 'general') {
            // $data['name'] = $data['last_name'] . ' ' . $data['first_name'];
        }

        $form = new Form([
            'inputs' => $formConfig->inputs,
            'data' => $data,
            'errors' => $request->session()->get('errors')
        ]);
        $form->map('setTemplatePath', 'web-libs.form');


        $account = new Arr(compact('settings', 'formConfig', 'form', 'data', 'tab'));

        $this->breadcrumb->add("Tài khoản", route('web.account'))
            ->add($formConfig->title, route('web.account.settings', ['tab' => $tab]));

        return $this->viewModule('index', compact('account'));
    }

    /**
     * cập nhật thông tin tài khoản
     *
     * @param Request $request
     * @param string $tab
     * @return void
     */
    public function updateAccount(Request $request, $tab = null)
    {
        $redirect = redirect()->back();
        if (!($tab = $this->getTabKey($tab ? $tab : $request->tab))) {
            $redirect->with('error', 'Lỗi không xác định');
        } elseif (!($validator = ($tab == 'customer' ? $this->customerRepository : $this->repository)->validator($request, $class = "\\App\\Validators\\Account\\" . ucfirst($tab) . "Validator")) || !$validator->success()) {
            $redirect->withInput($request->all())->withErrors($validator->errors());
        } elseif (!($data = $validator->inputs()) || !($user = $request->user())) {
            $redirect->with('error', 'Lỗi không xác định');
        } elseif ($tab == 'customer') {
            $customer_id = 0;
            if ($customer = $this->customerRepository->getCustomerByUser($user)) {
                $customer_id = $customer->id;
            }
            $data['user_id'] = $user->id;
            $this->customerRepository->save($data, $customer_id);
        } else {
            if ($tab == 'general') {
                $ns = explode(' ', $data['name']);
                $data['first_name'] = array_pop($ns);
                $data['last_name'] = implode(' ', $ns);
                if($request->also_update_customer && $customer = $this->customerRepository->getCustomerByUser($user)){
                    $this->customerRepository->update($customer->id, ['name' => $data['name']]);
                }
            }
            if (!$this->repository->update($user->id, $data)) $redirect->with('error', 'Lỗi không xác định');
            elseif ($tab == 'password' && !$this->authenticated($user, $request->password))
                $redirect->with('error', 'Lỗi không xác định');
            else
                $redirect->with('message', 'Cập nhật thông tin thành công!');
        }




        return $redirect;
    }
    /**
     * Handle response after user authenticated
     *
     * @param User $user
     * @param string $password
     *
     * @return bool
     */
    protected function authenticated($user, $password)
    {
        Auth::logout();
        $args = [
            'id' => $user->id,
            'email' => $user->email,
            'password' => $password
        ];
        if (Auth::attempt($args, null)) {
            Auth::logoutOtherDevices($password);
            return true;
        }


        return false;
    }




    public function getTabKey($key)
    {
        $tabs = get_account_setting_tabs();
        $key = strtolower($key);

        if (!$key) {
            $key = $tabs->first();
        } elseif ($k = $tabs->get($key)) {
            $key = $k;
        } elseif (!$tabs->in($key)) {
            $key = null;
        }
        return $key;
    }
}
