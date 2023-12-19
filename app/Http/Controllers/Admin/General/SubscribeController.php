<?php

namespace App\Http\Controllers\Admin\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

use App\Repositories\Subscribes\SubscribeRepository;

class SubscribeController extends AdminController
{
    protected $module = 'subscribes';

    protected $moduleName = 'Đăng ký theo dõi';



    protected $flashMode = true;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SubscribeRepository $SubscribeRepository)
    {
        $this->repository = $SubscribeRepository;
        $this->init();
    }

}
