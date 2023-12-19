<?php

namespace App\Http\Controllers\Admin\Payments;

use App\Excels\ReportPaymentDownloader;
use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Payments\RequestRepository;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class RequestController extends AdminController
{
    protected $module = 'payments.requests';

    protected $moduleName = 'Giao dịch thanh toán';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var RequestRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RequestRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
        $this->activeMenu('payments');
    }

    public function export(Request $request)
    {
        extract($this->apiDefaultData);
        $list = $request->ids?  $this->repository->mode('mask')->buildDownloadQuery()->getData(['id' => $request->ids]):$this->repository->mode('mask')->getResults($request, ['@paginate' => false]);

        $times = [];
        if($request->from_date) $times[] = $request->from_date;
        if($request->to_date) $times[] = $request->to_date;

        if(!$times) $times[] = date('Ymd-his');

        $filename = 'doanh-thu-' . implode('-', $times) . '.xlsx';
        $rpd = new ReportPaymentDownloader($filename);
        $rpd->loadTemplate();
        $rpd->addReport($list);
        if($rpd->save()){
            $status = true;
            $data = [
                'url' => route('admin.payments.requests.download', ['filename' => $filename])
            ];
        }
        return $this->json(compact(...$this->apiSystemVars));
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
