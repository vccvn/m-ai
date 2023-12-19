<?php

namespace App\Models;
use Gomee\Models\Model;

class Ward extends Model
{
    public $table = 'wards';
    public $fillable = ['district_id', 'name', 'slug'];

    public $timestamps = false;
}
