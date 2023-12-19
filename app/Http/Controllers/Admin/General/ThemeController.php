<?php

namespace App\Http\Controllers\Admin\General;

use App\Repositories\Files\FileRepository;
use App\Repositories\Options\OptionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use Gomee\Helpers\Arr;

use App\Repositories\Themes\ThemeRepository;
use App\Repositories\Web\SettingRepository;
use App\Validators\Themes\ActiveValidator;
use App\Validators\Themes\OptionValidator;

class ThemeController extends AdminController
{
    protected $module = 'themes';

    protected $moduleName = 'Giao diện';

    // protected $flashMode = true;


    /**
     * @var \App\Repositories\Themes\ThemeRepository $repository
     */
    public $repository;

    /**
     * Undocumented variable
     *
     * @var OptionRepository
     */
    public OptionRepository $optionRepository;
    /**
     * Undocumented variable
     *
     * @var FileRepository
     */
    public FileRepository $fileRepository;

    /**
     * @var string $themeZipDir
     */
    protected $themeZipDir = null;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ThemeRepository $themeRepository, OptionRepository $optionRepository, FileRepository $fileRepository)
    {
        $this->repository = $themeRepository;
        $this->optionRepository = $optionRepository;
        $this->fileRepository = $fileRepository;
        $this->init();
        $this->themeZipDir = base_path('themes/zip');
    }

    public function getList(Request $request)
    {
        return $this->getPublishThemes($request);
    }

    /**
     * lấy danh sách tất cả các them được publish
     *
     * @param Request $request
     * @return void
     */
    public function getPublishThemes(Request $request)
    {
        $urls = [
            'search' => $this->getModuleRoute('search'),
            'detail' => $this->getModuleRoute('detail'),
            'active' => $this->getModuleRoute('active'),
        ];
        return $this->viewModule('publish', compact('urls'));
    }

    public function getMyThemes(Request $request)
    {

        $urls = [
            'search' => $this->getModuleRoute('search-my-themes'),
            'detail' => $this->getModuleRoute('my-detail'),
            'active' => $this->getModuleRoute('active'),
            'delete' => $this->getModuleRoute('delete'),
        ];
        $form = $this->getJsonInputs('themes');
        $inputs = $form['inputs'] ?? [];
        $config = $form['config'] ?? [];
        $tab = $request->tab;
        return $this->viewModule('my-themes', compact('urls', 'inputs', 'config', 'tab'));
    }

    public function getMyThemeDetail(Request $request)
    {
        extract($this->apiDefaultData);
        if ($request->id && $detail = $this->repository->detail($request->id)) {
            $data = $detail;
            $status = true;
        } else {
            $message = "Không tìm thấy mục yêu càu";
        }
        return $this->json(compact(...$this->apiSystemVars));
    }



    /**
     * them  thong tin tuoc khi luu
     * @param Request $request
     * @param Arr $data
     *
     * @return void
     */
    public function beforeCreate(Request $request, Arr $data)
    {
        $slug = $this->repository->slug($data->name, null, $data->version);
        $data->slug = $slug;
        $data->owner_id = $request->user()->id;
    }



    /**
     * them  thong tin tuoc khi luu
     * @param Request $request
     * @param Arr $data
     *
     * @return void
     */
    public function beforeSave(Request $request, Arr $data)
    {
        $slug = ($request->id && $theme = $this->repository->find($request->id)) ? $theme->slug : $data->slug;

        if ($zip = $this->uploadFile($request, 'zip', $slug, $this->themeZipDir)) {
            $data->zip = $zip->filename;
        } elseif ($request->hasFile('zip')) {
            if (!is_dir($this->themeZipDir)) {
                return redirect()->back()->with('error', "Thư mục không tồn tại [$this->themeZipDir]");
            }
        } else {
            $data->remove('zip');
        }
        $this->uploadImageAttachFile($request, $data, 'image', content_path('themes'));
    }

    /**
     * luu thu vien anh
     *
     * @param Request $request
     * @param \App\Models\Theme $result
     * @return void
     */
    public function afterSave(Request $request, $result)
    {
        //nếu có gallery
        if ($request->id == $result->id) {
            $this->fileRepository->deleteRefFileIgnoreList('theme', $result->id, is_array($request->gallery_ids) ? $request->gallery_ids : []);
        }
        // if($request->gallery_data){
        //     $this->fileRepository->saveBase64List($request->gallery_data, 'theme', $result->id, $request->user()->id);
        // }
        $rs =  $this->extract($request, $result->id);
        if (!$rs['status']) {
            $this->addRedirectData('error', $rs['message']);
        }
    }

    /**
     * xóa file sau khi xoa theme
     * @param Model
     * @return void
     */
    public function afterDelete($theme)
    {
        if ($theme) {
            if ($path = $theme->getZipPath()) unlink($path);

            $filemanager = $this->getFilemanager();
            $filemanager->delete(base_path('themes/containers/' . $theme->slug));

            if (is_dir($assets = public_path('static/assets/' . $theme->slug))) {
                $filemanager->delete($assets);
            }
            if (is_dir($views = resource_path('views/themes/' . $theme->secret_id))) {
                $filemanager->delete($views);
            }
        }
    }

    public function devUpdate(Request $request, $id = null)
    {
        extract($this->apiDefaultData);
        $id = $id ? $id : $request->id;
        if (
            /*  || */
            !$id
            || !($theme = $this->repository->findBy($this->primaryKeyName, $id))
            || !($filemanager = $this->getFilemanager($this->themeZipDir))

        ) {
            $message = 'Giao diện không tồn tại';
        } elseif (
               !($themeDir = base_path('themes/containers/' . $theme->slug))
            || !is_dir($assets = $themeDir . '/assets')
            || !is_dir($views = $themeDir . '/views')

        ) {
            $message = 'Cấu trúc thư mục không hợp lệ';
        } elseif (
            /*  || */
               !$filemanager->copyFolder($assets, public_path('static/assets/' . $theme->slug))
            || !$filemanager->copyFolder($views, resource_path('views/themes/' . $theme->secret_id))

        ) {
            $message = 'Không thể copy file';
        } elseif (!$theme->available && !$this->repository->update($theme->id, ['available' => 1])) {
            $message = 'Không thể cập nhật available';
        } elseif (!$this->repository->active($id)) {
            $message = 'Không thể active';
        } else {
            $this->repository->createMetaData($id);
            $status = true;
            $message = "Update Successful!";
        }
        return compact(...$this->apiSystemVars);
    }

    /**
     * giải nén file
     * @param Request $request
     *
     * @return reponse
     */
    public function extract(Request $request, $id = null)
    {
        extract($this->apiDefaultData);
        $id = $id ? $id : $request->id;
        if (!$id || !($theme = $this->repository->findBy($this->primaryKeyName, $id)) || !($filemanager = $this->getFilemanager($this->themeZipDir))) {
            $message = "Theme không tồn tại hoặc đã bị xóa. Vui lòng kiểm tra lại!";
        } elseif (!$filemanager->extract($theme->zip, $themeDir = base_path('themes/containers/' . $theme->slug))) {
            $message = "Giải nén không thành công! Vui lòng thử lại sau giây lát.";
        } elseif (!is_dir($assets = $themeDir . '/assets') || !is_dir($views = $themeDir . '/views')) {
            // $filemanager->delete($themeDir);
            $message = "Cấu trúc thư mục không hợp lệ";
        } elseif (!$filemanager->copyFolder($assets, public_path('static/assets/' . $theme->slug)) || !$filemanager->copyFolder($views, resource_path('views/themes/' . $theme->secret_id))) {
            $filemanager->delete($themeDir);
            $message = "Lỗi không thể cài đặt theme";
        } elseif (!$theme->available && !$this->repository->update($theme->id, ['available' => 1])) {
            $message = "Không thể cập nhật trạng thái có thể sử dụng!";
        } else {
            $this->repository->createMetaData($id);
            $status = true;
            $message = "Đã giải nén thảnh công!";
            // $filemanager->delete($assets);
            // $filemanager->delete($views);
            // $filemanager->delete($themeDir.'/config');
            // $filemanager->delete($themeDir.'/components');



        }
        return compact(...$this->apiSystemVars);
    }




    /**
     * tim kiếm
     *
     * @param Request $request
     * @return Json
     */
    public function beforeAjaxSearch(Request $request, Arr $args)
    {
        $this->repository->activePublishMode();
        if ($request->type) {
            $this->repository->like('web_types', $request->type);
        }
    }

    public function beforeGetResourceDetail(Request $request)
    {
        $this->repository->detailQuery();
    }

    public function ajaxSearchMyThemes(Request $request)
    {
        $args = [];
        if ($request->tab == 'available') {
            $args['available'] = 1;
        } elseif ($request->tab == 'pending') {
            $args['available'] = 0;
        }
        // $this->repository->setCollectionClass(MyThemeCollection);
        return $this->getAjaxData($request, $args);
    }
    /**
     * kích hoạt theme
     *
     * @param Request $request
     * @return JSON
     */
    public function active(Request $request)
    {
        extract($this->apiDefaultData);

        if (!($validator = $this->repository->validator($request, ActiveValidator::class)) || !$validator->success()) {
            $message = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại';
            if ($validator) $errors = $validator->errors();
        } elseif (!$this->repository->active($request->id)) {
            $message = 'Lỗi không xác định. Vui lòng thử lại sau giây lát';
        } else {
            $status = true;
        }
        return $this->json(compact(...$this->apiSystemVars));
    }


    public function getThemeOptionForm(Request $request)
    {
        //
        if (!($theme = get_active_theme()) || !($optionGroups = $this->repository->getOptionGroupData($theme->id))) {
            return $this->showError($request, 404, 'Mục này không tồn tại');
        }
        $this->activeMenu('theme-option');
        $tab = $request->group;
        return $this->viewModule('option', compact('optionGroups', 'theme', 'tab'));
    }


    /**
     * THÊM ITEM
     *
     * @param Request $request
     * @param string $group
     * @return JSON
     */
    public function saveThemeOption(Request $request, $group = null)
    {
        $validator = $this->repository->validator($request, OptionValidator::class);
        if (!$validator->success()) {
            return redirect()->back()->withErrors($validator->getErrorObject())->withInput();
        }
        // tao doi tuong data de de truy cap phan tu

        $data = new Arr($validator->inputs());

        $theme = get_active_theme() ?? (new Arr(['slug' => 'theme-data', 'id' => 0]));

        $fileList = $this->repository->getOptionItems($request->route('group'), ['type' => 'file']);

        if (count($fileList)) {
            foreach ($fileList as $item) {
                $this->uploadAttachFile($request, $data, $item->name, content_path('themes/' . $theme->slug . '/' . $request->route('group')));
            }
        }
        // cập nhật danh sách
        $redirect = redirect()->route($this->routeNamePrefix . 'themes.options.group', ['group' => $request->route('group')]);
        if (!$this->repository->updateOptionData($request->route('group'), $data->all())) {
            $redirect->with('error', "Lỗi không xác định");
        } else {
            $redirect->with('success', "Cập nhât dữ liệu thành công!");
        }
        return $redirect;
    }
}
