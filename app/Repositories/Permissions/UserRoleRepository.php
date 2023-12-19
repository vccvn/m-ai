<?php

namespace App\Repositories\Permissions;
use Gomee\Repositories\BaseRepository;

class UserRoleRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = 'App\Validators\Permissions\UserRoleRValidator';

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PermissionUserRole::class;
    }

    /**
     * lấy các role id của một module nào đó
     * @param string $role_uuid
     * @return array
     */
    public function getUserRoleChecked($role_uuid = 0)
    {
        $data = [];
        if($role_uuid && $users = $this->getBy('role_uuid', $role_uuid)){
            foreach ($users as $key => $user) {
                $data[] = $user->role_uuid;
            }
        }
        return $data;
    }

    /**
     * cập nhật danh sách quyển cho module
     * @param string $role_uuid
     * @param array $user_uuid_list
     * @return void
     */
    public function updateUserRole(string $role_uuid, array $user_uuid_list = [])
    {
        $ingore = [];
        if(count($users = $this->getBy('role_uuid', $role_uuid))){
            foreach ($users as $user) {
                // nếu role nằm trong số id them thì bỏ qua
                if(in_array($user->user_uuid, $user_uuid_list)) $ingore[] = $user->user_uuid;
                // nếu ko thì xóa
                else $user->delete();
            }
        }
        if(count($user_uuid_list)){
            foreach ($user_uuid_list as $user_uuid) {
                if(!in_array($user_uuid, $ingore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $this->save(compact('user_uuid', 'role_uuid'));
                }
            }
        }
    }
}
