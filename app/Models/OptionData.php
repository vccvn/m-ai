<?php

namespace App\Models;
use Gomee\Models\Model;
class OptionData extends Model
{
    public $table = 'option_datas';
    public $fillable = ['group_id', 'name', 'label', 'type', 'value_type', 'value', 'priority', 'props', 'can_delete'];

    public $timestamps = false;

    public $casts = [
        'props' => 'json'
    ];

    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        if($this->props && is_array($this->props)){
            foreach($this->props as $key => $value){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    public function canDelete()
    {
        return $this->can_delete;
    }
}
