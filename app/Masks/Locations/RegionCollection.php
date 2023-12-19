<?php
namespace App\Masks\Locations;

use Gomee\Masks\MaskCollection;

class RegionCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return RegionMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
