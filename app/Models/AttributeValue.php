<?php

namespace App\Models;
use Gomee\Models\Model;
class AttributeValue extends Model
{
    public $table = 'attribute_values';
    public $fillable = ['attribute_id', 'int_value', 'decimal_value', 'varchar_value', 'text_value', 'advance_value'];

    public $timestamps = false;

    /**
     * lấy thuộc tính
     * 
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }
    
    public function deleteAdvanceValue()
    {
        if($this->attribute->advance_value_type == 'image' && $this->Advance_value && file_exists($p = public_path('static/contents/products/attributes/'.$this->advance_value))){
            unlink($p);
        }
    }

    public function getAdvanceValue()
    {
        # code...
    }

    public function beforeDelete()
    {
        $this->deleteAdvanceValue();
    }
    
}
