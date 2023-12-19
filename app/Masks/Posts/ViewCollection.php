<?php
namespace App\Masks\Posts;

use Gomee\Masks\MaskCollection;

class ViewCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return ViewMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
