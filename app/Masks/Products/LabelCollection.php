<?php
namespace App\Masks\Products;

use Gomee\Masks\MaskCollection;

class LabelCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return LabelMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
