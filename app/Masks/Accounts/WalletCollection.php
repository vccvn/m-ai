<?php
namespace App\Masks\Accounts;

use Gomee\Masks\MaskCollection;

/**
 * @method WalletMask getItem($attr, $value)
 */
/**
 * @property WalletMask[] $items
 */
class WalletCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return WalletMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
