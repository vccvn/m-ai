<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Users\UserRepository;
use App\Repositories\Emails\EmailTokenRepository;
use App\Repositories\Users\AuthLogRepository;
use App\Services\Mailers\Mailer;
use Gomee\Mailer\Email;

use Gomee\Helpers\Arr;

class MerchantAuthController extends Controller
{
    protected $module = 'auth';

    protected $moduleName = 'Auth';


    /**
     * @var string $viewFolder thu muc chua view
     * khong nen thay doi lam gi
     */
    protected $viewFolder = 'merchant';


    /**
     * auth log
     *
     * @var \App\Repositories\Users\AuthLogRepository
     */
    protected $authLogRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $UserRepository, EmailTokenRepository $EmailTokenRepository, AuthLogRepository $authLogRepository, MerchantRepository $merchantRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->repository = $UserRepository;
        // $this->repository->staffQuery();
        $this->emailTokens = $EmailTokenRepository;
        $this->authLogRepository = $authLogRepository;
        $this->merchantRepository = $merchantRepository;
        $this->init();
    }

    public function getLoginForm(Request $request)
    {
        if ($request->cookie('account_lock')) {
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
        $base_domain = get_cfg_domain();
        $sub_domain = get_sub_domain();
        $merchant_id = 0;
        if($sub_domain && $base_domain){
            if($merchant = $this->merchantRepository->getMerchantBySubDomain($base_domain, $sub_domain)){
                $merchant_id = $merchant->id;
            }
        }
        $validator = $this->repository->validator($request, 'Auth\Login');
        if (!$validator->success()) {
            $message = "Đăng nhập không thành công!!";
            $errors = $validator->errors();
        } elseif ($user = $this->repository->resetDefaultParams()->findLogin($request->username)) {
            $failLogin = $this->authLogRepository->orderBy('id', 'DESC')->first(['user_id' => $user->id, 'status' => 0]);
            $failTimeCond = $failLogin && (time() - strtotime($failLogin->created_at) < 5 * 60);
            $failCoundCond = $failLogin && ($failLogin->log_fail_count >= 5);
            $args = [
                'status' => 1,
                'trashed_status' => 0,
                'id' => $user->id,
                'email' => $user->email,
                'password' => $request->password
            ];
            if($user->id != $merchant_id){
                $this->authLogRepository->create(['user_id' => $user->id, 'status' => 0, 'log_fail_count' => 1, 'message']);
                $message = 'Sai tài khoản hoặc mật khẩu!';
            }
            elseif ($failTimeCond && $failCoundCond) {
                $message = "Bạn đã nhập sai mật khẩu quá 5 lần";
                $cookie = cookie('account_lock', 1, 5);
            } elseif (Auth::attempt($args, $request->remember)) {
                $data = ['redirect' => $request->next??(route('merchant.dashboard')) ];
                $status = true;
                if ($failTimeCond) {
                    $failLogin->status = 1;
                    $failLogin->save();
                } else {
                    $this->authLogRepository->create(['user_id' => $user->id, 'status' => 1]);
                }
            } else {
                $message = "Sai tài khoản hoặc mật khẩu";
                if ($failTimeCond) {
                    $failLogin->log_fail_count++;
                    $failLogin->save();
                    if ($failLogin->log_fail_count >= 5) {
                        $message = "Bạn đã nhập sai mật khẩu quá 5 lần";
                        $cookie = cookie('account_lock', 1, 5);
                    }
                } else {
                    $this->authLogRepository->create(['user_id' => $user->id, 'status' => 0, 'log_fail_count' => 1]);
                }
            }
        } else {
            $message = "Đăng nhập không thành công!";
        }
        $res = $this->json(compact(...$this->apiSystemVars));
        if ($cookie) $res->withCookie($cookie);
        return $res;
    }

    /**
     * Dăng xuất
     * @return redirect
     */

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
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
        $validator = $this->repository->validator($request, 'Auth\Email');
        if ($validator->success()) {
            $this->repository->resetDefaultParams();
            $owner_id = $this->repository->getOwnerID();
            $siteinfo = siteinfo();
            if(!$siteinfo) $siteinfo = new Arr();
            $user = $this->repository->findBy('email', $request->email);
            if (($user && (!$owner_id || in_array($owner_id, [$user->id, $user->owner_id]))) && ($emailToken = $this->emailTokens->createToken($request->email, 'reset-password'))) {
                $data = [
                    'url' => route('password.reset', [
                        'token' => $emailToken->token
                    ]),
                    'code' => $emailToken->code,
                    'email' => $user->email,
                    'user' => $user
                ];

                $mailer = get_mailer_setting();
                if(!$mailer) $mailer = new Arr();
                Mailer::from($mailer->mail_from_address(siteinfo('email', 'no-reply@' . get_non_www_domain())), $mailer->mail_from_name(siteinfo('site_name', 'Mangala')))
                    ->to($request->email, $user->name)
                    ->subject("Đặt lại mật khẩu trên hệ thống " . $siteinfo->site_name('Mangala'))
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
        if ($emailToken = $this->emailTokens->checkRoken($token)) {
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
        $validator = $this->emailTokens->validator($request, 'Auth\PasswordReset');
        if ($validator->success() && $email = $this->emailTokens->findBy('token', $request->token)) {
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
