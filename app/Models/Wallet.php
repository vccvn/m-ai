<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * Wallet class
 * 
 * @property integer $user_id User Id
 * @property float $balance Balance
 * @property float $money_in Money In
 * @property float $money_out Money Out
 */
class Wallet extends Model
{
    public $table = 'wallets';
    public $fillable = ['user_id', 'balance', 'money_in', 'money_out'];

    
    
}
