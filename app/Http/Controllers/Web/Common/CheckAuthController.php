<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;

class CheckAuthController extends WebController
{
    protected $module = 'auth';

    protected $moduleName = 'check auth';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function check(Request $request)
    {
        extract($this->apiDefaultData);
        $response = null;
        if($user = get_login_user()){
            $status = true;
            $links = [
                'account' =>  [
                    'link' => route('web.account'),
                    'text' => 'Tài khoản'
                ]
            ];
            if($user->type=='admin'){
                $links['admin'] = [
                    'link' => route('admin.dashboard'),
                    'text' => 'Trang quản trị'
                ];
            }
            // if(get_web_type() == 'ecommerce'){
            //     $links['orders'] = [
            //         'link' => route('web.orders.manager'),
            //         'text' => 'Quản lý đơn hàng'
            //     ];
            // }
            $links['logout'] = [
                'link' => route('web.account.logout'),
                'text' => 'Đăng xuất'
            ];
            $name = $user->name;
            if(strlen($name) > 12){
                $ns = explode(' ', trim($name));
                $name = array_pop($ns);
            }
            if(strlen($name) > 12){
                $name = "Tài khoản";
            }

            $data = [
                'name' => $name,
                'avatar' => $user->getAvatar(),
                'links' => $links
            ];
            $response = $this->json(compact($this->apiSystemVars));
            $this->fire('success', $request, $response);
        }else{
            $links = [];
            if(get_web_type() == 'ecommerce'){
                $links['orders'] = [
                    'link' => route('web.orders.manager'),
                    'text' => 'Quản lý đơn hàng'
                ];
            }
            $links['login'] = [
                'link' => route('web.account.login'),
                'text' => 'Đăng nhập'
            ];
            $links['register'] = [
                'link' => route('web.account.register'),
                'text' => 'Đăng ký'
            ];
            $data = [
                'links' => $links
            ];
            $response = $this->json(compact($this->apiSystemVars));
            $this->fire('fail', $request, $response);
        }

        return $response;
    }
}
