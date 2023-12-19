<?php

namespace App\Models;
use Gomee\Models\Model;

class PermissionModuleRole extends Model
{
    public $table = 'permission_module_roles';

    public $fillable = ['module_id', 'role_id'];



    public function role()
    {
        return $this->belongsTo(PermissionRole::class,'role_id','id');
    }

    public function module()
    {
        return $this->belongsTo(PermissionModule::class,'module_id','id');
    }

}
