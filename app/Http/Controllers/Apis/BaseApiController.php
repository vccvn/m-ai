<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Apis\Helpers\RespondTrait;
use Gomee\Repositories\BaseRepository;
use Gomee\Services\Traits\ApiMethods;
use Gomee\Services\Traits\Events;
use Gomee\Services\Traits\FileMethods;
use Gomee\Services\Traits\ModuleMethods;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Respond
 *
 * Trait RespondTrait
 * @package App\Http\Helpers
 */

 use Illuminate\Http\JsonResponse;
 use Illuminate\Http\Response;

 /**
  * Base Api
  * @property BaseRepository $repository
  */
class BaseApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,


        // module quan li lien quan toi modulw se dc ke thua tu controller nay
        ModuleMethods,
        // tap hop cac thuoc tinh va ham lien quan den xu ly su kien
        ApiMethods,
        // tap hop cac thuoc tinh va ham lien quan den xu ly su file
        FileMethods,
        Events,
        RespondTrait;
    /**
     * repository
     *
     * @var BaseRepository
     */
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * thuc thi mot so thiet lap
     * @return void
     */
    public function init()
    {
        $this->moduleInit();
        $this->crudInit();
        $this->fileInit();

    }

    /**
     * Respond with api result
     *
     * @param mixed $data
     * @param int   $statusCode
     *
     * @return JsonResponse
     */
    public function json(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $data_format = [
            'version' => self::$VERSION_API,
            'status'  => [
                'code'    => $statusCode,
                'message' => Response::$statusTexts[$statusCode]
            ],
            'data'    => $data ?? []
        ];

        return response()->json($data_format)->setStatusCode($statusCode);
    }

    /**
     * thực hiện các thao tác với dữ liệu truoc khi trả về trình duyệt
     * @param mixed $data           Dữ liệu trả về
     * @param integer $code         Http code
     *
     * @return Response
     */
    public function response($data, $code=200, array $headers = [])
    {
        $args = [$data, $code];
        if(is_array($headers) && count($headers)) $args[] = $headers;
        $resp = response()->json(...$args);
        return $resp;
    }
}
