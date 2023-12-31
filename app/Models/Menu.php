<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * Menu class
 *
 * @property string $name Name
 * @property string $slug Slug
 * @property string $type Type
 * @property integer $ref_id Ref Id
 * @property integer $priority Priority
 * @property boolean $is_main Is Main
 * @property string $positions Positions
 * @property integer $depth Depth
 * @property integer $trashed_status Trashed Status
 */
class Menu extends Model
{
    public $table = 'menus';
    public $fillable = ['name', 'slug', 'type', 'ref_id', 'priority', 'is_main', 'positions', 'depth', 'trashed_status'];


    public function menuItems()
    {
        return $this->hasMany('App\Models\MenuItem', 'menu_id', 'id');
    }

    public function items()
    {
        return $this->menuItems()->where('parent_id', 0)->orderBy('priority', 'ASC');
    }

    public function getPositions()
    {
        $a = [];
        if($this->positions){
            $a = array_filter(array_map('trim', explode(',', $this->positions)), function ($pos)
            {
                return strlen($pos) > 0;
            });
        }
        return $a;
    }

    public function getPositionText()
    {
        $pos = get_menu_positions();
        $p = $this->getPositions();
        $a = [];
        foreach ($p as $b) {
            if(isset($pos[$b])) $a[] = $pos[$b];
        }
        return implode(', ', $a);
    }

    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        $data['positions'] = $this->getPositions();
        return $data;
    }

    public function beforeDelete()
    {
        $this->menuItems()->delete();
    }

}
