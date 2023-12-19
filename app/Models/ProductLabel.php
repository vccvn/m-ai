<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductLabel extends Model
{
    public $table = 'product_labels';
    public $fillable = ['title', 'slug', 'bg_color', 'text_color'];

    public function productLabels(): HasMany
    {
        return $this->hasMany(ProductLabelRef::class, 'label_id')->where('ref', Product::class);
    }


    
    public function products()
    {
        return $this->productLabels()->join('products', 'products.id', '=', 'product_label_refs.product_id')->select('product_label_refs.product_id', 'product_label_refs.label_id', 'products.*');
    }


    
}
