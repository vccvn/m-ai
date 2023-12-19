<?php

namespace App\Models;

use Gomee\Models\Model;

class Payment extends Model
{
    public $table = 'payments';
    public $fillable = [
        'user_id',
        'order_id',
        'transaction_id',
        'payment_transactions_id',
        'status',
        'order_code',
        'amount',
        'response_code',
        'response_message',
        'bank_type',
        'bank_code',
        'url'
    ];
}
