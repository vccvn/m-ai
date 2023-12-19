<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLabelRef extends Model
{
    public $table = 'product_label_refs';
    public $fillable = ['label_id', 'ref', 'ref_id'];

    public $timestamps = false;

    /**
     * Get the product that owns the ProductLabelRef
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ref_id');
    }

    /**
     * Get the label that owns the ProductLabelRef
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function label(): BelongsTo
    {
        return $this->belongsTo(ProductLabel::class, 'label_id');
    }
    
}
