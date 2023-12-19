<?php
namespace App\Masks\Themes;

use Gomee\Masks\MaskCollection;

class ThemeCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return ThemeMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
