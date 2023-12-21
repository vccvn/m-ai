<?php
namespace App\Masks\GPT;

use Gomee\Masks\MaskCollection;

/**
 * @method ChatMask getItem($attr, $value)
 */
/**
 * @property ChatMask[] $items
 */
class ChatCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return ChatMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
