<?php

namespace App\Repositories\Permissions;

use Gomee\Repositories\BaseRepository;

class ModuleRoleRepository extends BaseRepository
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PermissionModuleRole::class;
    }

    /**
     * lấy các role id của một module nào đó
     * @param string $module_uuid
     * @return array
     */
    public function getModuleRoleChecked($module_uuid = 0)
    {
        $data = [];
        if($module_uuid && $roles = $this->getBy('module_uuid', $module_uuid)){
            foreach ($roles as $key => $moduleRole) {
                $data[] = $moduleRole->role_uuid;
            }
        }
        return $data;
    }

    /**
     * cập nhật danh sách quyển cho module
     * @param string $module_uuid
     * @param array $role_uuid_list
     * @return void
     */
    public function updateModuleRoles(string $module_uuid, array $role_uuid_list = [])
    {
        $ingore = [];
        if(count($roles = $this->getBy('module_uuid', $module_uuid))){
            foreach ($roles as $role) {
                // nếu role nằm trong số id them thì bỏ qua
                if(in_array($role->role_uuid, $role_uuid_list)) $ingore[] = $role->role_uuid;
                // nếu ko thì xóa
                else $role->delete();
            }
        }
        if(count($role_uuid_list)){
            foreach ($role_uuid_list as $role_uuid) {
                if(!in_array($role_uuid, $ingore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $this->save(compact('module_uuid', 'role_uuid'));
                }
            }
        }
    }


    /**
     * cập nhật danh sách quyển cho module
     * @param string $role_uuid
     * @param array $module_uuid_list
     * @return void
     */
    public function addRoleToModules(string $role_uuid, array $module_uuid_list = [])
    {
        $ingore = [];
        $res = [];
        if(count($roles = $this->get(['role_uuid' => $role_uuid, 'module_uuid' => $module_uuid_list]))){
            foreach ($roles as $role) {
                // nếu role nằm trong số id them thì bỏ qua
                $ingore[] = $role->module_uuid;
                // $res[] = $role;
            }
        }
        if(count($module_uuid_list)){
            foreach ($module_uuid_list as $module_uuid) {
                if(!in_array($module_uuid, $ingore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $res[] = $this->save(compact('module_uuid', 'role_uuid'));
                }
            }
        }
        return count($res);
    }


    /**
     * cập nhật danh sách quyển cho module
     * @param string $role_uuid
     * @param array $module_uuid_list
     * @return void
     */
    public function removeRoleFromModules(string $role_uuid, array $module_uuid_list = [])
    {
        $ingore = [];
        $res = [];
        if(count($roles = $this->get(['role_uuid' => $role_uuid, 'module_uuid' => $module_uuid_list]))){
            foreach ($roles as $role) {
                // nếu role nằm trong số id them thì bỏ qua
                $res[] = $role;
                $role->delete();
            }
        }
        return count($res);
    }


}
