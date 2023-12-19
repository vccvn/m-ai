<?php

namespace App\Http\Controllers\Web\Home;

use App\Http\Controllers\Apis\Payments\PaymentController;
use App\Http\Controllers\Web\WebController;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;
use Gomee\Apis\Api;

class HomeController extends WebController
{
    protected $module = 'home';

    protected $moduleName = 'Home Page';

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


    public function getIndexPage(Request $request)
    {
        // return redirect()->route('admin.dashboard');
        return $this->cacheViewModule($request, 'index');
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


    public function checkHealth() : string {
        try {
            $this->repository->count();
            return 'OK';
        } catch (\Throwable $th) {
            //throw $th;
            $this->filemanager->append("\n[".date('Y-m-d H:i:s')."] log check health", storage_path('crazy/check-health.txt'));
            return 'FAIL';

        }
    }



    public function phpinfo()
    {
        phpinfo();
    }

    public function authenticate(Request $request) {
        $api = new Api();
        $api->setResponseType('json');
        $data = $api->get(route('api.payment.complete'), $request->all());
        return $this->json(['data' => $data]);
    }

    public function cancel(Request $request) {
        $api = new Api();
        $api->setResponseType('json');
        $data = $api->get(route('api.payment.cancel'), $request->all());
        return $this->json(['data' => $data]);
    }
}
