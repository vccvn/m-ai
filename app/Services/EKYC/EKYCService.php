<?php

namespace App\Services\EKYC;

use App\Excels\AgentReportDownloader;
use App\Models\AgentPaymentLog;
use App\Models\User;
use App\Repositories\Payments\AgentPaymentLogRepository;
use App\Services\Service;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\Reports\LogRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Mailers\Mailer;
use Carbon\Carbon;
use Gomee\Apis\Api;

class EKYCService extends Service
{
    protected $module = 'ekyc';

    protected $moduleName = 'eKYC';
    /**
     * repository chinh
     *
     * @var UserRepository
     */
    public $repository;


    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }


    protected $d = null;
    public function verifyCICard($filename = null)
    {
        // ob_start();
        $curl = curl_init();

        $fileName = $filename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $finfo = finfo_file($finfo, $fileName);
        $cFile = curl_file_create($fileName, $finfo, basename($fileName));
        $data = array("image" => $cFile, "filename" => $cFile->postname);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.fpt.ai/vision/idr/vnm",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "api-key: 6l2DUraEStE2xANshDUhEjtX6PSoQRKS"
            ),
        ));

        // $api = new Api();
        // $res = $api->post("https://api.fpt.ai/vision/idr/vnm", $data, ['api-key' => "6l2DUraEStE2xANshDUhEjtX6PSoQRKS"]);

        // dd(json_decode($res->getBody()->getContents(), true));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {

             ['errorCode' => 1000, 'errorMessage' => $err];
        }

    }
}
