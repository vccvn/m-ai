<?php

namespace App\Services\Accounts;

use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\PaymentTransactions\PaymentTransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Service;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use Gomee\Html\Form;

class AccountService extends Service
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
     * Undocumented variable
     *
     * @var EmailTokenRepository
     */
    protected $emailTokens = null;

    /**
     * Undocumented variable
     *
     * @var PaymentTransactionRepository
     */
    protected $paymentTransactionRepository = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repository, EmailTokenRepository $EmailTokenRepository, PaymentTransactionRepository $paymentTransactionRepository)
    {
        $this->repository = $repository;
        $this->emailTokens = $EmailTokenRepository;
        $this->paymentTransactionRepository = $paymentTransactionRepository;

        $this->init();
        $this->setting = system_setting();
        $this->siteinfo = siteinfo();
    }


    /**
     * hiển thĩ form cập nhât
     *
     * @param Request $request
     * @return Arr|null|false
     */
    public function getAccountModuleData(Request $request)
    {
        $settings = get_account_setting_configs();
        $user = $request->user();
        $webType = get_web_type();
        $false = (
            // nếu t ab không hợp lệ
            !($tab = $this->getTabKey($request->tab)) ||
            // tệ nhất là không lấy dược form từ setting
            !($formConfig = $settings->get($tab))
        );
        if ($false) {
            return false;
        }
        // lấy data phù hợp với tab

        $data = $user->toFormData();

        if ($tab == 'general') {
            $data['name'] = $data['name']?? $data['last_name'] . ' ' . $data['first_name'];
        }

        $form = new Form([
            'inputs' => $formConfig->inputs,
            'data' => $data,
            'errors' => $request->session()->get('errors')
        ]);
        $form->map('setTemplatePath', 'frontend-libs.form');
        $user = $request->user();
        $balance = $this->paymentTransactionRepository->getBalanceByUser($user);
        $account = new Arr(compact('settings', 'formConfig', 'form', 'data', 'tab', 'balance'));

        // $this->breadcrumb->add("Tài khoản", route('web.account'))
        //     ->add($formConfig->title, route('web.account.settings', ['tab' => $tab]));

        return $account;
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
