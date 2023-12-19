<?php
namespace App\Masks\Products;

use Gomee\Masks\MaskCollection;

class AttributeCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return AttributeMask::class;
    }
    // xem Collection mẫu ExampleCollection

    public function setITems($items = [])
    {
        $this->total = count($items);
        $this->items = $items;
    }
}
