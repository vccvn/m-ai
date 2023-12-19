<?php

namespace App\Masks\Orders;

use App\Masks\Products\ProductMask;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Promo;
use App\Repositories\Products\ProductAttributeRepository;
use Gomee\Masks\Mask;

/**
 * @property ProductMask $product Sản phẩm
 */
class CartItemMask extends Mask
{

    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init()
    {
        $this->allow([
            'pivot'
        ]);
        $this->map([
            'product' => ProductMask::class
        ]);
    }


    /**
     * lấy data từ model sang mask
     * @param CartItem $cartItem Tham số không bắt buộc phải khai báo. 
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
        if ($this->product) {
            $this->image = $this->product->getImage();
            $this->link = $this->product->getViewUrl();
            $this->attributes = new ItemAttributeCollection($this->getAttrs());
            // $this->list_price = $this->product->list_price;
            // $this->final_price = $this->product->getFinalPrice();
            $this->product_name = $this->product->name;
            $this->has_promo = $this->product->hasPromo();

            $this->checkVarianPrice();
            $this->final_variant_price = $this->getFinalPrice();

            $this->price = $this->final_variant_price;
            $this->final_price = $this->final_variant_price;
            $this->total_list_origin_price = $this->quantity * $this->list_price;
            $this->total_final_origin_price = $this->quantity * $this->product->getFinalPrice();
            
            $this->total_list_price = $this->quantity * $this->list_variant_price;
            $this->total_price = $this->quantity * $this->final_variant_price;
            
            
        } else {
            $this->image = get_product_image($this->product_image);
            $this->link = get_product_url($this->model);
            $this->attributes = new ItemAttributeCollection($this->getAttrs());
            $this->total_price = $this->quantity * $this->final_price;
        }
    }
    public function getPrice()
    {
        return $this->final_price;
    }
    public function getPriceFormat($type = 'final')
    {
        return get_currency_format($this->{$type . '_price'});
    }

    public function getTotal()
    {
        return $this->quantity * $this->final_price;
    }

    public function getTotalFormat()
    {
        return get_currency_format($this->getTotal());
    }


    public function getOrderOption($name, $type = null)
    {
        return $this->product ? $this->product->getOrderOption($name, $type) : false;
    }


    public function getTypeOrderOption($type)
    {
        return $this->product ? $this->product->getTypeOrderOption($type) : false;
    }

    
    /**
     * tính giá cuối cùng (sau các loại khuyến mãi)
     * @return float
     */
    public function getFinalPrice()
    {
        $price = $this->product->on_sale ? $this->sale_variant_price : $this->list_variant_price;
        if ($price < 0) return $price;
        if (count($this->product->promoAvailable)) {
            foreach ($this->product->promoAvailable as $promo) {
                $down_price = $promo->down_price;
                if ($promo->type == Promo::TYPE_SAME_PRICE) {
                    $price = $down_price;
                } elseif ($promo->type == Promo::TYPE_DOWN_PERCENT) {
                    $down = $down_price * $price / 100;
                    $price -= $down;
                } elseif ($promo->type == Promo::TYPE_DOWN_PRICE) {
                    $price -= $down_price;
                }
            }
        }

        return $price < 0 ? 0 : $price;
    }
    

    public function checkVarianPrice()
    {
        $list_price = $this->product->list_price;
        $sale_price = $this->product->sale_price;
        $attributes = explode('-', $this->attr_values);
        if ($attributes) {
            $attrs = $this->attributes;
            $change = 0;
            if (count($attrs)) {
                foreach ($attrs as $key => $attr) {
                    if ($attr->price_type) {
                        if (!$change) {
                            $list_price = $attr->price;
                            $sale_price = $attr->price;
                            $change = 1;
                        }
                    } else {
                        $list_price += $attr->price;
                        $sale_price += $attr->price;
                            
                    }
                }
            }
        }
        $this->list_variant_price = $list_price;
        $this->sale_variant_price = $sale_price;
        

    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
