<?php
namespace App\Masks\Orders;

use Gomee\Masks\MaskCollection;

class OrderItemCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return OrderItemMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
