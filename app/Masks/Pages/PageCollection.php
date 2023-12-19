<?php
namespace App\Masks\Pages;

use Gomee\Masks\MaskCollection;

class PageCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return PageMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
