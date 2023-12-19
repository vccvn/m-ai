<?php

namespace App\Engines;

use App\Models\User;
use App\Repositories\Dynamics\DynamicRepository;
use App\Repositories\Options\OptionRepository;
use App\Services\Permissions\PermissionService;
use Gomee\Core\System;
use Gomee\Engines\JsonData;

class MenuEngine
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

    public $disableCheck = false;
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


        $itemList = get_web_module_list($filename);
        $menuData = $this->getData($filename);
        $data = $this->checkInclude(
            $this->checkExtends($menuData ?? [], $filename)
        );
        $user = request()->user();
        $roles = [];
        if ($user) {
            $roles = $user->getUserRoles();
        }
        $menuitems = isset($data['dashboard']) && (($user && ($user->type == User::ADMIN || $user->type == User::MANAGER)) || $this->disableCheck || (
            (array_key_exists('permiss', $data['dashboard']) && PermissionService::checkModulePermission($data['dashboard']['permiss'], $roles)) ||
            (array_key_exists('route', $data['dashboard']) && PermissionService::checkModulePermission($data['dashboard']['route'], $roles))
        )) ? [$data['dashboard']] : [];
        $issetPost = false;
        if (count($itemList)) {
            foreach ($data as $key => $item) {
                $keyFunc = substr($key, 0, 1) == '@' ? substr($key, 1) : "";

                if (in_array($key, $itemList) || (is_array($item) && array_key_exists('@parent', $item) && in_array($item['@parent'], $itemList))) {
                    $cond1 = array_key_exists('permiss', $item);
                    $cond2 = array_key_exists('route', $item);
                    if ($user && ($this->disableCheck || ($user->type == User::ADMIN || $user->type == User::MANAGER))) {
                        //pass
                    } elseif ($cond1 && !PermissionService::checkModulePermission($item['permiss'], $roles))
                        continue;
                    elseif ($cond2 && !PermissionService::checkModulePermission($item['route'], $roles))
                        continue;
                    elseif (!$cond1 && !$cond2)
                        continue;

                    if (!array_key_exists('active_key', $item)) $item['active_key'] = $key;
                    if ($key == 'themes' && $theme = get_active_theme()) {

                        if ($this->optionRepository->hasThemeOption($theme->id)) {
                            array_unshift($item['submenu']['data'], [
                                'text' => 'Tùy biến',
                                'title' => 'Tùy biến',
                                'active_key' => 'theme-option',
                                'route' => 'admin.themes.options',
                                'icon' => 'paint-brush'
                            ]);
                        }
                    }

                    if ($key == 'settings') {
                        if ($webType == 'ecommerce' && ( $user->type == User::ADMIN || $user->type == User::MANAGER || PermissionService::checkModulePermission('admin.settings.group.form', $roles))) {
                            $item['submenu']['data'][] = [
                                'text' => 'Trang sản phẩm',
                                'title' => 'Trang sản phẩm',
                                'active_key' => 'settings.products',
                                'route' => 'admin.settings.group.form',
                                'params' => [
                                    'group' => 'products'
                                ],
                                'icon' => 'cubes'
                            ];
                            $item['submenu']['data'][] = [
                                'text' => 'Cửa hàng',
                                'title' => 'Cửa hàng',
                                'active_key' => 'settings.ecommerce',
                                'route' => 'admin.settings.group.form',
                                'params' => [
                                    'group' => 'ecommerce'
                                ],
                                'icon' => 'shopping-cart'
                            ];
                        }
                    }
                    if (array_key_exists('submenu', $item) && array_key_exists('data', $item['submenu'])) {
                        $sData = $item['submenu']['data'];
                        foreach ($sData as $index => $sub) {
                            // dump($sub);
                            if (
                                ($user && ($this->disableCheck || ($user->type == User::ADMIN || $user->type == User::MANAGER))) || (
                                    (array_key_exists('permiss', $sub) && PermissionService::checkModulePermission($sub['permiss'], $roles)) ||
                                    (array_key_exists('route', $sub) && PermissionService::checkModulePermission($sub['route'], $roles))
                                )
                            ) {
                            } else {
                                $item['submenu']['data'][$index] = [];
                                unset($item['submenu']['data'][$index]);
                            }
                        }
                    }

                    $menuitems[] = $item;
                } elseif ($key == 'custom') {
                    if (is_array($item)) {
                        foreach ($item as $custom) {
                            if ($custom == 'posts' && (($user && ($this->disableCheck || ($user->type == User::ADMIN || $user->type == User::MANAGER))) || PermissionService::checkModulePermission('admin.posts', $roles))) {
                                $menuitems = array_merge($menuitems, $this->getPostMenuItems($user, $roles));
                                $issetPost = true;
                            } else {
                            }
                        }
                    } elseif ($item == 'posts' && (($user && ($user->type == User::ADMIN || $user->type == User::MANAGER)) || $this->disableCheck || PermissionService::checkModulePermission('admin.posts', $roles))) {
                        $menuitems = array_merge($menuitems, $this->getPostMenuItems($user, $roles));
                        $issetPost = true;
                    }
                } elseif ($keyFunc == 'package' || $keyFunc == 'packages') {
                    $packageMenus = System::getMenus($item);
                    if ($packageMenus) {
                        foreach ($packageMenus as $key => $value) {
                            if (
                                ($this->disableCheck || ($user && ($user->type == User::ADMIN || $user->type == User::MANAGER))) ||
                                (array_key_exists('permiss', $value) && PermissionService::checkModulePermission($value['permiss'], $roles)) ||
                                (array_key_exists('route', $value) && PermissionService::checkModulePermission($value['route'], $roles))
                            ) {
                                $menuitems[] = $value;
                            }
                        }

                        // dd($newData);
                    }
                }
            }

            if (!$issetPost && (($user && ($user->type == User::ADMIN || $user->type == User::MANAGER)) || $this->disableCheck || PermissionService::checkModulePermission('admin.posts', $roles))) {
                $menuitems = array_merge($menuitems, $this->getPostMenuItems($user, $roles));
            }
        }
        $sidebar_menu = [
            'type' => 'list',
            'data' => $menuitems
        ];

        return $sidebar_menu;
    }

    /**
     * lấy menu post
     *
     * @return array
     */
    public function getPostMenuItems($user, $roles = []): array
    {
        $items = [];




        return $items;
    }
}
