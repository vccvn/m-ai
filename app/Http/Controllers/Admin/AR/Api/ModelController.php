<?php

namespace App\Http\Controllers\Admin\AR\Api;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Apis\ApiController;
use App\Masks\AR\CategoryMask;
use App\Repositories\AR\CategoryRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\AR\ModelRepository;

class ModelController extends ApiController
{
    protected $module = '3d.api.items';

    protected $moduleName = 'Model API';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ModelRepository
     */
    public $repository;

    /**
     * CatRep
     *
     * @var CategoryRepository
     */
    public $categoryRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModelRepository $repository, CategoryRepository $categoryRepository)
    {
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->init();
    }

    public function update3D(Request $request)
    {
        extract($this->apiDefaultData);
        if(!$request->id || !($model = $this->repository->first(['id'=> $request->id]))){
            $message = 'Model không tồn tại';
        }
        elseif(!$request->size && !$request->settings){
            $message = 'Không có thông tin';
        }else{
            $d = $model->model_data;
            if(!is_array($d)){
                try {
                    $d = json_decode($d, true);
                } catch (\Throwable $th) {
                    $d = [
                        '__isObject__'  => true
                    ];
                }
            }
            if($request->size && is_array($request->size)) $d['size'] = $request->size;
            if($request->settings && is_array($request->settings)) {
                if(!isset($d['settings'])){
                    $d['settings'] = [
                        '__isObject__'  => true
                    ];
                }
                $s = $request->settings;
                if(array_key_exists('meshes', $s)){
                    $m = $s['meshes'];
                    foreach ($s['meshes'] as $i => $mesh) {
                        if(array_key_exists('data', $mesh)){
                            if(!is_array($mesh['data']) || !count($mesh['data'])){
                                $mesh['data'] = ['__isObject__'  => true];
                                $m[$i] = $mesh;
                            }
                        }
                    }
                    $d['settings']['meshes'] = $m;
                }
                if(array_key_exists('props', $s)){
                    $d['settings']['props'] = $s['props'];
                }
                if(array_key_exists('options', $s)){
                    $d['settings']['options'] = $s['options'];
                }
                if(!($m = $this->repository->update($model->id, ['__data__'=>$d]))){
                    $message = 'Lỗi không xác định';
                }
                else{
                    // return $d;
                    $status = true;
                    $data = $m;
                }
                
            }
            
        }
        
        

        return $this->json(compact(...$this->apiSystemVars));
    }

    public function updateThumbnail(Request $request)
    {
        extract($this->apiDefaultData);
        if(!$request->id || !($model = $this->repository->first(['id'=> $request->id]))){
            $message = 'Model không tồn tại';
        }
        elseif(!$request->thumbnail){
            $message = 'Không có ảnh';
        }elseif(!($file = $this->saveBase64Image($request->thumbnail, 'thumbnail', 'static/sources/models/'.$model->secret_id))){
            $message = 'Upload Không thành công';
        }
        elseif(!($model->thumbnail = $file->filename) || !($model->save()))
        {
            $message = 'Lỗi không xác định';
        }
        else{
            $status = true;
            $data = $model;
        }
        
        

        return $this->json(compact(...$this->apiSystemVars));
    }

    
    public function getItemCategories(Request $request)
    {
        // return $this->json($this->categoryRepository->mode('mask')->get());
        CategoryMask::$withItems = 6;
        return $this->getData(
            $request, 
            [], 
            $this->categoryRepository->mode('mask')
                                     ->leftJoin('ar_models as items', 'items.category_id', '=', 'categories.id')
                                     ->select('categories.*')
                                     ->selectRaw('count(items.id) as items_count')
                                     ->havingRaw('count(items.id) > 0')
                                     ->groupBy('categories.id')
        );
    }
}
