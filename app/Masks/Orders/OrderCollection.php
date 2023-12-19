<?php
namespace App\Masks\Orders;

use Gomee\Masks\MaskCollection;

class OrderCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return OrderMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
