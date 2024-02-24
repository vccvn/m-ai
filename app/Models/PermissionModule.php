<?php

namespace App\Models;

use Gomee\Laravel\Router;

use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PermissionModule extends Model
{
    const TYPE_DEFAULT = 'default';
    const TYPE_MODULE = 'module';
    const TYPE_GROUP = 'group';
    const TYPE_ACTION = 'action';
    const TYPE_ROUTE = 'route';
    const TYPE_PREFIX = 'prefix';
    const TYPE_REFS = 'refs';
    const TYPE_SCOPE = 'scope';
    const ALL_TYPE = [self::TYPE_DEFAULT, self::TYPE_ACTION, self::TYPE_GROUP, self::TYPE_MODULE, self::TYPE_PREFIX, self::TYPE_ROUTE, self::TYPE_REFS, self::TYPE_SCOPE];

    const GROUP_NAMES = [
        'view'      => 'Xem',
        'create'    => 'Thêm',
        'update'    => 'Chỉnh sửa',
        'delete'    => 'Xóa',
        'restore'   => 'Khôi phục',
        'config'    => 'Cấu hình form',
        'refs'      => 'Liên kết',
        'extra'     => 'Mở rộng'
    ];

    public $table = 'permission_modules';
    public $fillable = ['name', 'ref', 'type', 'slug', 'route', 'prefix', 'parent_id', 'description', 'path'];

    protected $actionList = [];


    protected $groupMap = [
        'view'       => null,
        'create'     => null,
        'update'     => null,
        'delete'     => null,
        'restore'    => null,
        'config'     => null,
        'refs'       => null,
        'extra'      => null,
    ];

    protected $groupMapChecked = false;

    protected $__roles__ = [];
    protected $roleIDChecked = false;




    public function moduleRoles()
    {
        return $this->hasMany(PermissionModuleRole::class, 'module_id', 'id');
    }

    /**
     * Get all of the actionGroups for the PermissionModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id')->where('type', self::TYPE_GROUP);
    }

    /**
     * Get all of the actionGroups for the PermissionModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actionGroups(): HasMany
    {
        return $this->hasMany(PermissionModuleGroupAction::class, 'group_id', 'id');
    }

    /**
     * The actions that belong to the PermissionModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'permission_module_group_actions', 'group_id', 'action_id');
    }

    /**
     * The group that belong to the PermissionModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function group(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'permission_module_group_actions', 'action_id', 'group_id');
    }

    /**
     * The roles that belong to the PermissionModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(PermissionRole::class, 'permission_module_roles', 'module_id', 'role_id');
    }


    /**
     * Get all of the modules for the PermissionModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modules(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id')->where('type', self::TYPE_MODULE);
    }



    public function updateRoleList($roles = [])
    {
        $ignore = [];
        if($this->moduleRoles && count($this->moduleRoles)){
            foreach ($this->moduleRoles as $mr) {
                if(!in_array($mr->role_id, $roles)) $mr->delete;
                else $ignore[] = $mr->role_id;
            }
        }

        if($roles){
            $module_id = $this->id;
            foreach ($roles as $role_id) {
                if(!in_array($role_id, $ignore)){
                    $data = compact('module_id', 'role_id');
                    $modRole = new PermissionModuleRole($data);
                    if($modRole->save()){
                        $ignore[] = $role_id;
                    }
                }
            }
        }
        return $ignore;

    }


    public function updateRoleByChildren()
    {
        $roles = [];
        if($this->type == self::TYPE_ACTION || $this->type == self::TYPE_ROUTE){
            if($this->moduleRoles && count($this->moduleRoles)){
                foreach ($this->moduleRoles as $role) {
                    $roles[] = $role->role_id;
                }
            }
        }elseif($this->type == self::TYPE_GROUP){
            if($this->actions && count($this->actions)){
                foreach ($this->actions as $key => $action) {
                    if($action->moduleRoles && count($action->moduleRoles)){
                        foreach($action->moduleRoles as $r){
                            if(!in_array($r->role_id, $roles)){
                                $roles[] = $r->role_id;
                            }
                        }
                    }
                }
            }
            // $this->updateRoleList($roles);
        }
        else{
            if($this->groups && count($this->groups)){
                foreach ($this->groups as $group) {
                    $groupRoles = $group->updateRoleByChildren();
                    if($groupRoles && count($groupRoles)){
                        foreach($groupRoles as $role_id){
                            if(!in_array($role_id, $roles)){
                                $roles[] = $role_id;
                            }
                        }
                    }
                }
            }
            if($this->modules && count($this->modules)){
                foreach ($this->modules as $group) {
                    $groupRoles = $group->updateRoleByChildren();
                    if($groupRoles && count($groupRoles)){
                        foreach($groupRoles as $role_id){
                            if(!in_array($role_id, $roles)){
                                $roles[] = $role_id;
                            }
                        }
                    }
                }
            }
            $this->updateRoleList($roles);

        }
        return $roles;
    }





    public function checkGroupMap()
    {
        if($this->groupMapChecked) return true;
        $this->groupMapChecked = true;
        if($this->groups && count($this->groups)){
            foreach ($this->groups as $group) {
                $this->groupMap[$group->slug] = $group;
            }
        }
    }


    public function getModuleActionGroupsAttribute()
    {
        $this->checkGroupMap();
        return $this->groupMap;
    }

    public function getRoleIdListAttribute()
    {
        if($this->roleIDChecked) return $this->__roles__;
        $this->roleIDChecked = true;
        if($this->roles && count($this->roles)){
            foreach ($this->roles as $key => $role) {
                $this->__roles__[] = $role->id;
            }
        }
        return $this->__roles__;
    }




    // older code. xóa / tối ưu sau


    public function getParent()
    {
        if ($this->parent_id > 0) {
            return $this->getParentByID();
        }
        return $this->getParentByPrefix();
    }

    public function getParentByID()
    {
        return self::find($this->parent_id);
    }

    public function getParentByPrefix()
    {
        if ($this->type != 'default') {
            $r = null;
            if ($this->type == 'name' && $route = Router::getByName($this->ref)) {
                $r = $route;
            } elseif ($this->type == 'uri' && $route = Router::getByUri($this->ref)) {
                $r = $route;
            }

            if ($r) {
                if ($r['prefix']) {
                    return self::where('type', 'prefix')->where('ref', $r['prefix'])->first();
                }
            }
        }
        return null;
    }

    public function getChildren()
    {
        if ($this->type == 'default') {
            return self::where('parent_id', $this->id)->get();
        }
        return null;
    }


    /**
     * lấy ra danh sách role
     */
    public function roleList()
    {
        return $this->moduleRoles()
            ->join('permission_roles', 'permission_roles.id', '=', 'permission_module_roles.role_id')
            ->select('permission_module_roles.role_id', 'permission_module_roles.module_id','permission_roles.id', 'permission_roles.name', 'permission_roles.level', 'permission_roles.description')
            ->orderBy('permission_roles.level', 'DESC');
    }

    public function roleLevels()
    {
        $data = ['admin' => [], 'mod' => [], 'access' => [], 'list' => [], 'roles' => []];
        $level = [3 => 'admin', 2 => 'mod', 1 => 'access'];
        if (count($this->roleList)) {
            foreach ($this->roleList as $role) {
                $data[$level[$role->level]][] = $role->id;
                $data['list'][] = $role->id;
                $data['roles'][$role->id] = $role;
            }
        }
        return $data;
    }

    public function beforeDelete()
    {
        $this->moduleRoles()->delete();

        if ($this->modules && count($this->modules)) {
            foreach ($this->modules as $key => $sub) {
                $sub->delete();
            }
        }
        if ($this->groups && count($this->groups)) {
            foreach ($this->groups as $key => $sub) {
                $sub->delete();
            }
        }
        if ($this->actions && count($this->actions)) {
            foreach ($this->actions as $key => $sub) {
                $sub->delete();
            }
        }
        if ($this->actionGroups && count($this->actionGroups)) {
            foreach ($this->actionGroups as $key => $sub) {
                $sub->delete();
            }
        }
    }
}
