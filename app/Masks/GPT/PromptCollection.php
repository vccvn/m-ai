<?php
namespace App\Masks\GPT;

use Gomee\Masks\MaskCollection;

/**
 * @method PromptMask getItem($attr, $value)
 */
/**
 * @property PromptMask[] $items
 */
class PromptCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return PromptMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
