<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Admin\AdminController;
use App\Repositories\Options\WebDataRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;
use App\Validators\Web\ConfigValidator;
use Gomee\Apis\Api;

class WebConfigController extends AdminController
{
    protected $module = 'webconfig';

    protected $moduleName = 'Cấu hình web';

    // protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var WebDataRepository
     */
    public $repository;
    
    /**
     * api
     *
     * @var Api
     */
    protected $api;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WebDataRepository $repository, Api $api)
    {
        $this->repository = $repository;
        $this->repository->setValidatorClass(ConfigValidator::class);
        $this->init();
        $this->submitUrl = route($this->routeNamePrefix.'settings.webconfig.save');
        $this->api = $api;
    }

    public function getWebConfigForm(Request $request)
    {
        $data = $this->repository->getGroupData('web.config', false, true);
        return $this->getForm($request, ['type'=>'free'], $data);
    }



    public function done(Request $request, Arr $data)
    {
        if($setting = $this->repository->getGroupData('web.config', false, true)){
            $s = $this->repository->updateData('web.config', $data->all());
            if($data->ssl && !$setting->ssl){
            }
            
        }
    }

}
