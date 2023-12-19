<?php

namespace App\Models;
use Gomee\Models\Model;
class PermissionUserRole extends Model
{
    public $table = 'permission_user_roles';

    public $fillable = ['user_id', 'role_id'];



    public function role()
    {
        return $this->belongsTo('App\Models\PermissionRole','role_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\PermissionUser','user_id','id');
    }

}
