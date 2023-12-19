<?php

namespace App\Models;
use Gomee\Models\Model;
class Component extends Model
{
    public $table = 'components';
    public $fillable = ['type', 'ref', 'ref_id', 'name', 'path', 'inputs', 'data'];

    
    public $casts = [
        'inputs' => 'json',
        'data' => 'json'
    ];

    public function htmlComponents()
    {
        return $this->hasMany(HtmlComponent::class, 'component_id', 'id');
    }

    public function beforeDelete()
    {
        $this->deleteList('htmlComponents');
    }
    
}
