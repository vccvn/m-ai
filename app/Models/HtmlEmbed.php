<?php

namespace App\Models;
use Gomee\Models\Model;
class HtmlEmbed extends Model
{
    public $table = 'html_embeds';
    public $fillable = ['area_id', 'label', 'slug', 'code', 'priority', 'status'];

    
    public $timestamps = false;
    
}
