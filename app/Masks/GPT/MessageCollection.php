<?php
namespace App\Masks\GPT;

use Gomee\Masks\MaskCollection;

/**
 * @method MessageMask getItem($attr, $value)
 */
/**
 * @property MessageMask[] $items
 */
class MessageCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return MessageMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
