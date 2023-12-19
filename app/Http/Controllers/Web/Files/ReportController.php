<?php

namespace App\Http\Controllers\Web\Files;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Reports\LogRepository;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ReportController extends WebController
{
    protected $module = 'reports';

    protected $moduleName = 'Report';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var LogRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LogRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function download(Request $request)
    {
        if(!$request->filename || !file_exists($path = storage_path('excels/downloads/' . $request->filename)))
            abort(404);
        $file     = File::get($path);
        $type     = File::mimeType($path);
        $response = Response::download($path, $request->filename,array(
            'Content-Type: ' . $type,
          ));
        return $response;
    }
}
