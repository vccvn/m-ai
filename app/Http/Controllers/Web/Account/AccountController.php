<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Web\WebController;
use App\Models\User;
use App\Repositories\Auth\UniversalLoginRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Options\GroupRepository;
use App\Repositories\Showrooms\SettingRepository;
use App\Repositories\Showrooms\ShowroomRepository;
use App\Repositories\Templates\MerchantTemplateRepository;
use App\Repositories\Templates\TemplateRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;
use App\Repositories\Web\ConfigRepository;
use App\Services\Mailers\Mailer;
use App\Services\Mailers\MailNotification;
use App\Services\Users\UserService;
use App\Validators\Account\SignUpValidator;
use App\Validators\Auth\Login;
use App\Validators\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends WebController
{
    protected $module = 'account';

    protected $moduleName = 'User';

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


    protected $showroomRepository;

    protected $settingRepository;

    protected $configRepository;

    protected $groupRepository;
    protected $templateRepository;
    protected $merchantTemplateRepository;
    protected $isUpdate = false;

    protected $passwordGenerated = null;

    /**
     * ULR
     *
     * @var UniversalLoginRepository
     */
    public $universalLoginRepository;


    public $createButtonText = 'Thanh Toán';
    /**
     * user Service
     *
     * @var UserService
     */
    public $userService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $repository,
        EmailTokenRepository $EmailTokenRepository,
        GroupRepository $groupRepository,
        UserService $userService
    ) {
        $this->repository = $repository;
        $this->emailTokens = $EmailTokenRepository;
        $this->groupRepository = $groupRepository;
        $this->userService = $userService;

        $this->init();
        $this->setting = system_setting();
        $this->siteinfo = siteinfo();
    }

    public function getCSRFToken(Request $request)
    {
        extract($this->apiDefaultData);
        if ($token = csrf_token()) {
            $status = true;
            $data = compact('token');
        }
        return $this->json(compact($this->apiSystemVars));
    }

    public function getHomePage(){
        return redirect()->route('account.sign-in');
    }

    public function getSignUpForm(Request $request)
    {
        return $this->viewModule('sign-up');
    }
    /**
     * đăng ký
     *
     * @param Request $request
     * @return void
     */
    public function postSignUp(Request $request)
    {

        $validator = $this->repository->validator($request, SignUpValidator::class);
        if(!$validator->success())
            return redirect()->back()->withInput()->withErrors($validator->getErrorObject())->with('error', 'Dữ liệu đăng ký không hợp lệ. Vui lòng kiểm tra lại');
        $data = $validator->inputs();
        // $data = $this->repository->validate($request, SignUpValidator::class);
        // dd($data);
        $a = ['MALE', 'FEMALE', 'OTHER'];
        $errors = [];
        // $dn = explode(' ', $request->name);
        // $data['first_name'] = array_pop($dn);
        // $data['last_name'] = implode(' ', $dn);

        $data['gender'] = $a[rand(0, 2)];
        $status = 0;

        $data['username'] = $this->repository->getUsernameByEmail($data['email']);
        $data['phone_number'] = $this->repository->getUniquePhone('0978');
        $data['status'] = $status;
        if (!($user = $this->repository->create($data))) {
            $message = "Lỗi không xác định";

        } else {
            return $this->sendVerifyEmailByUser($user, __( 'account.sign-up-' . ($status?'success':'verify')));
        }


        return redirect()->back()->withInput()->with('error', $message);
    }

    public function getSignInForm(Request $request){
        return $this->viewModule('sign-in');
    }

    public function postSignIn(Request $request){
        // die('hutf');
        $message = '';
        $validator = $this->repository->validator($request, Login::class);
        // nhap sai hoặc thiếu thông tin
        if (!$validator->success() || !($user = $this->repository->resetDefaultParams()->findLogin($request->username))) {
            $message = __('auth.failed');
        } // không tìm thấy user
        // sai mật khẩu
        elseif (!Hash::check($request->password, $user->password)) {
            $message = __('auth.password');
        } // chưa kích hoạt
        elseif ($user->status == 0) {
            return redirect()->route('account.alert')->with([
                'type'    => 'warning',
                'message' => __('account.not_verify_message'),
                'link'    => route('web.account.verify.form'),
                'text'    => __('account.get_active_account_request')
            ]);
        } // bị xóa hoạc vô hiệu hóa
        elseif ($user->trashed_status || $user->status < 0) {
            return redirect()->route('account.alert')->with([
                'type'    => 'danger',
                'message' => __('account.deactive')
            ]);
        } // nếu đăng nhập sai. trường hợp này còn lâu mới xảy ra =))))

        elseif (!Auth::attempt(
            ['id' => $user->id, 'email' => $user->email, 'password' => $request->password],
            $request->remember_me
        )) {
            $message = __('auth.login-failed');
        } // nếu có yêu cầu chuyển hướng
        elseif ($request->next) return redirect($request->next);
        else {
            if($user->type != User::ADMIN){
                return redirect()->route('merchant.3d.models');
            }
            return redirect()->route('admin.dashboard');
            return redirect()->route('home');
        }


        return redirect()->route('account.sign-in')->withInput($request->all())->with('error', $message);
    }

    /**
     * gửi email xác thực từ thông tin người dùng
     *
     * @param \App\Models\User|\App\Masks\Users\UserMask $user
     * @param string $message
     * @return \Illuminate\Support\Facades\Redirect
     */
    protected function sendVerifyEmailByUser($user, $message = null)
    {
        if ($emailToken = $this->emailTokens->createToken($user->email, 'verify', 'account')) {
            $data = [
                'url' => route('account.verify-email', [
                    'token' => $emailToken->token
                ]),
                'code' => $emailToken->code,
                'email' => $user->email,
                'user' => $user
            ];
            try {

                Mailer::from('no-reply@' . get_non_www_domain(), 'Mangala Shop')
                ->to($user->email, $user->full_name)
                ->subject("Xác minh email")
                ->body('mails.verify-email')
                ->data($data)
                ->sendAfter(1);
            } catch (\Throwable $th) {
                $user->delete();
                return redirect()->back()->withInput()->with('error', 'Không thể gửi mail xác nhận');
            }
        }
        return redirect()->route('account.alert')->with([
            'type' => 'success',
            // 'message' => ($message ? $message . ' ' : '') . 'Vui lòng truy cập hộp thư đến để xác minh email'
            'message' => ($message ? $message . ' ' : ''),
            'text' => 'Đăng nhập'
        ]);
    }


    /**
     * xác thực email bằng token
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function verifyEmail(Request $request, $token = null)
    {
        if (!$token) $token = $request->token;
        if (!($emailToken = $this->emailTokens->checkRoken($token, 'verify')) || !($user = $this->repository->first(['email' => $emailToken->email, 'status' => User::DEACTIVATED]))) {
            return redirect()->route('account.alert')->with([
                'type' => 'warning',
                'message' => 'Token không hợp lệ'
            ]);
        }
        $emailToken->delete();
        $this->repository->update($user->id, [
            'status' => User::ACTIVATED
        ]);
        return redirect()->route('account.alert')->with([
            'type' => 'success',
            'message' => 'Chúc mừng bạn đã xác minh tài khoản thành công! Vui lòng đăng nhập để tiếp tục!',
            'link' => route('account.sign-in'),
            'text' => 'Đăng nhập'
        ]);
    }

    public function createUserData(User $user){
        return $this->userService->createMerchantData($user);
    }


    public function showAlertMessage(Request $request, $message = '')
    {
        $data = array_merge([
            'page_title' => 'Thông báo'
        ], $request->all());
        return $this->viewModule('alert', $data);
    }
}
