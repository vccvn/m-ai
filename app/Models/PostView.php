<?php

namespace App\Models;
use Gomee\Models\Model;
class PostView extends Model
{
    public $table = 'post_views';
    public $fillable = ['post_id', 'view_date', 'view_total'];

    public $timestamps = false;
    
}
