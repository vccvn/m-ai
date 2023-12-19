<?php

namespace App\Validators\Permissions;

use App\Repositories\Users\UserRepository;
use Gomee\Validators\Validator as BaseValidator;
use ReflectionClass;

class RoleValidator extends BaseValidator
{
    public function extends()
    {
        // kiểm tra role level
        $this->addRule('role_level', function($attr, $value){
            if(!strlen($value)) return true;
            // nếu level = 1 | 2 | 3
            if(in_array($value, [1, 2, 3])){
                $params = ['level'=>$value];
                // nếu level = 3
                if($value == 3){
                    // nếu có role
                    if($role = $this->repository->first($params)){
                        // nếu update và id update = id role level 3 thì chấp nhận
                        if($this->id && $this->id == $role->id && $role->level == $value) return true;
                        return false;
                    }
                }
                // nếu level = 2
                elseif($value == 2){
                    // có ít hơn 2 role có level 2 thì được chấp nhận
                    if($this->repository->count($params) < 2) return true;
                    // nếu có nhiều hơn một role có level 2 thì kiểm tra trường hợp update
                    elseif($this->id && $r = $this->find($this->id)){
                        // nếu tìm dc role có có id như đã cho mà level cũ của nó khác 2 thì sai.
                        // vì level 2 chỉ dc phép tối đa 2 role
                        // mà level cũ của nó khác 2 nghĩa là role này ko nằm trong nhóm 2 role kia :v
                        if($r->level != $value) {
                            return false;
                        }
                        return true;
                    }
                    // còn lại hiển nhiên sai
                    return false;
                }
                // luôn luôn đúng
                return true;
            } // level khác 1 | 2 | 3 sai
            return false;

        });

        $this->addRule('check_users', function($attr, $value){
            if(!$value) return true;
            if(!is_array($value)) return false;
            if(!count($value)) return true;
            $staffs = new UserRepository();
            return $staffs->count(['id' => $value]) == count($value);

        });
        $this->addRule('check_modules', function($attr, $value){
            if(!$value) return true;
            if(!is_array($value)) return false;
            return true;

        });
    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        $rules = [
            'name'                => 'required|max:191|unique_prop',
            'handle'              => 'role_handle',
            'description'         => 'max:2000',
            'users'               => 'check_users',
            'modules'             => 'check_modules'
        ];
        return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'name.max'                             => 'Tên vượt quá số ký tự',

            'name.required'                        => 'Bạn chưa nhập tên quyền',

            'level.role_level'                     => 'level không hợp lệ',

            'handle.role_handle'                   => 'Handle cha không hợp lệ',
        ];
    }
}
