<?php
namespace App\Masks\Policies;

use Gomee\Masks\MaskCollection;

/**
 * @method CommissionMask getItem($attr, $value)
 */
/**
 * @property CommissionMask[] $items
 */
class CommissionCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CommissionMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
