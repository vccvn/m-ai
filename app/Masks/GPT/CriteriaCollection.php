<?php
namespace App\Masks\GPT;

use Gomee\Masks\MaskCollection;

/**
 * @method CriteriaMask getItem($attr, $value)
 */
/**
 * @property CriteriaMask[] $items
 */
class CriteriaCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return CriteriaMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
