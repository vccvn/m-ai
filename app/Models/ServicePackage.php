<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ServicePackage class
 *
 * @property integer $agent_id Agent Id
 * @property string $role Role
 * @property string $name Name
 * @property string $description Description
 * @property integer $quantity Quantity
 * @property float $wholesale_price Wholesale Price
 * @property float $retail_price Retail Price
 * @property string $currency Currency
 * @property array $metadata Metadata
 */
class ServicePackage extends Model
{
    public $table = 'service_packages';
    public $fillable = [
        'agent_id',
        'role',
        'name',
        'description',
        'quantity',
        'wholesale_price',
        'retail_price',
        'currency',
        'metadata'
    ];

    /**
     * Get the agent that owns the ServicePackage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }


}
