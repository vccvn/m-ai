<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HtmlComponent extends Model
{
    public $table = 'html_components';
    public $fillable = ['component_id', 'area_id', 'parent_id', 'priority', 'data'];


    public $casts = [
        'data' => 'json'
    ];

    /**
     * Get all of the children for the HtmlComponent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id')->join('components', 'components.id', '=', 'html_components.component_id')
        ->select(
            'html_components.*',
            'components.name',
            'components.type',
            'components.ref',
            'components.ref_id',
            'components.path',
            'components.inputs'
        );
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id', 'id');
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id', 'id');
    }

}
