<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Users\UserRepository;

use App\Repositories\Profiles\ProfileRepository;
use App\Validators\Account\AdminSecurityValidator;
use App\Validators\Account\InfoValidator;
use App\Validators\Account\SecurityValidator;

class AccountController extends AdminController
{
    protected $module = 'account';

    protected $moduleName = 'Account';

    // protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;


    /**
     * profile
     *
     * @var ProfileRepository
     */
    public $profiles = null;


    public $tab = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $UserRepository)
    {
        $this->repository = $UserRepository;
        // $this->repository->staffQuery();
        $this->init();

    }

    /**
     * hiển thị form account
     *
     * @param Request $request
     * @param string $tab
     * @return void
     */
    public function getAccountForm(Request $request, $tab = 'info')
    {

        // $this->repository->addSelectable([
        //     // 'secret_id' => 'users.secret_id',
        //     // 'client_key' => 'users.client_key'
        // ]);

        if($detail = $this->repository->getFormData(['id'=>$request->user()->id])){
            $this->repository->setActiveID($detail->id);
            return $this->getCrudForm($request, ['type'=>'update'], $detail, [], ['tab'=>$tab]);
        }
        return $this->showError($request, 404, "Mục này không tồn tại hoặc đã bị xóa");
    }

    public function getInfoForm(Request $request)
    {
        return $this->getAccountForm($request, 'info');
    }
    public function getSecurityForm(Request $request)
    {
        return $this->getAccountForm($request, 'security');
    }



    public function saveInfo(Request $request)
    {
        // $request->id = $request->user()->id;
        $this->tab = 'info';
        $this->repository->setValidatorClass(InfoValidator::class);
        $this->redirectRoute = 'admin.account.info';
        return $this->handle($request);
    }

    public function saveSecurity(Request $request)
    {
        // $request->id = $request->user()->id;
        $this->tab = 'security';
        $this->repository->setValidatorClass(AdminSecurityValidator::class);
        $this->redirectRoute = 'admin.account.security';
        return $this->handle($request);
    }

    public function done(Request $request, Arr $data)
    {

        if($this->tab == 'info'){
            $data->full_name = $data->last_name . ' ' . $data->first_name;
            // $this->uploadImageAttachFile($request, $data, 'avatar', get_content_path('users/'.$request->user()->secret_id.'/avatar'), 400, 400);
            $this->uploadImageAttachFile($request, $data, 'avatar', get_content_path('users/avatar'));
        }
        if($this->repository->update($request->user()->id, $data->all())){
            return redirect()->route($this->redirectRoute)->with('success', __('admin.account.update.'.$this->tab));
        }
        return redirect()->route($this->redirectRoute);

    }

    public function onError($request, $error, $validator)
    {
        return redirect()->route($this->redirectRoute)->withErrors($validator->getErrorObject())->withInput();
    }

}
