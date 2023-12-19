<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Files\FileRepository;
use Gomee\Files\Image;

class AssetController extends WebController
{
    protected $module = 'assets';

    protected $moduleName = 'Tài nguyên';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var FileRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function getImage(Request $request, $client_id, $ref = 'files', $width = null, $height = null, $filename = null)
    {

        $path = public_path('static/users/' .$client_id .'/'. $ref . '/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }


        $image = new Image($path);
        if (!$image->check()) {
            abort(404);
        }
        $type = $image->getMime();
        $image->resizeAndCrop($width ? $width : 300, $height ? $height : 300);

        header("Content-Type: ". $type);
        $image->show();
        die;
    }
}
