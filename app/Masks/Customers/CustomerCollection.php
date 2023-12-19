<?php
namespace App\Masks\Customers;

use Gomee\Masks\MaskCollection;

class CustomerCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CustomerMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
