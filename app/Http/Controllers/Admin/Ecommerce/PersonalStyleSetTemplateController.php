<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;
use App\Models\PersonalStyleTemplateItem;
use App\Models\PersonalStyleTemplateItemConfig;
use App\Repositories\Categories\CategoryRefRepository;
use App\Repositories\StyleSets\Personal\ItemConfigRepository;
use App\Repositories\StyleSets\Personal\TemplateItemConfigRepository;
use App\Repositories\StyleSets\Personal\TemplateItemRepository;
use App\Repositories\StyleSets\Personal\TemplateRepository;
use App\Repositories\Tags\TagRefRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use Gomee\Engines\JsonData;
use Illuminate\Http\JsonResponse;

/**
 * @property-read ItemConfigRepository $itemConfigRepository
 * @property-read TemplateItemConfigRepository $templateItemConfigRepository
 * @property-read TemplateItemRepository $templateItemRepository
 * @property-read CategoryRefRepository $categoryRefRepository
 * @property-read TagRefRepository $tagRefRepository
 */
class PersonalStyleSetTemplateController extends AdminController
{
    protected $module = 'style-sets.personal.templates';

    protected $moduleName = 'Mẫu style cá nhân';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var TemplateRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        TemplateRepository $repository, 
        TemplateItemRepository $templateItemRepository,
        ItemConfigRepository $styleItemConfigRepository, 
        TemplateItemConfigRepository $templateItemConfigRepository, 
        CategoryRefRepository $categoryRefRepository,
        TagRefRepository $tagRefRepository
    )
    {
        $this->repository = $repository;
        $this->templateItemRepository = $templateItemRepository;
        $this->itemConfigRepository = $styleItemConfigRepository;
        $this->templateItemConfigRepository = $templateItemConfigRepository;
        $this->categoryRefRepository = $categoryRefRepository;
        $this->tagRefRepository = $tagRefRepository;
        $this->init();
        
        $this->activeMenu('style-sets');

        add_js_data('style_template_data', [
            'urls' => [
                'detail' => $this->getModuleRoute('detail', ['id' => 'TEMPLATE_ID']),
                'deleteItem' => $this->getModuleRoute('delete-item'),
            ]
        ]);
    }

    public function beforeAjaxSave(Request $request, Arr $data){
        // die(json_encode($data->all()));
    }

    public function getIndex(Request $request)
    {
        if ($template = $this->repository->first([])) return $this->viewTemplateDetail($request, $template->id);
        return $this->getList($request);
    }

    public function viewTemplateDetail(Request $request, $id = null)
    {
        if (!$id) $id = $request->id;
        if (!($template = $this->repository->detail($id))) return $this->showError($request, 404, "Không tìm thấy template");

        $urlOptions = $this->repository->getUrlOptions($this->routeNamePrefix . $this->module . '.detail');
        // configs
        $styleItemConfigs = $this->itemConfigRepository->mode('mask')->getData();
        $templateItemConfigs = $this->templateItemConfigRepository->with([
            'itemConfig', 
            'categoryRefs', 
            'templateItems' => function($query){
                $query->with(['frontImage', 'backImage', 'tagRefs']);
            }
        ])->mode('mask')->getData(['template_id' => $template->id]);
        // dd($templateItemConfigs);
        // form inputs
        $form = new JsonData();
        $path = 'admin/modules/style-sets/personal/templates';
        $templateInputs = $form->getData($path . '/form');

        $itemConfigInputs = $form->getData($path . '/item-configs/form');
        $itemInputs = $form->getData($path . '/items/form');

        return $this->viewModule('detail', compact(
            'template', 'urlOptions', 'templateInputs', 'styleItemConfigs', 'itemInputs', 'itemConfigInputs', 'templateItemConfigs'
        ));
    }

    /**
     * item config
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveItemConfig(Request $request)
    {
        extract($this->apiDefaultData);

        // validate
        $validator = $this->templateItemConfigRepository->validator($request);

        $id = $request->id;
        $inputData = $validator->inputs();
        $success = $validator->success();
        if($success && !$id && !$request->use_custom_config && ($itemConfig = $this->itemConfigRepository->first(['id' => $request->config_id]))) {
            $inputData['preview_config'] = $itemConfig->getPreviewConfigData();
        }
        if (!$success) {
            $message = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại';
            $errors = $validator->errors();
        } elseif (!($res = $this->templateItemConfigRepository->save($inputData, $id))) {
            $message = 'Đã có lỗi xảy ra. Vui lòng thử lại';
        } else {
            $status = true;
            $this->categoryRefRepository->updateCategoryRef(PersonalStyleTemplateItemConfig::REF_KEY, $res->id, $inputData['categories'] ?? []);
            $data = $this->templateItemConfigRepository->with('itemConfig')->mode('mask')->detail($res->id);
            if ($data->id == $id) $message = 'Cập nhật ' . $data->itemConfig->name . ' thành công!';
            else $message = 'Tạo ' . $data->itemConfig->name . ' thành công!';
        }
        return $this->json(compact(...$this->apiSystemVars));
    }



    public function createItemConfig(Request $request)
    {
        if ($request->id) return $this->ajaxError("Hành động không hợp lệ");
        return $this->saveItemConfig($request);
    }

    public function updateItemConfig(Request $request, $id = null)
    {
        if (!$request->id) return $this->ajaxError("Hành động không hợp lệ");
        return $this->saveItemConfig($request);
    }


    

    
    /**
     * item
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveItem(Request $request)
    {
        extract($this->apiDefaultData);

        // validate
        $validator = $this->templateItemRepository->validator($request);
        $id = $request->id;
        $inputData = $validator->inputs();
        if (!$validator->success()) {
            $message = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại';
            $errors = $validator->errors();
        } elseif (!($res = $this->templateItemRepository->save($inputData, $id))) {
            $message = 'Đã có lỗi xảy ra. Vui lòng thử lại';
        } else {
            $status = true;
            $this->tagRefRepository->updateTagRef(PersonalStyleTemplateItem::REF_KEY, $res->id, $inputData['tags'] ?? []);
            $detail = $this->templateItemRepository->with(['frontImage', 'backImage', 'tagRefs'])->mode('mask')->detail($res->id);
            $data = [
                'detail' => $detail
            ];
            if ($detail->id == $id) $message = 'Cập nhật Style Set Item thành công!';
            else {
                $message = 'Tạo Style Set Item thành công!';
                $form = new JsonData();
                $path = 'admin/modules/style-sets/personal/templates';
                $itemInputs = $form->getData($path . '/items/form');
                $templateItem = $detail;
                $html = $this->viewModule('item-form', compact(
                    'itemInputs', 'templateItem'
                ))->render();
                $data['item_form'] = $html;
            }
            
        }
        return $this->json(compact(...$this->apiSystemVars));
    }



    public function createItem(Request $request)
    {
        if ($request->id) return $this->ajaxError("Hành động không hợp lệ");
        return $this->saveItem($request);
    }

    public function updateItem(Request $request, $id = null)
    {
        if (!$request->id) return $this->ajaxError("Hành động không hợp lệ");
        return $this->saveItem($request);
    }



    
    /**
     * item
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteItem(Request $request)
    {
        extract($this->apiDefaultData);

        if(!$request->id || !($item = $this->templateItemRepository->detail($request->id))){
            $message = 'không tồn tại';
        }
        elseif(!$this->templateItemRepository->delete($request->id)){
            $message = 'Không thể xóa yêu item';
        }
        else{
            $status = true;
            $data = $item;
        }
        return $this->json(compact(...$this->apiSystemVars));
    }







    public function ajaxError($errorMessage)
    {
        extract($this->apiDefaultData);

        $message = $errorMessage;
        return $this->json(compact(...$this->apiSystemVars));
    }
}
