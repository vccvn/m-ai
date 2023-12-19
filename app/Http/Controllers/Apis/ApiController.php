<?php

namespace App\Http\Controllers\Apis;

use App\Engines\CacheEngine;
use Gomee\Helpers\Arr;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Respond
 *
 * Trait RespondTrait
 * @package App\Http\Helpers
 */

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiController extends BaseApiController
{
    /*
     * Define version API
     * */
    public static string $VERSION_API = '1.0.0';

    /**
     * @var array $apiDefaultData đử liệu mặc định trả về api
     *
     */
    protected $apiDefaultData = [
        'statusCode' => Response::HTTP_OK,
        'status' => false,
        'message' => 'Thao tác thành công!',
        'data' => null,
        'errors' => []
    ];


    /**
     * danh sách trả về
     * @var array $apiSystemVars
     */
    protected $apiSystemVars = ['status', 'data', 'message', 'errors'];

    protected static $vars = [
        'statusCode' => Response::HTTP_OK,
        'status' => false,
        'message' => 'Thao tác thành công!',
        'data' => null,
        'errors' => []
    ];


    /**
     * danh sách trả về
     * @var array $apiSystemVars
     */
    protected static $outVars = ['status', 'data', 'message', 'errors'];

    /**
     * thời gian lưu chữ cache của view
     *
     * @var integer
     */
    public $cacheViewTime = 0;

    /**
     * thời gian lưu cach3 của data lấy từ db
     *
     * @var integer
     */
    public $cacheDataTime = 0;
    /**
     * repository
     *
     * @var BaseRepository
     */
    protected $repository;


    /**
     * @var Gomee\Validators\BaseValidator
     */
    public $validator;

    protected $crudAction = null;
    public function save(Request $request, $id = null)
    {
        extract(self::$vars);
        $this->repository->resetDefaultParams('trashed_status');
        // gan id de sac minh la update hay them moi
        $id = $id?$id: $id = $request->{$this->repository->getKeyName()};
        // is update
        if($id){
            if($record = $this->repository->find($id)){
                $result = $record;
                $action = 'Update';
                $is_update = true;
            }else{
                $data = $request->all();
                $statusCode = 404;
                $message = 'Đã có lỗi xảy ra. Cập nhật không hợp lệ';
                return $this->json(compact(...self::$outVars), $statusCode);
            }
        }
        else{
            $result = $this->repository->model();
            $action = 'Create';
            $is_update = false;
        }

        $this->crudAction = strtolower($action);
        // gọi phuong thuc bat su kien
        $event = 'before'.$action.'Validate';
        $this->callCrudEvent($event,$request, $id);
        $this->callCrudEvent('beforeValidate', $request, $id);

        $this->fire($action.'Validating', $this, $request, $id);
        $this->fire('validating', $this, $request, $id);

        // validate
        $validator = $this->repository->validator($request);
        if(!$validator->success()){
            $message = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại';
            $errors = $validator->errors();

        }else{
            $this->validator = $validator;
            // tao doi tuong data de de truy cap phan tu
            $d = new Arr($validator->inputs());
            // goi cac ham su kien truoc khi luu
            $this->callCrudEvent('before'.$action,$request, $d, $result);
            $this->callCrudEvent('beforeSave', $request, $d);

            $this->fire(($action == 'update')?'updating': 'creating', $this, $request, $result);
            $this->fire('saving', $this, $request, $d);

            // lấy thông tin bản ghi mới tạo
            if($model = $this->repository->save($d->all(), $id)){
                // gọi các hàm sau khi luu bản ghi thành công
                // $this->callCrudEvent('after'.$action, $request, $model, $d);
                // $this->callCrudEvent('afterSave', $request, $model, $d);
                $this->fire($action.'d', $this, $request, $model, $d);
                $this->fire('saved', $this, $request, $model, $d);
                $boolA = is_bool($afa = $this->callCrudEvent('after'.$action, $request, $model, $d));
                $boolS = is_bool($afs = $this->callCrudEvent('afterSave', $request, $model, $d));
                if($afa && !$boolA){
                    return $afa;
                }
                elseif($afs && !$boolS){
                    return $afs;
                }
                elseif(($boolA && $afa === false) || ($boolS && $afs === false)){
                    $message = 'Lỗi không xác định';
                }else{
                    if($action == 'create'){
                        $statusCode = Response::HTTP_CREATED;
                    }else{
                        $statusCode = Response::HTTP_OK;
                    }

                    $status = true;
                    $data = $this->repository->detail($model->{$this->repository->getKeyName()});

                }

            }else{
                $message = 'Lỗi không xác định';
            }

        }
        return $this->json(compact(...self::$outVars));
    }

    /**
     * Respond with api result
     *
     * @param mixed $data
     * @param int   $statusCode
     *
     * @return JsonResponse
     */
    public function json(mixed $data = [], int $statusCode = Response::HTTP_OK, $headers = []): JsonResponse
    {

        $status = array_key_exists('status', $data) ? $data['status'] : ($statusCode == Response::HTTP_OK ? true : false);
        $code = array_key_exists('statusCode', $data) ? (
            $status == true?$data['statusCode']:(
                $data['statusCode'] == Response::HTTP_OK && $statusCode != Response::HTTP_OK?$statusCode:$data['statusCode']
            )
        ) : $statusCode;
        $text = Response::$statusTexts[$code]??'';
        $errors = [];
        if(array_key_exists('errors', $data)){
            if(is_array($data['errors'])){
                foreach ($data['errors'] as $key => $value) {
                    if(is_array($value)){
                        $errors[] = $value;
                    }else{
                        $errors[] = [
                            'key' => $key,
                            'message' => $value
                        ];
                    }
                }
            }
        }
        $data_format = array_merge($data, [
            'status' => $status,
            'status_code' => $code,
            'status_text' => $text,
            'message' => $data['message'] ?? $text,
            'data' => $data['data']??null,
            'errors' => $errors
        ]);
        $response = response()->json($data_format)->setStatusCode($code);
        return $response;
    }





    /**
     * thao tác với data trong csdl thông qua hàm callback
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function cacheData($key, $callback)
    {
        $k = (static::class) . '-data-' . $key;

        if ($this->cacheDataTime <= 0 || !($data = CacheEngine::get($k))) {
            $d = null;
            if (is_callable($callback) && $calledData = $callback()) {
                $d = $calledData;
            }
            if ($d instanceof Htmlable) {
                $data = $d->toHtml();
            } elseif (is_a($d, \Illuminate\View\View::class)) {
                $data = $d->render();
            } elseif (is_object($d) && method_exists($d, 'render')) {
                $data = $d->render();
            } else {
                $data = $d;
            }

            if ($this->cacheDataTime > 0) {
                CacheEngine::set($k, $data, $this->cacheDataTime);
            }
        }
        return $data;
    }

    /**
     * cache theo url
     *
     * @param Request $request
     * @param bool $withQueryString
     * @param \Closure $callback
     * @return mixed
     */
    protected function cacheUrl(Request $request, $withQueryString = false, $callback = null)
    {
        $id = ($user = $request->user()) ? $user->id : null;
        if ($id || $this->cacheViewTime <= 0) {
            if (is_callable($callback)) {
                return $callback($request);
            }
            return $callback;
        }
        $uri = $withQueryString ? $request->getRequestUri() : $request->getPathInfo();
        $isMobileKey =  'desktop-';
        $urlKey = $isMobileKey . 'cache-url-' . $uri;
        if (!($data = CacheEngine::get($urlKey))) {
            $d = null;
            if (is_callable($callback) && $calledData = $callback($request)) {
                $d = $calledData;
            }
            if (is_object($d) && method_exists($d, 'toArray')) {
                $data = $d->toArray();
            } elseif (is_object($d) && method_exists($d, 'render')) {
                $data = $d->render();
            } else {
                $data = $d;
            }

            if (!$id && $this->cacheViewTime > 0) {
                CacheEngine::set($urlKey, $data, $this->cacheViewTime);
            }
        }
        return $data;
    }


    /**
     * lấy thông tin cache của view
     *
     * @param Request $request
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     */
    public function cache(Request $request, $key, $callback = null)
    {
        $id = ($user = $request->user()) ? $user->id : null;
        if ($id || $this->cacheViewTime <= 0) {
            if (is_callable($callback)) {
                return $callback($request);
            }
            return $callback;
        }
        $urlKey = 'cache-controller-' . $key . '-' . str_slug($request->getRequestUri());

        if (!($data = CacheEngine::get($urlKey))) {
            $d = null;
            if (is_callable($callback) && $calledData = $callback($request)) {
                $d = $calledData;
            }

            if (is_object($d) && method_exists($d, 'toArray')) {
                $data = $d->toArray();
            } elseif (is_object($d) && method_exists($d, 'render')) {
                $data = $d->render();
            } else {
                $data = $d;
            }
            if (!$id && $this->cacheViewTime > 0) {
                CacheEngine::set($urlKey, $data, $this->cacheViewTime);
            }
        }
        return $data;
    }







    /**
     * lấy cache task của repository
     *
     * @param Request $request
     * @param string $key
     * @param \App\Repositories\Base\BaseRepository $repository
     * @return \App\Repositories\Base\BaseRepository|\App\Repositories\Base\CacheTask
     */
    public function cacheTask(Request $request, $key, $repository = null)
    {
        if (!$repository) $repository = $this->repository;
        return $repository->cache($key, $this->cacheDataTime, $request->all());
    }
}
