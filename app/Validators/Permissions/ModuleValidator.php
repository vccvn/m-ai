<?php

namespace App\Validators\Permissions;

use Gomee\Validators\Validator as BaseValidator;

use App\Repositories\Permissions\RouteRepository;

use App\Repositories\Permissions\RoleRepository;

class ModuleValidator extends BaseValidator
{
    protected $routes = null;
    public function checkRef2()
    {
        return in_array(strtolower($this->type), ['uri', 'name', 'prefix']);
    }
    public function extends()
    {
        $this->routes = new RouteRepository();


        $this->addRule('check_roles', function($attr, $value){
            if(!$value) return true;
            if(is_array($value)){
                if(count($value)){
                    $roleRep = app(RoleRepository::class);
                    return $roleRep->count(['id'=>$value]) == count($value);
                }
                return true;
            }
            return false;;
        });

    }
    /**
     * ham lay rang buoc du lieu
     */
    public function rules()
    {
        $rules = [
            'name'                => 'required|max:191',

            'roles'               => 'check_roles',
            'description'         => 'max:2000',
        ];


        return $this->parseRules($rules);
    }

    public function messages()
    {
        return [
            'name.max'                             => 'Tên vượt quá số ký tự',

            'name.required'                        => 'Bạn chưa nhập tên module',

            'type.module_type'                     => 'Loại module không hợp lệ',

            'parent_id.parent_id'                  => 'Module cha không hợp lệ',

            'roles.check_roles'                    => 'Danh sách quyền không hợp lệ',

            'ref.module_ref'                       => 'Mục tham chiếu không hợp lệ'
        ];
    }
}
