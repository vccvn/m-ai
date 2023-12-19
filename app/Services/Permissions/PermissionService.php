<?php

namespace App\Services\Permissions;

use App\Engines\ModuleManager;
use App\Models\PermissionModule;
use App\Models\PermissionRole;
use App\Repositories\Permissions\ModuleGroupActionRepository;
use App\Repositories\Permissions\ModuleRepository;
use App\Repositories\Permissions\ModuleRoleRepository;
use App\Repositories\Permissions\RoleRepository;
use App\Repositories\Permissions\UserRoleRepository;
use App\Services\Service;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

class PermissionService extends Service
{
    protected $module = 'permissions';

    protected $moduleName = 'Permission';

    protected $flashMode = true;

    protected static $matrixMap = [
        'paths' => [],
        'prefix' => []
    ];

    protected static $firstCheck = false;

    public static function checkPermiss($type, $module, $roles = [])
    {

        if(!array_key_exists($type, static::$matrixMap) || !array_key_exists($module, static::$matrixMap[$type])) return false;
        $moduleRoles = static::$matrixMap[$type][$module];
        if(!$moduleRoles) return false;
        return count(array_filter($moduleRoles, function($role_id) use ($roles){
            return in_array($role_id, $roles);
        })) > 0;
    }

    public static function checkPermissByPrefix($prefix, $roles = [])
    {
        return static::checkPermiss('prefix', $prefix, $roles);
    }

    public static function checkPermissByPath($path, $roles = [])
    {
        return static::checkPermiss('paths', $path, $roles);
    }


    public static function checkModulePermission($path, $roles = [])
    {
        return static::checkPermiss('paths', $path, $roles);
    }


    /**
     * kiểm tra user có quyền truy cập route hay ko
     *
     * @param User $user
     * @param string $path
     * @return boolean
     */
    public static function checkUserPermiss($user, $path)
    {
        if(in_array($user->type, ['admin', 'manager'])) return true;
        $userRoles = $user->getUserRoles();
        return static::checkModulePermission($path, $userRoles);
    }

    public static function getModuleRoles($type = 'paths', $module = null){
        if(!array_key_exists($type, static::$matrixMap) || !array_key_exists($module, static::$matrixMap[$type])) return [];
        $moduleRoles = static::$matrixMap[$type][$module];
        return $moduleRoles;
    }
    /**
     * repository chinh
     *
     * @var ModuleRepository
     */
    public $repository;

    /**
     * module group repository
     *
     * @var ModuleGroupActionRepository
     */
    public $moduleGroupActionRepository;

    /**
     * Role Repo
     *
     * @var RoleRepository
     */
    public $roleRepository;


    /**
     * Role Repo
     *
     * @var UserRoleRepository
     */
    public $userRoleRRepository;


    /**
     * module role
     *
     * @var ModuleRoleRepository
     */
    public $moduleRoleRepository;

    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct(
        ModuleRepository $repository,
        ModuleGroupActionRepository $moduleGroupActionRepository,
        ModuleRoleRepository $moduleRoleRepository,
        RoleRepository $RoleRepository,
        UserRoleRepository $UserRoleRepository
    )
    {
        $this->repository = $repository;
        $this->moduleGroupActionRepository = $moduleGroupActionRepository;
        $this->roleRepository = $RoleRepository;
        $this->userRoleRRepository = $UserRoleRepository;
        $this->moduleRoleRepository = $moduleRoleRepository;
        $this->init();
    }

    public function showMatrix()
    {
        return ModuleManager::getContainers();
    }

    public function updateModuleRouteMatrix()
    {
        $containers = ModuleManager::getContainers();
        $list = [];
        if(request()->reset_routes){
            $this->repository->query()->delete();
        }
        foreach ($containers as $slug => $value) {
            $list[$slug] = $value->toArray();
        }
        return $this->updateModuleList($list);
    }

    public function updateModuleList($routes, $conditions = [])
    {
        if (!count($routes)) return false;
        $parent_id = array_key_exists('parent_id', $conditions) && $conditions['parent_id'] ? $conditions['parent_id'] : 0;
        $ignore = [];
        foreach ($routes as $slug => $module) {
            $ignore[] = $this->updateModule($slug, $module, $conditions)->id;
        }
        if($parent_id){
            $list = $this->repository->get($conditions);
            if(count($list)){
                foreach ($list as $item) {
                    if(!in_array($item->id, $ignore)) $item->delete();
                }
            }
        }
        return $ignore;
    }

    public function addActionModules($actions, $group_id)
    {
        $groupParams = ['group_id' => $group_id];
        foreach ($actions as $slug => $module) {
            $groupParams['action_id'] = $this->updateModule($slug, $module)->id;
            if(!$this->moduleGroupActionRepository->count($groupParams)){
                $this->moduleGroupActionRepository->create($groupParams);
            }
        }


    }
    /**
     * update module
     *
     * @param string $slug
     * @param array $module
     * @param array $conData
     * @return PermissionModule
     */
    public function updateModule($slug, $module, $conData = [])
    {
        $s = array_key_exists('slug', $module) && $module['slug'] ? $module['slug'] : $slug;
        $type = $module['type'] ?? 'default';
        $cond = array_merge($conData, ['slug' => $s, 'type' => $type]);
        switch ($type) {
            case PermissionModule::TYPE_SCOPE:
                //
                $cond['prefix'] = $module['prefix']??'';
                $cond['path'] = $module['path']??'';
                break;
            case PermissionModule::TYPE_MODULE:
                $cond['prefix'] = $module['prefix']??'';
                $cond['path'] = $module['path']??'';
                if (array_key_exists('route', $module) && $module['route']) $cond['route'] = $module['route'];
                break;

            case PermissionModule::TYPE_GROUP:
                break;
            case PermissionModule::TYPE_PREFIX:
                $cond['prefix'] = $module['prefix']??'';
                break;


            case PermissionModule::TYPE_ACTION:
            case PermissionModule::TYPE_ROUTE:
                $cond['route'] = $module['route'];
                break;

            default:
                # code...
                break;
        }
        $isUpdate = true;
        $moduleData = array_merge($module, $cond);
        if (!($mod = $this->repository->first($cond))) {
            $mod = $this->repository->create($moduleData);
            $isUpdate = false;
        }
        if (!$mod) return false;
        if($isUpdate){
            $this->repository->update($mod->is, $moduleData);
        }
        if (in_array($type, [PermissionModule::TYPE_ACTION, PermissionModule::TYPE_ROUTE])) return $mod;
        $cdata = [];
        switch ($type) {
            case PermissionModule::TYPE_SCOPE:
                $this->updateModuleList($module['modules'], ['parent_id' => $mod->id]);
                break;
            case PermissionModule::TYPE_MODULE:
                if (array_key_exists('groups', $module) && $module['groups']) $this->updateModuleList($module['groups'], ['parent_id' => $mod->id, 'type' => PermissionModule::TYPE_GROUP]);
                if (array_key_exists('subs', $module) && $module['subs']) $this->updateModuleList($module['subs'], ['parent_id' => $mod->id, 'type' => PermissionModule::TYPE_MODULE]);
                break;

            case PermissionModule::TYPE_GROUP:
                if (array_key_exists('actions', $module) && $module['actions']) $this->addActionModules($module['actions'], $mod->id);
                break;

            default:
                if (array_key_exists('modules', $module) && $module['modules']) $this->updateModuleList($module['modules'], ['parent_id' => $mod->id, 'type' => PermissionModule::TYPE_MODULE]);
                if (array_key_exists('subs', $module) && $module['subs']) $this->updateModuleList($module['subs'], ['parent_id' => $mod->id, 'type' => PermissionModule::TYPE_MODULE]);
                if (array_key_exists('groups', $module) && $module['groups']) $this->updateModuleList($module['groups'], ['parent_id' => $mod->id, 'type' => PermissionModule::TYPE_GROUP]);
                // if (array_key_exists('children', $module) && $module['children']) $this->updateModuleList($module['children'], ['parent_id' => $mod->id, 'type' => PermissionModule::TYPE_GROUP]);

                break;
        }
        return $mod;
    }

    public function updateModuleRoleMatrix($role_id, $modelMatrix = [])
    {
        $hasChange = false;
        foreach ($modelMatrix as $module_id => $data) {
            if(array_key_exists('change', $data) && $data['change']){
                $ignore = [];
                $addActions = [];
                $removeActions = [];
                $addList = [];
                $removeList = [];
                if(array_key_exists('groups', $data) && $data['groups']){
                    $ignore = $data['groups'];
                }
                if($ignore && $needAddRole = $this->repository->with('actions')->get(['type' => PermissionModule::TYPE_GROUP, 'parent_id' => $module_id, 'id' => $ignore])){
                    foreach ($needAddRole as $group) {
                        if(in_array($group->id, $ignore)){
                            $addList[] = $group->id;
                            if($group->actions && count($group->actions)){
                                foreach ($group->actions as $action) {
                                    if(!in_array($action->id, $addActions)){
                                        $addActions[] = $action->id;
                                    }
                                }
                            }
                        }
                    }
                }
                if(count($groups = $this->repository->whereNotIn('id', $ignore)->with('actions')->get(['type' => PermissionModule::TYPE_GROUP, 'parent_id' => $module_id]))){
                    foreach ($groups as $group) {
                        if(!in_array($group->id, $ignore)){
                            $removeList[] = $group->id;
                            if($group->actions && count($group->actions)){
                                foreach ($group->actions as $action) {
                                    if(!in_array($action->id, $addActions) && !in_array($action->id, $removeActions)){
                                        $removeActions[] = $action->id;
                                    }
                                }
                            }
                        }
                    }
                }

                if($addActions && $this->moduleRoleRepository->addRoleToModules($role_id, $addActions)) $hasChange = true;
                if($addList && $this->moduleRoleRepository->addRoleToModules($role_id, $addList)) $hasChange = true;
                if($removeActions && $this->moduleRoleRepository->removeRoleFromModules($role_id, $removeActions)) $hasChange = true;
                if($removeList && $this->moduleRoleRepository->removeRoleFromModules($role_id, $removeList)) $hasChange = true;
                if($ignore) $this->moduleRoleRepository->addRoleToModules($role_id, [$module_id]);
                else $this->moduleRoleRepository->removeRoleFromModules($role_id, [$module_id]);
            }
        }
        if($hasChange){
            $list = $this->repository->getModuleMatrix();
            $arr = [];
            if(count($list)){
                foreach ($list as $key => $module) {
                    $arr[] = $module->updateRoleByChildren();
                }
            }
            return $arr;
        }
        return [];
    }

    protected function addModuleRoles($type, $module, $roles = [])
    {
        if(!array_key_exists($type, static::$matrixMap)){
            static::$matrixMap[$type] = [];
        }
        static::$matrixMap[$type][$module] = $roles;
    }

    public function setupModuleRoleMatrix()
    {
        $this->repository->whereNotNull('path')->with('moduleRoles')->select('id', 'type', 'prefix', 'route', 'path')->chunkById(50, function($modules){
            if(count($modules)){
                foreach ($modules as $module) {
                    $roles = [];
                    if($module->moduleRoles && count($module->moduleRoles)){
                        foreach ($module->moduleRoles as $mr) {
                            $roles[] = $mr->role_id;
                        }
                    }
                    if($module->prefix){
                        $this->addModuleRoles('prefix', $module->prefix, $roles);
                    }

                    if($module->path){
                        $this->addModuleRoles('paths', $module->path, $roles);
                    }


                }
            }
        });
    }
}
