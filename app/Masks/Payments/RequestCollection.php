<?php
namespace App\Masks\Payments;

use Gomee\Masks\MaskCollection;

/**
 * @method RequestMask getItem($attr, $value)
 */
/**
 * @property RequestMask[] $items
 */
class RequestCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return RequestMask::class;
    }
    public function onLoaded()
    {
        $index = 0;
        foreach ($this->items as $key => $pr) {
            $pr->index = ++$index;
        }
    }
    // xem Collection mẫu ExampleCollection
}
