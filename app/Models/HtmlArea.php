<?php

namespace App\Models;
use Gomee\Models\Model;
class HtmlArea extends Model
{
    public $table = 'html_areas';
    public $fillable = ['name', 'slug', 'ref', 'ref_id'];

    public $timestamps = false;
    
    
    public function embeds()
    {
        return $this->hasMany(HtmlEmbed::class, 'area_id', 'id');
    }

    public function areaComponents()
    {
        return $this->hasMany(HtmlComponent::class, 'area_id', 'id');
    }

    
    public function components()
    {
        return $this->areaComponents()
                    ->join('components', 'components.id', '=', 'html_components.component_id')
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
    
}
