<?php
namespace App\Masks\Payments;

use Gomee\Masks\MaskCollection;

/**
 * @method PaymentMask getItem($attr, $value)
 */
/**
 * @property PaymentMask[] $items
 */
class PaymentCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return PaymentMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
