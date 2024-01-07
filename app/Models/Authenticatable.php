<?php

namespace App\Models;

use App\Services\Encryptions\HashService;
use Gomee\Models\CommonMethods;
use Gomee\Models\ModelEventMethods;
use Gomee\Models\ModelFileMethods;
use Gomee\Models\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

/**
 * user class
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $birthday
 * @property string $username
 * @property mixed $email
 * @property mixed $password
 * @property mixed $phone_number
 * @property mixed $ci_card_number
 * @property mixed $ci_card_front_scan
 * @property mixed $ci_card_back_scan
 * @property boolean $ci_status
 * @property mixed $type
 * @property mixed $level
 * @property mixed $avatar
 * @property mixed $status
 * @property mixed $secret_id
 * @property mixed $google2fa_secret
 * @property mixed $trashed_status
 * @property mixed $address
 * @property mixed $region_id
 * @property mixed $district_id
 * @property mixed $ward_id
 * @property mixed $school_id
 * @property int $test_turns
 */
class Authenticatable extends AuthUser
{
    use HasApiTokens, HasFactory, Notifiable, ModelEventMethods, ModelFileMethods, CommonMethods,
        Uuid
    ;

        /**
     * lấy ra danh sách role
     */
    public function roles()
    {
        return $this->userRole()
            ->join('permission_roles', 'permission_roles.id', '=', 'permission_user_roles.role_id')
            ->select('permission_roles.id', 'permission_roles.name', 'permission_roles.level', 'permission_roles.description')
            ->orderBy('permission_roles.level', 'DESC');
    }

    public function roleLevels()
    {
        if ($this->role_levels) return $this->role_levels;
        $data = ['admin' => [], 'mod' => [], 'access' => [], 'list' => [], 'roles' => []];
        $level = [3 => 'admin', 2 => 'mod', 1 => 'access'];
        if (count($this->roles)) {
            foreach ($this->roles as $role) {
                $data[$level[$role->level]][] = $role->id;
                $data['list'][] = $role->id;
                $data['roles'][$role->id] = $role;
            }
        }
        $this->role_levels = $data;
        return $data;
    }

    public function inGroup($level = 'mod')
    {
        if (!in_array($lv = strtolower($level), ['admin', 'mod'])) return false;
        $data = $this->roleLevels();
        return count($data[$lv]);
    }

    public function hasRoles($roles = [])
    {
        if (!is_array($roles) || !count($roles)) return false;
        $data = $this->roleLevels();
        foreach ($roles as $role_id) {
            if (!in_array($role_id, $data['list'])) return false;
        }
        return true;
    }

    public function hasOnly($roles = [])
    {
        if (!is_array($roles) || !count($roles)) return false;
        $data = $this->roleLevels();
        if (count($roles) != count($data['list'])) return false;
        foreach ($roles as $role_id) {
            if (!in_array($role_id, $data['list'])) return false;
        }
        return true;
    }


    /**
     * ket noi voi bang metadata
     * @return queryBuilder
     */
    public function metadatas()
    {
        return $this->hasMany(Metadata::class, 'ref_id', 'id')->where('ref', 'user');
    }


    public function userRole()
    {
        return $this->hasMany(PermissionUserRole::class, 'user_id', 'id');
    }

    public function getUserRoles()
    {
        if ($this->isCheckRoles) return $this->_roles;
        $data = [];
        if ($this->userRole && count($this->userRole)) {
            foreach ($this->userRole as $r) {
                $data[] = $r->role_id;
            }
        }
        $this->isCheckRoles = true;
        $this->_roles = $data;
        return $this->_roles;
    }

    public function getRoleIdListAttribute()
    {
        return $this->getUserRoles();
    }


    public function __get_table()
    {
        return 'users';
    }

}
