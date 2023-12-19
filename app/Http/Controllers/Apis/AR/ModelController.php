<?php

namespace App\Http\Controllers\Apis\AR;

use App\Http\Controllers\Apis\ApiController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\AR\ModelRepository;
use Gomee\Files\Filemanager;

class ModelController extends ApiController
{
    protected $module = 'models';

    protected $moduleName = 'Model';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ModelRepository
     */
    public $repository;

    /**
     * file manager
     *
     * @var Filemanager
     */
    public $filemanager = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModelRepository $repository, Filemanager $filemanager)
    {
        $this->repository = $repository;
        $this->filemanager = $filemanager;
        $this->init();
    }

    public function updateTrackingData(Request $request, $secret = null)
    {
        extract(self::$vars);
        $secret_id = $secret ? $secret : $request->secret;
        if (!$secret_id || !($model = $this->repository->first(['secret_id' => $secret_id])))
            $message = 'Không tìm thấy model';
        else {
            $model->is_processing = 0;
            if ($request->status) {
                $model->is_image_tracking = 1;
                $folder = $model->getModelPath();
                $tracking_image = str_replace($model->secret_id, 'tracking', $request->image);
                if($model->tracking_image && file_exists($path = $folder . '/' . $model->tracking_image)){
                    unlink($path);
                }
                $model->tracking_image = $request->image;
            }
            if ($model->save())
                $status = true;
        }

        return $this->json(compact(...self::$outVars));
    }
}
