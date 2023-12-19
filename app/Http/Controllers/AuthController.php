<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Users\UserRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Users\AuthLogRepository;
use App\Services\Mailers\Mailer;
use App\Validators\AdminAuth\Email as AdminAuthEmail;
use App\Validators\AdminAuth\Login;
use App\Validators\AdminAuth\PasswordReset;
use Gomee\Mailer\Email;

use Gomee\Helpers\Arr;

class AuthController extends Controller
{
    protected $module = 'auth';

    protected $moduleName = 'Auth';


    /**
     * @var string $viewFolder thu muc chua view
     * khong nen thay doi lam gi
     */
    protected $viewFolder = 'admin';


    /**
     * auth log
     *
     * @var \App\Repositories\Users\AuthLogRepository
     */
    protected $authLogRepository;

    /**
     * auth log
     *
     * @var EmailTokenRepository
     */
    protected $emailTokenRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $UserRepository, EmailTokenRepository $EmailTokenRepository, AuthLogRepository $authLogRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->repository = $UserRepository;
        // $this->repository->staffQuery();
        $this->emailTokenRepository = $EmailTokenRepository;
        $this->authLogRepository = $authLogRepository;
        $this->init();
    }

    public function getLoginForm(Request $request)
    {
        if ($request->cookie('VX-TOKEN')) {
            return $this->showError($request, 403, "Tài khoản này bị tạm khoá do nhập sai mật khẩu quá 5 lần. Vui lòng quay lại sau 5 phút");
        }
        return $this->viewModule('login', ['auth_page' => 'signin']);
    }


    /**
     * đăng nhập
     * @param Request
     * @return json
     */
    public function postLogin(Request $request)
    {
        extract($this->apiDefaultData);

        $cookie = null;
        $validator = $this->repository->validator($request, Login::class);
        $loginEvent = 'login:fail';
        $loginUser = null;
        if (!$validator->success()) {
            $message = "Đăng nhập không thành công!!";
            $errors = $validator->errors();
        } elseif ($user = $this->repository->resetDefaultParams()->findLogin($request->username)) {
            $failLogin = $this->authLogRepository->orderBy('created_at', 'DESC')->first(['user_id' => $user->id, 'status' => 0]);
            $failTimeCond = $failLogin && (time() - strtotime($failLogin->created_at) < 5 * 60);
            $failCoundCond = $failLogin && ($failLogin->log_fail_count >= 5);
            $args = [
                'status' => 1,
                'trashed_status' => 0,
                'id' => $user->id,
                'email' => $user->email,
                'password' => $request->password
            ];
            if ($failTimeCond && $failCoundCond) {
                $message = "Bạn đã nhập sai mật khẩu quá 5 lần";
                $cookie = cookie('VX-TOKEN', 1, 5);
            } elseif (Auth::attempt($args, $request->remember)) {
                $data = ['redirect' => $user->type == User::MERCHANT || $user->owner_id? (get_subdomain() == 'merchant' ?route('merchant.dashboard'): 'https://merchant.' . get_cfg_domain()): route('dashboard')];
                $status = true;
                if ($failTimeCond) {
                    $failLogin->status = 1;
                    $failLogin->save();
                } else {
                    $this->authLogRepository->create(['user_id' => $user->id, 'status' => 1]);
                }
                $loginEvent = 'login:success';
                $loginUser = $user;
            } else {
                $message = "Sai tài khoản hoặc mật khẩu";
                if ($failTimeCond) {
                    $failLogin->log_fail_count++;
                    $failLogin->save();
                    if ($failLogin->log_fail_count >= 5) {
                        $message = "Bạn đã nhập sai mật khẩu quá 5 lần";
                        $cookie = cookie('VX-TOKEN', 1, 5);
                    }
                } else {
                    $this->authLogRepository->create(['user_id' => $user->id, 'status' => 0, 'log_fail_count' => 1]);
                }
            }
        } else {
            $message = "Đăng nhập không thành công!";

        }
        $res = $this->json(compact(...$this->apiSystemVars));

        $this->fire($loginEvent, $request, $res, $loginUser);
        if ($cookie) $res->withCookie($cookie);
        return $res;
    }

    /**
     * Dăng xuất
     * @return redirect
     */

    public function logout(Request $request)
    {
        Auth::logout();
        if(strtoupper(env('APP_2FA')) == 'ON'){
            $adminMiddleWare[] = '2fa';
        }
        $request->session()->forget(config('google2fa.session_var'));
        $res = redirect('/login');
        $this->fire('logout', $request, $res);
        return $res;
    }

    /**
     * đăng nhập
     * @param Request
     * @return json
     */
    public function sendEmailResetPassword(Request $request)
    {
        extract($this->apiDefaultData);
        $code = 201;
        $validator = $this->repository->validator($request, AdminAuthEmail::class);
        if ($validator->success()) {
            $this->repository->resetDefaultParams();
            $owner_id = $this->repository->getOwnerID();
            $siteinfo = siteinfo();
            $user = $this->repository->findBy('email', $request->email);
            if (($user && (!$owner_id || in_array($owner_id, [$user->id, $user->owner_id]))) && ($emailToken = $this->emailTokenRepository->createToken($request->email, 'reset-password'))) {
                $data = [
                    'url' => route('password.reset', [
                        'token' => $emailToken->token
                    ]),
                    'code' => $emailToken->code,
                    'email' => $user->email,
                    'user' => $user
                ];

                $mailer = get_mailer_setting();
                Mailer::from($mailer->mail_from_address(siteinfo('email', 'no-reply@' . get_non_www_domain())), $mailer->mail_from_name(siteinfo('site_name', 'Tổng đài tuyển sinh CAND')))
                    ->to($request->email, $user->name)
                    ->subject("Đặt lại mật khẩu trên hệ thống " . $siteinfo->site_name('Hướng dẫn tuyển sinh CAND'))
                    ->body('mails.reset-password')
                    ->data($data)
                    ->sendAfter(1);
                $code = 200;
            }

            $status = true;
            $message = "Gửi thành công! Hãy kiểm tra hộp thư đến để đặt lại mật khẩu";
        } else {
            $message = "Email không hợp lệ";
        }

        return $this->json(compact(...$this->apiSystemVars), $code);
    }

    /**
     * get reset password frm
     * @param Request $request
     * @param string $token
     */
    public function getResetPasswordForm(Request $request, $token = null)
    {
        if (!$token) $token = $request->token;
        if ($emailToken = $this->emailTokenRepository->checkRoken($token)) {
            $error = null;
            $tk = $token;
        } else {
            $error = "Token không hợp lệ";
            $tk = null;
        }
        return $this->viewModule('login', ['page' => 'reset', 'error' => $error, 'token' => $token]);
    }

    /**
     * đặt lại mật khẩu
     * @param $request
     */
    public function postResetPassword(Request $request)
    {
        extract($this->apiDefaultData);
        $validator = $this->emailTokenRepository->validator($request, PasswordReset::class);
        if ($validator->success() && $email = $this->emailTokenRepository->findBy('token', $request->token)) {
            if ($user = $this->repository->resetDefaultParams()->findBy('email', $email->email)) {
                if ($this->repository->update($user->id, ['password' => $request->password])) {
                    $status = true;
                    $message = "Đặt lại mật khẩu thành công!";
                } else {
                    $message = "Lỗi không xác định";
                }
            } else {
                $message = "Hình như có gì đó sai sai! Bạn hãy thử lại trong giây lát";
            }
        } else {
            $message = "Đã có lỗi xảy ta. Vui lòng thử lại!";
            $errors = $validator->errors();
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
}
