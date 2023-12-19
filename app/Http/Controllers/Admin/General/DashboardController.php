<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Admin\AdminController;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Repositories\Exams\ExamTestRepository;
use App\Repositories\Exams\ExamTestResultRepository;
use App\Repositories\Options\SettingRepository;
use App\Repositories\Payments\RequestRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Mailers\Mailer;
use Gomee\Apis\Api;
use Gomee\Core\System;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use Gomee\Laravel\Router;
use Illuminate\Support\Facades\Route;

/**
 * @property RequestRepository $requestRepository
 */
class DashboardController extends AdminController
{
    protected $module = 'dashboard';
    protected $moduleBlade = 'dashboard';
    protected $moduleName = 'Dashboard';

    /**
     * $User
     *
     * @var UserRepository
     */
    public $repository;


    /**
     * $settingRepository
     *
     * @var SettingRepository
     */
    public $settingRepository;


    /**
     * er
     *
     * @var ExamTestResultRepository
     */
    protected $examTestResultRepository;
    /**
    * er
    *
    * @var ExamTestRepository
    */
   protected $examTestRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $repository,
        SettingRepository $settingRepository
    )
    {
        $this->repository = $repository;
        $this->settingRepository = $settingRepository;
        $this->activeMenu();
    }

    public function showConfig()
    {
        dd(config());
    }

    public function testApi(Request $request)
    {
        $api = new Api;
        $api->setResponseType('json');
        $data = $api->get('http://'.config('system.api.ip') . ':' . config('system.api.port'));
        if($e = $api->getException()){
            throw $e;
        }
        return $data;
    }

    public function sendMail(Request $request)
    {
        $to = $request->email??'doanln16@gmail.com';
        $subject = $request->subject??'Test email Subject';
        $message = $request->message??'Test email message';
        $delay = $request->delay??0;
        $mail = Mailer::to($to)->subject($subject)->content($message)->body('mails.message');
        if($delay) $mail->sendAfter($delay);
        else $mail->send();

    }


    public function getIndex(Request $request)
    {

        // return redirect()->route('admin.users');
        return $this->viewModule('index');
    }

    public function viewDefaultDashboard(Request $request)
    {
        $data = [];
        // return redirect()->route('admin.users');
        return $this->viewModule('default', $data);
    }

    public function updateSystemData()
    {
        return $this->settingRepository->createNewData();
    }
}
