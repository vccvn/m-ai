<?php
namespace App\Masks\Promos;

use Gomee\Masks\MaskCollection;

class PromoCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return PromoMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
