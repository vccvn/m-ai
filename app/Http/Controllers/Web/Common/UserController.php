<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;
use Gomee\Laravel\Router;

class UserController extends WebController
{
    protected $module = 'users';

    protected $moduleName = 'User';

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

    public function getList(Request $request)
    {
        return Router::all();
    }

}
