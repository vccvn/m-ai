<?php
namespace App\Masks\Payments;

use Gomee\Masks\MaskCollection;

/**
 * @method TransactionMask getItem($attr, $value)
 */
/**
 * @property TransactionMask[] $items
 */
class TransactionCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return TransactionMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
