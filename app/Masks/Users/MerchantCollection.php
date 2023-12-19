<?php
namespace App\Masks\Users;

use Gomee\Masks\MaskCollection;

/**
 * @method MerchantMask getItem($attr, $value)
 */
/**
 * @property MerchantMask[] $items
 */
class MerchantCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return MerchantMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
