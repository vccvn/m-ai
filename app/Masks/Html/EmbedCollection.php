<?php
namespace App\Masks\Html;

use Gomee\Masks\MaskCollection;

class EmbedCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return EmbedMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
