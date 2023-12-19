<?php

namespace App\Http\Controllers\Web;

# use App\Http\Controllers\Web\WebController;

use App\Engines\Breadcrumb;
use App\Engines\CacheEngine;
use App\Engines\ViewManager;
use App\Http\Controllers\Controller;
use App\Repositories\Html\AreaRepository;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Web\Html;
use App\Web\HtmlAreaList;
use App\Web\Options;
use Gomee\Engines\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Mobile_Detect;

class WebController extends Controller
{


    public static $isShare = false;
    /**
     * @var string $routeNamePrefix
     */
    protected $routeNamePrefix = 'web.';

    protected $containers = [];

    /**
     * @var string $viewFolder thu muc chua view
     * khong nen thay doi lam gi
     */
    protected $viewFolder = 'web';
    /**
     * @var string dường dãn thư mục chứa form
     */
    protected $formDir = 'web/forms';

    /**
     * @var string $menuName
     */
    protected $menuName = 'web_menu';

    protected $scope = 'web';


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
     * path khi su dung view cache
     *
     * @var string
     */
    public $cacheViewPrefixPath = 'modules.';

    /**
     * Breakcrump
     * @var \App\Engines\Breadcrumb $breadcrumb
     */
    protected $breadcrumb  = null;


    /**
     * chế độ view
     *
     * @var string $viewMode
     */
    public $viewMode = 'module';


    /**
     * device
     *
     * @var \Mobile_Detect
     */
    public $device = null;

    protected $webType = null;


    public function share($name = null, $value = null)
    {
        if ($this->webType != 'theme') {
            $a = $name ? (is_array($name) ? $name : (is_string($name) ? [$name => $value] : [])) : [];
            if (!static::$isShare) {
                $request = request();
                $options = new Options();
                $options->updateCache();
                set_web_data('options', $options);

                $siteinfo = siteinfo();
                $ecommerce = ecommerce_setting();
                $payment = $options->settings->payments;
                $favicons = $options->settings->favicons ?? new Arr();

                $settings = system_setting();
                $helper = new Helper();
                $current_url = URL::current();
                $current_full_query_url = URL::full();
                // $areas = app(AreaRepository::class)->cache('component-areas', $settings && $settings->cache_data_time ? $settings->cache_data_time : 0)->mode('mask')->getAreaData();
                $html = new Html(new HtmlAreaList(
                    // $areas
                ));
                // dd($html);
                set_web_data('__html__', $html);
                // static::$themeFolder = 'Webs.'.theme_path(null, true);
                $d = $this->viewFolder . '.';
                $m = $d;
                $data = array_merge(
                    $a,
                    compact('options', 'siteinfo', 'settings', 'ecommerce', 'payment', 'html', 'helper', 'current_url', 'request', 'favicons', 'current_full_query_url'),
                    [
                        '_component' => $d . 'components.',
                        '_template' => $m . 'templates.',
                        '_layout' => $m . 'layouts.',
                        '_module' => $m . 'modules.',
                        '_base' => $d,
                        '_theme' => $d,
                        '_lib' => 'web-libs.'
                    ]
                );

                view()->share($data);
                static::$isShare = true;
            } elseif ($a) {
                view()->share($a);
            }
        } else {
            ViewManager::share($name, $value);
        }



        return true;
    }

    protected function shareDefaultData($name = null, $value = null)
    {
        if (self::$isShare) return true;
        $this->share($name, $value);
        self::$isShare = true;
    }

    public function init()
    {
        $this->webType = 'theme';
        $this->breadcrumb = app(Breadcrumb::class);
        $this->device = app(Mobile_Detect::class);
        set_web_data('mobile_detect', $this->device);

        parent::init();
    }

    public function checkShare()
    {
        if (self::$isShare) return true;
        try {
            // $this->webType = 'system';
            // system_setting('frontebd_type', 'system');
        } catch (\Throwable $th) {
            // abort(222, "Hệ thống chưa được thiết lập");
        }

        $this->shareDefaultData([
            'breadcrumb' => $this->breadcrumb,
            '_device' => $this->device,
        ]);
    }

    /**
     * check view
     * @param string $bladePath
     * @return boolean
     */
    public function checkViewExists(string $bladePath)
    {
        $this->checkShare();
        $bp = ($this->viewMode == 'module' ? 'modules.' : '') . $bladePath;
        if ($this->webType != 'theme') {
            return view()->exists($this->viewFolder . '.' . $bp);
        }

        return ViewManager::checkThemeView($bp);
    }

    /**
     * kiểm tra module có tồn tại hay không
     * @param string $bladeName
     * @return boolean
     */
    public function checkModuleExists($subModule)
    {
        $this->checkShare();
        return $this->checkViewExists(($this->viewMode != 'module' ? 'modules.' : '') . $this->moduleBlade . ($subModule ? '.' . $subModule : ''));
    }

    /**
     * view
     * @param string $bladePath
     * @param array $data
     * @return \Illuminate\View\View
     */
    public function view(string $bladePath, array $data = [])
    {
        $this->checkShare();
        $bp = ($this->viewMode == 'module' ? 'modules.' : '') . $bladePath;
        $viewdata = array_merge($data, [
            'module_slug' => $this->module,
            'module_name' => $this->moduleName,
            'route_name_prefix' => $this->routeNamePrefix
        ]);

        if ($this->webType != 'theme') {
            return view($this->viewFolder . '.' . $bp, $viewdata);
        }
        return ViewManager::theme($bp, $viewdata);
    }

    /**
     * giống view nhung trỏ sẵn vào module
     * @param string $bladeName
     * @param array $data dữ liệu truyền vào
     */
    public function viewModule($subModule, array $data = [])
    {
        $this->checkShare();
        return $this->view(($this->viewMode != 'module' ? 'modules.' : '') . $this->moduleBlade . ($subModule ? '.' . $subModule : ''), $data);
    }

    /**
     * lấy thông tin cche của view
     *
     * @param Request $request
     * @param string $bladeName
     * @param mixed $data
     * @param string $key
     * @param string $use_param
     * @return mixed
     */
    public function cacheView(Request $request, $bladeName = null, $data = null, $key = null, $use_param = false)
    {
        $this->checkShare();
        // trường hợp không cache
        $id = ($user = $request->user()) ? $user->id : null;
        if ($this->cacheViewTime <= 0) {
            if (is_array($data)) $viewData = $data;
            elseif (is_callable($data) && is_array($calledData = $data($request))) $viewData = $calledData;
            else $viewData = [];
            $html = $this->view($bladeName, $viewData);
            return $html;
        }

        if (!$key) $key = $bladeName;
        $key = 'view-' . $key . ($id?'-' . $id: '');
        if ($use_param) {
            $params = $request->all();
            ksort($params);
        } else {
            $params = [];
        }


        if (!($html = CacheEngine::get($key, $params))) {
            $viewData = [];
            if (is_array($data)) $viewData = $data;
            elseif (is_callable($data) && is_array($calledData = $data($request))) {
                $viewData = $calledData;
            }
            $html = $this->view($bladeName, $viewData);
            if (!$id && $this->cacheViewTime > 0) {
                $html = $html->render();
                CacheEngine::set($key, $html, $this->cacheViewTime, $params);
            }
        }
        return $html;
    }

    /**
     * lấy cache module hoặc tạo mới
     *
     * @param Request $request
     * @param string $subModule
     * @param array|callable $data
     * @param string $key
     * @param bool $use_param
     * @return View
     */
    public function cacheViewModule(Request $request, $subModule, $data = null, $key = null, $use_param = false)
    {
        $this->checkShare();
        if (!$key) $key = $subModule;
        $key = 'module-' . $key;
        return $this->cacheView($request,  $this->moduleBlade . '.' . $subModule, $data, $key, $use_param);
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
        $this->checkShare();
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
        if ($this->cacheViewTime <= 0) {
            if (is_callable($callback)) {
                return $callback($request);
            }
            return $callback;
        }
        $uri = $withQueryString ? $request->getRequestUri() : $request->getPathInfo();
        $isMobileKey = $this->device->isMobile() ? 'mobile-' : 'desktop-';
        $urlKey =  (static::class) . $isMobileKey . 'cache-url-' . $uri . ($id?'-' . $id : '');
        if (!($data = CacheEngine::get($urlKey))) {
            $d = null;
            if (is_callable($callback) && $calledData = $callback($request)) {
                $d = $calledData;
            }
            if ($d instanceof Htmlable) {
                $data = $d->toHtml();
            } elseif (is_a($d, \Illuminate\View\View::class)) {
                $data = $d->render();
            } elseif (is_object($d) && method_exists($d, 'toArray')) {
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
        $this->checkShare();
        $id = ($user = $request->user()) ? $user->id : null;
        if ($this->cacheViewTime <= 0) {
            if (is_callable($callback)) {
                return $callback($request);
            }
            return $callback;
        }
        $urlKey = 'cache-controller-' . $key . '-' . str_slug($request->getRequestUri()) . ($id ? '-' . $id : '');

        if (!($data = CacheEngine::get($urlKey))) {
            $d = null;
            if (is_callable($callback) && $calledData = $callback($request)) {
                $d = $calledData;
            }

            if ($d instanceof Htmlable) {
                $data = $d->toHtml();
            } elseif (is_a($d, \Illuminate\View\View::class)) {
                $data = $d->render();
            } elseif (is_object($d) && method_exists($d, 'toArray')) {
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
     * @param \App\Repositories\BaseRepository $repository
     * @return \Gomee\Repositories\BaseRepository|\Gomee\Repositories\CacheTask
     */
    public function cacheTask(Request $request, $key, $repository = null)
    {
        $this->checkShare();
        if (!$repository) $repository = $this->repository;
        return $repository->cache($key, $this->cacheDataTime, $request->all());
    }

    public function __set($name, $value)
    {
        $this->containers[$name] = $value;
    }

    public function __get($name)
    {
        return $this->containers[$name]??null;
    }
}
