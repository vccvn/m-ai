<?php

namespace App\Models;

use Gomee\Models\Model;

class TagRef extends Model
{
    public $table = 'tag_refs';
    public $fillable = ['tag_id', 'ref', 'ref_id'];



    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        return $data;
    }

}
