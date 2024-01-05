<?php
namespace App\Masks\Accounts;

use Gomee\Masks\MaskCollection;

/**
 * @method AgentMask getItem($attr, $value)
 */
/**
 * @property AgentMask[] $items
 */
class AgentCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return AgentMask::class;
    }
    // xem Collection mẫu ExampleCollection
}
