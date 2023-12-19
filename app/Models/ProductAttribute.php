<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * ProductAttribute class
 *
 * @property integer $product_id Product Id
 * @property integer $attribute_value_id Attribute Value Id
 * @property string $advance_value Advance Value
 * @property string $thumbnail Thumbnail
 * @property float $price Price
 * @property integer $is_default Is Default
 */
class ProductAttribute extends Model
{
    public $table = 'product_attributes';
    public $fillable = ['product_id', 'attribute_value_id', 'advance_value', 'thumbnail', 'price', 'is_default'];

    public $timestamps = false;


    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id', 'id');
    }

    /**
     * lấy thuộc tính
     *
     */
    public function attribute()
    {
        return $this->attributeValue->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    /**
     * lấy sản phẩm
     *
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function beforeDelete()
    {
        if($this->attribute->advance_value_type == 'image' && $this->Advance_value && file_exists($p = asset(get_content_path('products/variants/'.$this->advance_value)))){
            unlink($p);
        }
    }


}
