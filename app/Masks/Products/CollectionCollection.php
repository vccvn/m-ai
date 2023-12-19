<?php
namespace App\Masks\Products;

use Gomee\Masks\MaskCollection;

class CollectionCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CollectionMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
