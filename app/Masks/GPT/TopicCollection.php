<?php
namespace App\Masks\GPT;

use Gomee\Masks\MaskCollection;

/**
 * @method TopicMask getItem($attr, $value)
 */
/**
 * @property TopicMask[] $items
 */
class TopicCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return TopicMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
