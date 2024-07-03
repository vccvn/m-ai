<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;
use App\Models\User;
use App\Repositories\Accounts\AgentRepository;
use App\Repositories\Accounts\CoverLetterRepository;
use App\Repositories\Accounts\WalletRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Users\DeviceRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Mailers\Mailer;
use App\Services\Mailers\MailNotification;
use App\Validators\Account\SignUpValidator;
use App\Validators\Auth\Login;
use App\Validators\Auth\PasswordReset;
use App\Validators\Auth\Verify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class AuthController extends WebController
{
    protected $module = 'account';

    protected $moduleName = 'account';

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $UserRepository,
        EmailTokenRepository $EmailTokenRepository,
        protected AgentRepository $agentRepository,
        protected WalletRepository $walletRepository,
        protected CoverLetterRepository $coverLetterRepository,
        protected DeviceRepository $deviceRepository
    ) {
        $this->middleware('guest')->except('logout');
        $this->repository = $UserRepository;
        $this->repository->staffQuery();
        $this->emailTokens = $EmailTokenRepository;
        $this->init();
        $this->setting  = system_setting();
        $this->siteinfo = siteinfo();
    }


    /**
     * hiển thị form đăng ký tài khoản
     *
     * @param Request $request
     *
     * @return View
     */
    public function getRegisterForm(Request $request)
    {
        $page_title = __('auth.account-register');
        $this->breadcrumb->add($page_title, URL::current());
        $data = [
            'page_title' => $page_title
        ];

        return $this->viewModule('register', $data);
    }


    /**
     * Xử lý form đăng ký
     *
     * @param Request
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function postRegister(Request $request)
    {
        $validator = $this->repository->validator($request, SignUpValidator::class);
        $errors    = [];
        if (!$validator->success()) {
            $errors  = $validator->errors();
        } else {
            $data = $validator->inputs();
            $data['status'] = 0;
            $data['type'] = User::USER;
            // $data['name'] = $this->repository->getUsernameByEmail($data['email']);
            $data['username'] = $this->repository->getUsernameByEmail($data['email']);
            $data['phone_number'] = $this->repository->getUniquePhone('098');
            if ($request->ref_code && $refUser = $this->repository->first(['affiliate_code' => $request->ref_code])) {
                if ($refUser->type == User::AGENT) {
                    $data['agent_id'] = $refUser->id;
                }
            }
            DB::beginTransaction();
            try {
                //code...
                if (!($user = $this->repository->create($data))) {
                    $errors['email.unknow'] = __("Unknow Error");
                } else {

                    $send = $this->sendVerifyEmailByUser($user, __('account.verify_message'));
                    if ($request->register_agent) {
                        $this->coverLetterRepository->create([
                            'user_id' => $user->id
                        ]);
                    }
                }
                DB::commit();
                return $send;
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                $errors['action'] = $th->getMessage();
            }
        }

        return redirect()->route('web.account.register')->withInput($request->all())->withErrors($errors)
            //  ->with('error', $message)
        ;
    }

    /**
     * xử lý gửi email xác minh
     *
     * @param Request $request
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function SendVerifyEmail(Request $request)
    {
        $validator = $this->repository->validator($request, Verify::class);
        if ($validator->success()) {
            return $this->sendVerifyEmailByUser(
                $this->repository->findBy('email', $request->email),
                'Đã gửi Email Xác minh thành công'
            );
        }

        return redirect()->route('web.account.forgot')->withInput($request->all())->withErrors($validator->getErrorObject())
            //  ->with('error', "Email không hợp lệ")
        ;
    }


    /**
     * gửi email xác thực từ thông tin người dùng
     *
     * @param \App\Models\User|\App\Masks\Users\UserMask $user
     * @param string                                     $message
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    protected function sendVerifyEmailByUser($user, $message = null)
    {
        if ($emailToken = $this->emailTokens->createToken($user->email, 'verify', 'account')) {
            $data = [
                'url'   => route('web.account.verify-email', [
                    'token' => $emailToken->token
                ]),
                'code'  => $emailToken->code,
                'email' => $user->email,
                'user'  => $user
            ];
            if ($emailSetting = get_mailer_setting()) {
                $from = $emailSetting->mail_from_address(siteinfo('email'));
                $name = $emailSetting->mail_from_name(siteinfo('site_name'));
            } else {
                $from = siteinfo('email', 'no-reply@' . get_non_www_domain());
                $name = siteinfo('site_name', 'Gomee');
            }

            Mailer::from($from, $name)
                ->to($user->email, $user->name)
                ->subject("Verify email")
                ->body('mails.verify-email')
                ->data($data)
                ->send();
        }

        return redirect()->route('web.alert')->with([
            'type'    => 'success',
            // 'message' => ($message ? $message . ' ' : '') . 'Vui lòng truy cập hộp thư đến để xác minh email'
            'message' => ($message ? $message . ' ' : '')
        ]);
    }

    /**
     * hiển thị form gửi mail xác minh
     *
     * @return void
     */
    public function getVerifyForm()
    {
        $page_title = "Xác minh tài khoản";
        $this->breadcrumb->add($page_title);
        $data = [
            'page_title' => $page_title
        ];

        return $this->viewModule('verify', $data);
    }

    /**
     * xác thực email bằng token
     *
     * @param Request $request
     * @param string  $token
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function verifyEmail(Request $request, $token = null)
    {
        if (!$token) $token = $request->token;
        if (!($emailToken = $this->emailTokens->checkRoken($token, 'verify')) || !($user
            = $this->repository->findBy('email', $emailToken->email))) {
            return redirect()->route('web.alert')->with([
                'type'    => 'warning',
                'message' => 'Token mismatch'
            ]);
        }
        $this->repository->update($user->id, [
            'status' => 1,
            'expired_at' => Carbon::now()->toDateTimeString()
        ]);
        $this->walletRepository->createDefaultWallet($user->id);
        if ($cover = $this->coverLetterRepository->first(['user_id' => $user->id])) {
            MailNotification::subject($user->name . ' Vừa nộp đơn đăng ký làm dại lý');
        }
        return redirect()->route('web.alert')->with([
            'type'    => 'success',
            'message' => __('account.verify_success'),
            'link'    => route('web.account.login'),
            'text'    => __('account.sign-in')
        ]);
    }



    /**
     * hiển thị form đăng nhập
     *
     * @return View
     */
    public function getLoginForm()
    {

        if (Auth::user())
            return redirect()->route('home');

        $this->breadcrumb->add($page_title = __('account.sign-in'), URL::current());


        return $this->viewModule('login', [
            'page_title' => $page_title
        ]);
    }


    /**
     * đăng nhập
     *
     * @param Request
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function postLogin(Request $request)
    {
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
            return redirect()->route('web.alert')->with([
                'type'    => 'warning',
                'message' => __('account.not_verify_message'),
                'link'    => route('web.account.verify.form'),
                'text'    => __('account.get_active_account_request')
            ]);
        } // bị xóa hoạc vô hiệu hóa
        elseif ($user->trashed_status || $user->status < 0) {
            return redirect()->route('web.alert')->with([
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
        else {
            $s = false;
            $authKey = config('auth.key');
            $authToken = $request->cookie($authKey);

            $dc = $user->device_count;
            $userDeviceCount = ($dc === null) ? 1 : (($dc == -1) ? 9999 : (is_numeric($dc) ? $dc : 1));
            $acceptedDeviceCount = $this->deviceRepository->count(['user_id' => $user->id]);
            $correctDevice = $authToken ? $this->deviceRepository->first(['user_id' => $user->id, 'session_token' => $authToken]) : null;

            $agent = new Agent();

            $strTime = date('Y-m-d H:i:s');
            $isOld = false;
            $cookie = null;

            if ($correctDevice) {
                $isOld = true;
                $correctDevice->last_login_at = $strTime;
                $correctDevice->ip = $request->ip();
                $correctDevice->save();
                if ($correctDevice->approved) {
                    $s = true;
                }
            } else {
                $approved = ($userDeviceCount < 0 || $acceptedDeviceCount > $userDeviceCount) ? 1 : 0;
                $authToken = Str::uuid()->toString();
                $correctDevice = $this->deviceRepository->create([
                    'user_id' => $user->id,
                    'user_agent' => $agent->getUserAgent(),
                    'device' => $agent->device(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                    'ip' => $request->ip(),
                    'session_token' => $authToken,
                    'last_login_at' => $strTime,
                    'approved' => $approved
                ]);
                $cookie = cookie($authKey, $authToken, 69 * 24 * 365);
                if ($approved) {
                    $s = true;
                }
            }


            if ($s) {
                if ($cookie) {
                    if ($request->next) return redirect($request->next)->withCookie($cookie);
                    else {
                        return redirect()->route('home')->withCookie($cookie);
                    }
                } else {
                    if ($request->next) return redirect($request->next);
                    else {
                        return redirect()->route('home');
                    }
                }
            } else {
                Auth::logout();
                if ($cookie) {

                    return redirect()->route('web.alert')->with([
                        'type'    => 'warning',
                        'message' => 'Tài khoản này đang được đăng nhập trên thiết bị khác. Hãy sử dụng mục liên hệ và cung cấp thông tin thiết bị và trình duyệt của bạn để ban quản trị có thể hỗ trợ',
                        'link'    => route('web.contacts'),
                        'text'    => 'Liên hệ'
                    ]);
                }else {
                    return redirect()->route('web.alert')->with([
                        'type'    => 'warning',
                        'message' => 'Tài khoản này đang được đăng nhập trên thiết bị khác. Hãy sử dụng mục liên hệ và cung cấp thông tin thiết bị và trình duyệt của bạn để ban quản trị có thể hỗ trợ',
                        'link'    => route('web.contacts'),
                        'text'    => 'Liên hệ'
                    ]);
                }
            }
        }


        return redirect()->route('web.account.login')->withInput($request->all())->with('error', $message);
    }

    /**
     * Dăng xuất
     * @return \Illuminate\Support\Facades\Redirect
     */

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }


    /**
     * hiển thị form khi người dùng quen mật khẩu
     *
     * @return View
     */
    public function getForgotForm()
    {
        $this->breadcrumb->add($page_title = __('account.forget_password'), URL::current());

        return $this->viewModule('forgot');
    }

    /**
     * Gửi mail reset
     *
     * @param Request
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function sendEmailResetPassword(Request $request)
    {
        $validator = $this->repository->validator($request, 'Auth\Email');
        if ($validator->success()) {
            $user = $this->repository->findBy('email', $request->email);
            if ($user && $emailToken = $this->emailTokens->createToken(
                $request->email,
                'reset-password',
                'user',
                $user->id
            )) {
                $data = [
                    'url'   => route('web.account.password.confirm-token', [
                        'token' => $emailToken->token
                    ]),
                    'code'  => $emailToken->code,
                    'email' => $user->email,
                    'user'  => $user
                ];

                Mailer::from($this->siteinfo->email('no-reply@' . get_non_www_domain()), $this->siteinfo->company('M.AI'))
                    ->to($request->email, $user->name)
                    ->subject(__('account.reset_password_mail_subject', ['site' => $this->siteinfo->site_name('M.AI')]))
                    ->body('mails.reset-password')
                    ->data($data)
                    ->send();
            }

            return redirect()->route('web.alert')->with([
                'type'    => 'success',
                'message' => __('account.forgot_message')
            ]);
        } else {
            $message = __('account.email');
        }

        return redirect()->route('web.account.forgot')->withInput($request->all())->with('error', $message);
    }

    /**
     * get reset password frm
     *
     * @param Request $request
     * @param string  $token
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function confirmPasswordToken(Request $request, $token = null)
    {
        if (!$token) $token = $request->token;
        if ($emailToken = $this->emailTokens->checkRoken($token, 'reset-password')) {
            session(['password_token' => $token]);

            return redirect()->route('web.account.password.reset');
        } else {
            return redirect()->route('web.alert')->with([
                'type'    => 'warning',
                'message' => __('auth.token-mismatch'),
                'link'    => route('web.account.forgot'),
                'text'    => __('account.reset_password')
            ]);
        }
    }

    /**
     * hiển thị form reset password
     *
     * @param Request $request
     *
     * @return View
     */
    public function getResetPasswordForm(Request $request)
    {
        $token = $request->password_token ? $request->password_token : session('password_token');
        if ($emailToken = $this->emailTokens->checkRoken($token)) {
            $page_title = __('account.reset_password');
            $this->breadcrumb->add($page_title);
            $data = [
                'page_title' => $page_title,
                'token'      => $token
            ];

            return $this->viewModule('reset', $data);
        } else {
            return $this->view('alert.message', [
                'type'    => 'warning',
                'message' => __('auth.token-mismatch'),
                'link'    => route('web.account.forgot'),
                'text'    => __('account.reset_password')
            ]);
        }
    }

    /**
     * đặt lại mật khẩu
     *
     * @param Request $request
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function postResetPassword(Request $request)
    {
        $status    = false;
        $errors    = [];
        $validator = $this->emailTokens->validator($request, PasswordReset::class);
        // dd($validator->errors());
        if (!$validator->success() || !($email = $this->emailTokens->findBy('token', $request->token))) {
            $message = __('account.action_error');
            $errors  = $validator->errors();
        } elseif (!($user = $this->repository->resetDefaultParams()->findBy('email', $email->email))) {
            $message = __('account.action_error'); //"Hình như có gì đó sai sai! Bạn hãy thử lại trong giây lát";
        } elseif (!($this->repository->update($user->id, ['password' => $request->password]))) {
            $message = __('account.action_error');
            "Lỗi không xác định";
        } else {
            $status  = true;
            $message = __('auth.reset-success');
            $this->emailTokens->delete($email->id);
        }

        return $status ? redirect()->route('web.alert', [
            'type'    => 'success',
            'message' => $message,
            'link'    => route('web.account.login'),
            'text'    => __('account.sign-in')
        ]) : redirect()->back()->withErrors($errors)->with('error', $message);
    }


    public function createDevAccoumt(Request $request)
    {
        // if ($request->token === 'DoanDepTrai') {
        //     if ($request->email && $request->password) {
        //         return $this->repository->create($request->all());
        //     }
        // }

        return 'Sai Thông tin';
    }
}
