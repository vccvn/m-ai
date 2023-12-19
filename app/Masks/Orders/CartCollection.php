<?php
namespace App\Masks\Orders;

use Gomee\Masks\MaskCollection;

class CartCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CartMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
