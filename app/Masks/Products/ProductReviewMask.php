<?php
namespace App\Masks\Products;

use App\Masks\Metadatas\MetadataCollection;
use App\Models\Attribute;
use App\Models\ProductReview;
use Gomee\Masks\Mask;

class ProductReviewMask extends Mask
{

    protected $meta = [];
    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        $this->allow([
            'getAvatar', 'getFeatureImage'
        ]);
        $this->map([
            'metadatas'        => MetadataCollection::class,
            
        ]);
    }

    /**
     * lấy data từ model sang mask
     * @param ProductReview $productReview Tham số không bắt buộc phải khai báo. 
     * Xem thêm ExampleMask
     */
    // public function toMask()
    // {
    //     $data = $this->getAttrData();
    //     // thêm data tại đây.
    //     // Xem thêm ExampleMask
    //     return $data;
        
    // }

    /**
     * sẽ được gọi sau khi thiết lập xong
     *
     * @return void
     */
    protected function onLoaded()
    {
        $this->review_name = $this->name?$this->name:(
            $this->customer_name?$this->customer_name:(
                $this->user_name
            )
        );
        $this->review_email = $this->email?$this->email:(
            $this->customer_email?$this->customer_email:(
                $this->user_email
            )
        );
        
    }
    /**
     * gán dự liệu meta cho product
     * @return void
     */
    public function applyMeta()
    {
        if (!$this->meta) {
            if ($metadatas = $this->relation('metadatas', true)) {
                foreach ($metadatas as $m) {
                    $value = $m->value;
                    $this->data[$m->name] = $value;
                    $this->meta[$m->name] = $value;
                }
            }
        } else {
            foreach ($this->meta as $key => $value) {
                $this->data[$key] = $value;
            }
        }
        
    }

    /**
     * thuộc tính
     */
    public function getAttrs()
    {
        if (!$this->attr_values) return [];
        $attrValues = explode('-', $this->attr_values);
        
        // DB::enableQueryLog();
        $query = Attribute::join('attribute_values', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->join('product_attributes', 'product_attributes.attribute_value_id', '=', 'attribute_values.id')
            ->where('product_attributes.product_id', $this->product_id)
            ->whereIn('attribute_values.id', $attrValues)
            ->orderBy('attributes.is_order_option', 'DESC')
            ->orderBy('attributes.is_variant', 'DESC')
            ->orderBy('attributes.price_type', 'DESC')
            ->select(
                'attributes.id',
                'attributes.name',
                'attributes.label',
                'attributes.value_type',
                'attributes.price_type',
                'attributes.value_unit',
                'attributes.is_variant',
                'attributes.advance_value_type',
                'attribute_values.id AS value_id',
                'attribute_values.varchar_value',
                'attribute_values.int_value',
                'attribute_values.decimal_value',
                'attribute_values.text_value',
                'attribute_values.advance_value as attribute_advance_value',
                'product_attributes.advance_value as variant_advance_value',
                'product_attributes.price',
                'product_attributes.is_default'
            );
        $data = $query->get();
        // dd(DB::getQueryLog());
        return $data;
    }
    
    public function timeFormat($format = 'd/m/Y')
    {
        return date($format, strtotime($this->created_at));
    }
    
    // khai báo thêm các hàm khác bên dưới nếu cần
}