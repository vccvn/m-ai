<?php
namespace App\Masks\Accounts;

use Gomee\Masks\MaskCollection;

/**
 * @method CoverLetterMask getItem($attr, $value)
 */
/**
 * @property CoverLetterMask[] $items
 */
class CoverLetterCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CoverLetterMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
