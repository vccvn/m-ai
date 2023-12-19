<?php

namespace App\Http\Controllers\Web\AR;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\AR\ModelRepository;


use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ModelController extends WebController
{
    protected $module = 'ar.models';

    protected $moduleName = 'AR Model';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var ModelRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModelRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function viewModel(Request $request, $id = null)
    {
        $secret_id = $id ? $id : ($request->id ?? $request->secret_id);
        if ($secret_id && $model = $this->repository->mode('mask')->first(['secret_id' => $secret_id])) {
            $qr_code_image_url = $model->getQRCodeImageUrl();


            return $this->viewModule('view', ['model' => $model, 'qr_code_image_url' => $qr_code_image_url]);
        }
        abort(404);
    }
    public function viewImageTracking(Request $request, $secret = null)
    {
        $secret_id = $secret ? $secret : ($request->secret ?? $request->secret_id);
        if ($secret_id && $model = $this->repository->mode('mask')->first(['secret_id' => $secret_id])) {
            $model_data = $model->model_data;
            $x = 1;
            if(is_array($model_data) && array_key_exists('size', $model_data) && is_array($size = $model_data['size']) && array_key_exists('x', $size))
                $x = $size['x'];
            // dd($model);
            return $this->viewModule('tracking', ['model' => $model, 'x' => $x]);
        }
        abort(404);
    }
    public function viewDemo(Request $request, $id = null){
        return $this->viewModule('demo');
    }
}
