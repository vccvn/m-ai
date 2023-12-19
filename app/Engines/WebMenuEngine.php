<?php

namespace App\Engines;

use App\Repositories\Dynamics\DynamicRepository;
use App\Repositories\Options\OptionRepository;
use App\Services\Permissions\PermissionService;
use Gomee\Core\System;
use Gomee\Engines\JsonData;

class WebMenuEngine
{

    /**
     * duong dan thu muc
     *
     * @var string
     */
    protected $path;

    /**
     * data engin
     *
     * @var JsonData
     */
    protected $dataEngine;

    /**
     * opt repo
     *
     * @var OptionRepository
     */
    protected $optionRepository;
    /**
     * tạo đối tượng
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        $this->path = trim($path, '/') . '/';
        $this->dataEngine = new JsonData();
        $this->optionRepository = app(OptionRepository::class);
    }

    /**
     * lấy thông tin dữ liệu
     * @param string $filename
     * @return array
     */
    public function getData(string $filename)
    {
        return $this->dataEngine->getJsonData($this->path . trim($filename, '/'));
    }

    /**
     * lấy thông tin kế thừa
     *
     * @param array $rawData
     * @param string $filename
     * @return array
     */
    public function checkExtends($rawData, string $filename): array
    {
        if (!$rawData) return [];
        $data = $rawData['data'] ?? [];
        // kiểm tra trong mảng gốc có yêu cầu kế thừa hay ko
        if (array_key_exists('extends', $rawData)) {
            // lấy dử liễu file cần thừa kế
            // tạm bỏ qua nó có kế thừa cái khác hay ko
            $baseData = $this->getData($rawData['extends']);
            if ($baseData && array_key_exists('data', $baseData)) {
                $newData = [];
                $hasYield = false;
                // duyệt mảng data để lấy ra các phần tử r ném vào new data
                foreach ($baseData['data'] as $key => $value) {
                    if (substr($key, 0, 1) == '@') {
                        // kiểm tra xem có tồn tại key @yield hay ko?
                        $keyFunc = substr($key, 1);
                        if ($keyFunc == 'yield') {
                            if (
                                (is_array($value) && (in_array($filename, $value) || in_array('all', $value) || in_array('*', $value)))
                                || (is_string($value) && in_array($value, [$filename, 'all', '*']))
                            ) {
                                // nếu có thì merge với data
                                // trùng nhau sẽ được merge, chưa có sẽ dược thêm mới
                                $hasYield = true;
                                $newData = array_merge($newData, $data);
                            }
                        } elseif (in_array($keyFunc, ['include', 'module', 'modules'])) {
                            // kiểm tra xem có yêu cầu include không?
                            // có thì duyệt mảng
                            if ($keyFunc == 'include') {
                                if (!is_array($value)) $values = [$value];
                                else $values = $value;
                            } elseif (in_array($keyFunc, ['modules', 'module'])) {
                                if (!is_array($value)) $vals = [$value];
                                else $vals = $value;
                                $values = array_map(function ($val) {
                                    return 'module-' . $val;
                                }, $vals);
                            }

                            if ($values) {
                                foreach ($values as $key => $file) {
                                    if ($menu = $this->getData($file)) {
                                        $newData = array_merge(
                                            $newData,
                                            $this->checkInclude(
                                                $this->checkExtends($menu ?? [], $file)
                                            )
                                        );
                                    }
                                }
                            }
                        } else {
                            $newData[$key] = $value;
                        }
                    } else {
                        if (is_array($value) && !array_key_exists('active_key', $value)) {
                            $value['active_key'] = is_numeric($key) ? (array_key_exists('route', $value) ? preg_replace('/^admin\./i', '', $value['route']) : '') : '';
                        }
                        $newData[$key] = $value;
                    }
                }
                if ($hasYield) {
                    $data = $newData;
                } else {
                    // nếu không có yield thì sẽ merge data vào new data
                    $data = array_merge($newData, $data);
                }
            }
        }
        return $data;
    }

    /**
     * check include
     *
     * @param array $data
     * @return array
     */
    public function checkInclude($data)
    {
        if (!is_array($data)) return [];
        if (array_has_any($data, ['@include', '@module', '@modules'])) {
            // kiểm tra xem có yêu cầu include không?
            // có thì duyệt mảng
            $newData = [];
            foreach ($data as $key => $value) {
                if (substr($key, 0, 1) == '@') {
                    // kiểm tra từ khóa include
                    $keyFunc = substr($key, 1);
                    $values = [];
                    if ($keyFunc == 'include') {
                        if (!is_array($value)) $values = [$value];
                        else $values = $value;
                    } elseif (in_array($keyFunc, ['modules', 'module'])) {
                        if (!is_array($value)) $vals = [$value];
                        else $vals = $value;
                        $values = array_map(function ($val) {
                            return 'module-' . $val;
                        }, $vals);
                    } else {
                        $newData[$key] = $value;
                    }

                    if ($values) {
                        foreach ($values as $key => $file) {
                            if ($menu = $this->getData($file)) {
                                $newData = array_merge(
                                    $newData,
                                    $this->checkInclude(
                                        $this->checkExtends($menu ?? [], $file)
                                    )
                                );
                            }
                        }
                    }
                } else {
                    $newData[$key] = $value;
                }
            }
            $data = $newData;
        }

        return $data;
    }



    /**
     * lấy menu
     *
     * @param string $filename
     * @return array
     */
    public function get(string $filename)
    {
        // DynamicPost::check($request);
        if (!$filename) $filename = 'admin';
        $webType = get_web_type();
        // $webSetting = web_setting();


        $menuData = $this->getData($filename);
        $data = $this->checkInclude(
            $this->checkExtends($menuData ?? [], $filename)
        );
        $user = request()->user();
        $roles = [];
        if ($user) {
            $roles = $user->getUserRoles();
        }

        $menuitems = [];
        $issetPost = false;
        // dd($data);
            foreach ($data as $key => $item) {
                $keyFunc = substr($key, 0, 1) == '@' ? substr($key, 1) : "";

                if ($keyFunc == 'package' || $keyFunc == 'packages') {
                    $packageMenus = System::getMenus($item);
                    if ($packageMenus) {
                        foreach ($packageMenus as $key => $value) {
                            $menuitems[] = $value;
                        }

                        // dd($newData);
                    }
                } else{

                    if (array_key_exists('submenu', $item) && array_key_exists('data', $item['submenu'])) {
                        $sData = $item['submenu']['data'];
                        foreach ($sData as $index => $sub) {
                            // dump($sub);

                            if (is_array($sub) && !array_key_exists('active_key', $sub)) {
                                $sub['active_key'] = is_numeric($index) ? (array_key_exists('route', $sub) ? preg_replace('/^frontend\./i', '', $sub['route']) : '') : $index;
                            }
                        }
                    }

                    if (is_array($item) && !array_key_exists('active_key', $item)) {
                        $item['active_key'] = is_numeric($key) ? (array_key_exists('route', $item) ? preg_replace('/^frontend\./i', '', $item['route']) : '') : $key;
                    }
                    $menuitems[] = $item;
                }
            }

        $sidebar_menu = [
            'type' => 'list',
            'data' => $menuitems
        ];

        return $sidebar_menu;
    }

}
