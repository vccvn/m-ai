<?php
namespace App\Masks\Html;

use Gomee\Masks\MaskCollection;

class AreaCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return AreaMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
