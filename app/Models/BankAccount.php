<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BankAccount class
 *
 * @property integer $user_id User Id
 * @property string $name Name
 * @property string $brand Brand
 * @property string $account_name Account Name
 * @property string $account_id Account Id
 */
class BankAccount extends Model
{
    public $table = 'bank_accounts';
    public $fillable = ['user_id', 'name', 'brand', 'account_name', 'account_id'];

    /**
     * Get the user that owns the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

}
