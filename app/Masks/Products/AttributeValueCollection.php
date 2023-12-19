<?php
namespace App\Masks\Products;

use Gomee\Masks\MaskCollection;

class AttributeValueCollection extends MaskCollection
{
    /**
     * lấy tên class mask tương ứng
     *
     * @return string
     */
    public function getMask()
    {
        return AttributeValueMask::class;
    }
    // xem Collection mẫu ExampleCollection

    public function onLoaded()
    {
        if($this->parent){
            $this->set([
                'advance_value_type' => $this->parent->advance_value_type,
                'value_type' => $this->parent->value_type
            ], null, true);
        }
    }
}
