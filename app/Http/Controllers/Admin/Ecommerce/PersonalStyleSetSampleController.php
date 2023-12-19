<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Admin\AdminController;
use App\Models\PersonalStyleSet;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\StyleSets\Personal\ItemConfigRepository;
use App\Repositories\StyleSets\Personal\StyleSampleRepository;
use App\Repositories\StyleSets\Personal\StyleSetItemRepository;
use App\Repositories\StyleSets\Personal\TemplateItemConfigRepository;
use App\Repositories\StyleSets\Personal\TemplateItemRepository;
use App\Repositories\StyleSets\Personal\TemplateRepository;

/**
 * @property-read ItemConfigRepository $itemConfigRepository
 * @property-read TemplateItemConfigRepository $templateItemConfigRepository
 * @property-read TemplateItemRepository $templateItemRepository
 * @property-read TemplateRepository $templateRepository
 * @property-read StyleSetItemRepository $styleSetItemRepository
 */
class PersonalStyleSetSampleController extends AdminController
{
    protected $module = 'style-sets.personal.samples';

    protected $moduleName = 'Style Mẫu';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var StyleSampleRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        StyleSampleRepository $repository,
        TemplateRepository $templateRepository,
        TemplateItemRepository $templateItemRepository,
        ItemConfigRepository $styleItemConfigRepository,
        TemplateItemConfigRepository $templateItemConfigRepository,
        StyleSetItemRepository $styleSetItemRepository
    ) {
        $this->repository = $repository->mode('mask');
        $this->styleSetItemRepository = $styleSetItemRepository->mode('mask');
        $this->templateRepository = $templateRepository->mode('mask');
        $this->templateItemRepository = $templateItemRepository->mode('mask');
        $this->itemConfigRepository = $styleItemConfigRepository->mode('mask');
        $this->templateItemConfigRepository = $templateItemConfigRepository->mode('mask');
        $this->init();
        $this->activeMenu('style-sets');
        $this->activeMenu('style-sets.personal');
        $this->activeMenu('style-sets.personal.samples');
    }


    public function getCreateForm(Request $request)
    {
        if (!count($templateList = $this->templateRepository->getListWithAttributes()))
        return $this->showError($request, 404, 'Hệ thống chưa cấu hình style mẫu');
        $templateDetail = $templateList[0];

        if (!$templateDetail) return $this->showError($request, 404, 'Hệ thống chưa cấu hình style mẫu');
        $style = null;
        $styleItems = [];
        return $this->viewModule('form', [
            'templateDetail' => $templateDetail,
            'templateList' => $templateList,
            'action' => 'create',
            'style' => $style,
            'styleItems' => $styleItems
        ]);
    }

    public function getUpdateForm(Request $request, $id = null)
    {
        $id = $id ? $id : $request->id;
        if (!$id || !($style = $this->repository->detail($id))) abort(404);
        if (!count($templateList = $this->templateRepository->getListWithAttributes()))
            return $this->showError($request, 404, 'Hệ thống chưa có style mẫu');

        $templateDetail = $templateList->getItem(['id' => $style->template_id]);
        if (!$templateDetail)
        return $this->showError($request, 404, 'Hệ thống chưa có style mẫu');

        $templateList = $this->templateRepository->getData([]);
        $styleItems = $this->styleSetItemRepository->getItemTemplateIDs($style->id);
        return $this->viewModule('form', [
            'templateDetail' => $templateDetail,
            'templateList' => $templateList,
            'action' => 'create',
            'style' => $style,
            'styleItems' => $styleItems
        ]);
    }

    public function beforeCreate(Request $request, Arr $data)
    {
        $data->set_data = ['attr_values' => $request->attrs ?? []];
    }

    public function beforeUpdate(Request $request, Arr $data, PersonalStyleSet $style)
    {
        $data->set_data = array_merge($style->getSetData(), ['attr_values' => $request->attrs ?? []]);
    }

    public function beforeSave(Request $request, Arr $data, $style = null)
    {
        if ($file = $this->uploadImage($request, 'thumbnail', null, get_content_path('style-sets'))) {
            $data->thumbnail_image = $file->filename;
        }
    }

    public function afterSave(Request $request, PersonalStyleSet $style)
    {
        if (!$this->styleSetItemRepository->updateItems($style->id, $request->items)) {
            return redirect()->back()->withInput()->with('error', 'Lỗi không xác định');
        }
    }
}
