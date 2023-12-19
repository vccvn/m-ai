<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    
    public $table = 'carts';
    public $fillable = ['user_id', 'customer_id', 'secret_id','sub_total', 'total_money'];

    
    


    /**
     * kết nối với bảng order item
     * @return QueryBuilder
     */
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    // public function details()
    // {
    //     return $this->items()->join('products', 'products.id', '=', 'cart_items.product_id')
    //                 ->select(
    //                     'order_items.*', 
    //                     'products.name as product_name', 
    //                     'products.slug', 
    //                     'products.type as product_type', 
    //                     'products.featured_image as product_image'
    //                 );
    // }

    /**
     * Get all of the details for the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id')->with('product');
    }

    /**
     * The products that belong to the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_items', 'cart_id', 'product_id');
    }
    
    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        $items = [];
        if(count($this->items)){
            foreach ($this->items as $item) {
                $idata = $item->toFormData();
                $items[] = $idata;

            }
        }
        $data['items'] = $items;
        return $data;
    }

    

    public function beforeDelete()
    {
        $this->items()->delete();
    }
}
