<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Notices\NoticeRepository;

class AlertController extends WebController
{
    protected $module = 'alert';

    protected $moduleName = 'Alert';

    protected $flashMode = true;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NoticeRepository $NoticeRepository)
    {
        $this->repository = $NoticeRepository;
        $this->init();
    }

    public function message(Request $request)
    {
        $message = $request->get('message', session('message'));
        // if(!$message) return redirect('/');
        $this->breadcrumb->add('thông báo');
        $data = array_merge([
            'page_title' => 'thông báo'
        ], $request->all());
        return $this->viewModule('message', $data);
    }

}
