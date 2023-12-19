<?php

namespace App\Models;
use Gomee\Models\Model;

class Region extends Model
{
    public $table = 'regions';
    public $fillable = ['name', 'slug', 'code', 'position'];

    public $timestamps = false;

    public function districts()
    {
        return $this->hasMany('App\Models\District', 'region_id', 'id');
    }

    public function beforeDelete()
    {
        $this->districts()->delete();
    }
}
