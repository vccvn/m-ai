<?php
namespace App\Masks\Verifications;

use Gomee\Masks\MaskCollection;

/**
 * @method VerificationMask getItem($attr, $value)
 */
/**
 * @property VerificationMask[] $items
 */
class VerificationCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return VerificationMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
