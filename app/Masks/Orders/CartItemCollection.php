<?php
namespace App\Masks\Orders;

use Gomee\Masks\MaskCollection;

class CartItemCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CartItemMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
