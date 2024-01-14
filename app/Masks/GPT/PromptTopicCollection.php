<?php
namespace App\Masks\GPT;

use Gomee\Masks\MaskCollection;

/**
 * @method PromptTopicMask getItem($attr, $value)
 */
/**
 * @property PromptTopicMask[] $items
 */
class PromptTopicCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return PromptTopicMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
