<?php
namespace App\Masks\Orders;

use Gomee\Masks\MaskCollection;

class ItemAttributeCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return ItemAttributeMask::class;
    }
    
}
