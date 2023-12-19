<?php

namespace App\Models;
use Gomee\Models\Model;
class Warehouse extends Model
{
    public $table = 'warehouses';
    public $fillable = ['product_id', 'staff_id', 'type', 'total', 'note'];

    
    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        $data['total'] = abs($this->total);
        return $data;
    }
    
}
