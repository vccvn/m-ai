<?php
namespace App\Masks\Payments;

use Gomee\Masks\MaskCollection;

/**
 * @method PackageMask getItem($attr, $value)
 */
/**
 * @property PackageMask[] $items
 */
class PackageCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return PackageMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
