<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;
use App\Repositories\Metadatas\MetadataRepository;
use App\Repositories\StyleSets\Personal\ItemConfigRepository;
use App\Services\Styles\StyleConfigService;
use App\Validators\StyleSets\Personal\ConfigValidator;
use App\Validators\StyleSets\Personal\ItemConfigValidator;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use Gomee\Engines\JsonData;

/**
 * repository chinh
 *
 * @property ItemConfigRepository $repository
 * @property MetadataRepository $metadataRepository
 * @property StyleConfigService $styleConfigService
 */

class PersonalStyleSetConfigController extends AdminController
{
    protected $module = 'style-sets.personal.config';

    protected $moduleName = 'Cấu hình Style Mẫu';

    protected $flashMode = true;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ItemConfigRepository $repository,
        MetadataRepository $metadataRepository,
        StyleConfigService $styleConfigService
    ) {
        $this->repository = $repository;
        $this->metadataRepository = $metadataRepository;
        $this->styleConfigService = $styleConfigService;
        
        $this->init();
        
        $this->activeMenu('style-sets');
    }

    public function getStyleConfigForm(Request $request)
    {
        // $this->activeMenu($this->module.'.list');
        $form = new JsonData();
        // $inputs = [];
        $empty = app(Arr::class, []);
        $f = $this->getFormData($empty);
        $inputs = $f['form_inputs'];
        $data = $this->styleConfigService->getData();
        $items = $this->repository->mode('mask')->getData();

        $itemInputs = $form->getData('admin/modules/style-sets/personal/item-config/form');
        return $this->viewModule('form', compact('inputs', 'data', 'itemInputs', 'items'));
    }

    public function beforeHandleValidate($request)
    {
        $this->repository->setValidatorClass(ConfigValidator::class);
    }
    public function done($request, $data)
    {
        $this->styleConfigService->saveData($data->all());
    }
    public function saveItem(Request $request)
    {
        extract($this->apiDefaultData);
        
        // validate
        $validator = $this->repository->validator($request, ItemConfigValidator::class);
        $id = $request->id;
        if(!$validator->success()){
            $message = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại';
            $errors = $validator->errors();
        }
        elseif(!($res = $this->repository->save($validator->inputs(), $id))){
            $message = 'Đã có lỗi xảy ra. Vui lòng thử lại';
        }
        else{
            $status = true;
            if($res->id == $id) $message = 'Cập nhật '.$res->name.' thành công!';
            else $message = 'Tạo '.$res->name.' thành công!';
            $data = $this->repository->mode('mask')->detail($res->id);
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

}
