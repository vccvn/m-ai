<?php
namespace App\Masks\Html;

use Gomee\Masks\MaskCollection;

class ComponentCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return ComponentMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
