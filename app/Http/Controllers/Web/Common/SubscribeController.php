<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Crazy\Helpers\Arr;

use App\Repositories\Subscribes\SubscribeRepository;

class SubscribeController extends WebController
{
    protected $module = 'Subscribes';

    protected $moduleName = 'Subscribe';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var SubscribeRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SubscribeRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function afterSave(Request $request, $result)
    {
        $this->redirectRoute = 'web.alert';
        $this->addRedirectData([
            'type' => 'success',
            'message' => 'Đăng ký theo dõi thành công!'
        ]);
    }

    public function onError(Request $request, $errors = null)
    {
        $error = (is_array($errors) && count($errors))? array_shift($errors) : "Lỗi không xác định";
        return redirect()->back()->with('popup_error', $error);
    }

}
