<?php

namespace App\Http\Controllers\Merchant\AR;

use App\Http\Controllers\Merchant\MerchantController;
use App\Models\ARModel;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\AR\ModelRepository;
use App\Validators\AR\ModelUploadValidator;
use Gomee\Apis\Api;
use Gomee\Files\Filemanager;
use Gomee\Files\Image;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ModelController extends MerchantController
{
    protected $module = '3d.models';

    protected $moduleName = 'Model';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ModelRepository
     */
    public $repository;

    /**
     * @var array $supportExtensions
     */
    protected $supportExtensions = [];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModelRepository $repository)
    {
        $this->repository = $repository;

        $this->supportExtensions = get_3d_support_extensions();
        $this->init();
    }

    public function downloadTrackingImage(Request $request, $secret_id = null)
    {
        $secret_id = $secret_id ? $secret_id : $request->secret;
        if (!$secret_id || !($model = $this->repository->first(['secret_id' => $secret_id])))
            abort(404);
        if (!$model->tracking_image || !($path = $model->getModelPath($model->tracking_image)))
            abort(404);

        $file     = File::get($path);
        $type     = File::mimeType($path);
        $response = Response::download($path, $request->filename, array(
            'Content-Type: ' . $type,
        ));
        return $response;
    }
    public function prepareGetCrudForm(Request $request)
    {
        $this->flashMode = true;
    }

    public function beforeUpdate(Request $request, Arr $data, ARModel $old)
    {

        // do something


        if (!$request->hasFile('tracking_image_file')) {
            //
            return;
        }
        $redirect = redirect()->route($this->routeNamePrefix . '3d.models.update', ['id' => $old->id])->withInput();
        $filename = uniqid();
        if (!($file = $this->uploadImage($request, 'tracking_image_file', $filename, $folder = $old->getModelPath()))) {
            return $redirect->withErrors(['tracking_image_file.upload' => 'Không thể tạo Tải lên file ảnh tracking'])->with('error', 'Lỗi! Không thể tải lên file ảnh');
        } else {
            $image = new Image($file->filepath);
            $width = $image->getWidth();
            $height = $image->getHeight();
            $size = (int) ($width < $height ? $width : $height);
            $qrSize = (int) $size / 5;
            Filemanager::addMineType('mind', 'application/octet-stream');
            if ($request->nft_file_data && $mind = $this->filemanager->saveBase64($request->nft_file_data, $folder . '/' . $filename . '.mind')) {
                $data->lib = 'mind';
                $data->is_image_tracking = 1;
                $qrSize = (int) $size / 6;
                $image->insertQR($old->getTrackingUrl(), $qrSize, 'left', 'bottom', 15);
                $image->save($file->filepath);
                $data->tracking_image = $file->filename;
                $this->redirectData = [
                    'success' => 'Đã cập nhật thông tin thành công!<br />Sẽ mất chút thời gian để xử lý ảnh tracking'
                ];
            } elseif ($image->getWidth() < 1000 || $image->getHeight() < 1000)
                return $redirect->withErrors(['tracking_image_file.sizes' => 'Kích thước ảnh tracking tối thiểu là 1000x1000px'])->with('error', 'Lỗi! Kích thước ảnh tracking không hợp lệ');
            else {
                $image->insertQR($old->getTrackingUrl(), $qrSize, 'left', 'bottom', 15);
                $image->save($file->filepath);
                //
                $api = new Api();
                $api->setResponseType('json');
                $res = $api->post('http://' . config('system.api.ip') . ':' . config('system.api.port') . '/nft-creator', [
                    'folder' => $folder,
                    'relative_folder' => $old->getBaseRelativeFolder(),
                    'image' => $file->filename,
                    'secret' => $old->secret_id,
                    'callback' => route('api.ar.models.update-tracking')
                ]);
                if (!is_array($res) || !array_key_exists('status', $res)) {
                    return $redirect->withErrors(['tracking_image_file.convert' => 'Không thể tạo NFT'])->with('error', 'Lỗi. Không thể tạo NFT');
                }
                if (!$res['status']) {
                    return $redirect->withErrors(['tracking_image_file.convert' => 'Không thể tạo NFT'])->with('error', $res['message'] ?? 'Lỗi! Không thể tạo NFT');
                }

                $data->is_processing = 1;
                $this->redirectData = [
                    'success' => 'Đã cập nhật thông tin thành công!<br />Sẽ mất chút thời gian để xử lý ảnh tracking'
                ];
            }
        }
    }

    public function afterSave(Request $request, $model)
    {
        $this->flashMode = true;
        # code...
    }

    public function afterDelete($result = null)
    {
        /**
         * @var User
         */
        $user = auth()->user();
        $user->upload_count++;
        $user->save();
    }

    /**
     * Hiển thị danh sách các kết quar tim dc
     * @param Request $request
     * @return View
     */
    public function getIndex(Request $request)
    {
        $this->repository->mode('mask');
        $data = [];
        $data['results'] = $this->getResults($request, ['user_id' => $request->user()->id]);
        return $this->viewModule($this->index, $data);
    }

    /**
     * Hiển thị danh sách các kết quar tim dc
     * @param Request $request
     * @return View
     */
    function getList(Request $request)
    {

        // $this->activeMenu($this->moduleMenuKey.'.list');
        $this->repository->mode('mask');
        $data = [];
        $data['results'] = $this->getResults($request, ['user_id' => $request->user()->id]);
        // $arrData = new Arr($data);
        // $this->callViewEvent('beforeGetListView', $request, $arrData);

        // co the code them =))))))

        return $this->viewModule($this->list, $data);
    }


    /**
     * Hiển thị danh sách các kết quar tim dc
     * @param Request $request
     * @return View
     */
    function getDetail(Request $request, $id = null)
    {
        $this->repository->mode('mask');

        if ($id && $detail = $this->repository->getDetail(['id' => $id, 'user_id' => $request->user()->id])) {
            $data = [];
            $data['detail'] = $detail;
            // $arrData = new Arr($data);
            // $this->callViewEvent('beforeGetDetailView', $request, $arrData);
            return $this->viewModule($this->detail, $data);
        }

        // co the code them =))))))
        return $this->showError($request, 404, "Mục này không tồn tại hoặc đã bị xóa");
    }


    /**
     * Hiển thị danh sách Dã bị xóa tạm thời
     * @param Request $request
     * @return View
     */
    function getTrash(Request $request)
    {
        $this->repository->mode('mask');
        // $this->activeMenu($this->moduleMenuKey.'.trash');
        $this->repository->resetDefaultParams('deleted');
        $data = [];
        $data['results'] = $this->getResults($request, ['deleted' => 1, 'user_id' => $request->user()->id]);


        return $this->viewModule($this->trash, $data);
    }

    public function beforeGetUpdateForm($request, $config, $inputs, $data, $attrs)
    {
        add_css_link('static/manager/css/model-form.min.css');
        add_js_data('model_data', $data->toArray());
        add_js_data('model_config', [
            'urls' => [
                'preview' => $this->getModuleRoute('preview'),
                'editor' => route('admin.3d.items.edit', ['secret_id' => $data->secret_id])
            ]
        ]);


        add_js_src('static/manager/js/r3d.bundle.js');
        add_js_src('static/manager/js/model-form.js');
        set_merchant_template_data('modals', 'preview-modals');

        // dd($data);
    }

    public function getUploadForm(Request $request)
    {
        add_js_data('model_config', [
            'urls' => [
                'first_update' => route($this->routeNamePrefix . '3d.models.first-update'),

                'preview' => $this->getModuleRoute('preview'),
                'editor' => route('admin.3d.items.edit', ['secret_id' => 'SECRET_ID'])
            ]
        ]);
        return $this->viewModule('upload');
    }

    /**
     * tải file lên bằng dropzone
     * @param Request $request
     * @return json
     */
    public function doUpload(Request $request)
    {

        extract($this->apiDefaultData);
        $secret_id = strtoupper(substr(md5(uniqid() . time()), 10, 8));
        $this->repository->setValidatorClass(ModelUploadValidator::class);
        $validator = $this->repository->validator($request);
        $p = 'static/sources/models/' . $secret_id;
        if (!$validator->success()) {
            $message = "Đã có lỗi xảy ra. Vui lòng kiểm tra lại!";
            $errors = $validator->errors();
        } elseif (!($file = $this->uploadFile($request, 'file', null, $p))) {
            $message = "Đã có lỗi xảy ra. Không thể upload file";
        }
        // chết đoạn này à cai nà chỉ luu db thoi
        else {
            $pp = public_path($p);
            // $f = $request->file('file');
            $ext = strtolower($file->extension);
            $n = substr($file->original_filename, 0, strlen($file->original_filename) - 1 - strlen($ext));
            $filename = $file->filename;
            $path = '/' . $p . '/';
            $s = true;
            $zip_file = null;
            if ($ext == 'zip') {
                // dd();
                if (!$this->filemanager->extract($file->filepath, $pp)) {
                    // $this->filemanager->delete($pp);
                    $s = false;
                    $message = 'Không thể giải nén';
                } else if (!($d = $this->find3DFile($pp, $p . '/'))) {
                    $this->filemanager->delete($pp);
                    $s = false;
                    $message = 'Không tìm thấy file 3D';
                } else {
                    $path = '/' . $d['path'] . '/';
                    $filename = $d['file'];
                    $ext = $d['ext'];
                    $zip_file = $filename;
                }
            }

            $e = strtolower($ext);
            if ($e == 'glb') $e = 'gltf';


            $d = [
                'name' => $n,
                'path' => $path,
                'file' => $filename,
                'secret_id' => $secret_id,
                'user_id' => $request->user()->id,
                'zip_file' => $zip_file,
                'type' => $e,
                'model_data' => [
                    'size' => [
                        '__isObject__' => true,
                    ],
                    'load_options' => [
                        'useRoughnessMipmapper' => true,
                        'materialNeedsUpdate' => true
                    ],
                    'settings' => [
                        '__isObject__' => true,
                    ]
                ]
            ];

            if ($s) {
                if ($model = $this->repository->create($d)) {
                    $status = true;
                    $model->getTrackingQRCodePath();
                    $data = $this->repository->mode('mask')->detail($model->id);
                    $user = $request->user();
                    $user->upload_count--;
                    $user->save();
                } else {
                    $message = "Đã có lỗi xảy ra. Vui lòng kiểm tra lại!";
                    $this->filemanager->delete($pp);
                }
            }
            // $data = $file->all();

        }

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function find3DFile($dir, $rootPath = '')
    {
        $rp = rtrim($rootPath, '/') . '/';
        $list = $this->filemanager->getList($dir);
        foreach ($list as $item) {
            if ($item->type == 'file') {
                if (in_array($e = strtolower($item->extension), $this->supportExtensions) && $item->extension != 'zip') {
                    return ['path' => $rootPath, 'file' => $item->name, 'ext' => $e];
                }
            } elseif ($d = $this->find3DFile($item->path, $rp . $item->name)) {
                return $d;
            }
        }
        return null;
    }


    public function FirstUpdate(Request $request)
    {
        extract($this->apiDefaultData);
        if (!$request->id || !($item = $this->repository->first(['id' => $request->id, 'user_id' => $request->user()->id]))) {
            $message = "Không tìm thấy model";
        } else {
            $d = $item->model_data;
            if (!is_array($d)) {
                try {
                    $d = json_decode($d, true);
                } catch (\Throwable $th) {
                    $d = [
                        '__isObject' => true
                    ];
                }
            }
            if (is_array($request->size)) {
                $item->model_data = array_merge($d, ['size' => $request->size]);
            }
            if ($request->thumbnail && $file = $this->saveBase64Image($request->thumbnail, 'thumbnail', 'static/sources/models/' . $item->secret_id)) {
                $item->thumbnail = $file->filename;
            }
            if (!$item->save()) {
                $message = "Lỗi không xác định";
            } else {
                $status = true;
                $data = $this->repository->mode('mask')->detail($item->id);
            }
        }
        return $this->json(compact(...$this->apiSystemVars));
    }

    public function beforeDelete($model)
    {
        $this->filemanager->delete(public_path('static/sources/models/' . $model->id));
    }

    public function preview(Request $request)
    {
        if ($request->secret_id && $model = $this->repository->mode('mask')->first(['secret_id' => $request->secret_id])) {
            return $this->viewModule('preview', ['model' => $model]);
        }
        $this->showError($request, 404);
    }



    public function getItemCategories()
    {
    }
}
