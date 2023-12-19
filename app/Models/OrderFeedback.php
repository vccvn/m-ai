<?php

namespace App\Models;
use Gomee\Models\Model;
class OrderFeedback extends Model
{
    public $table = 'order_feedbacks';
    public $fillable = ['order_id', 'customer_id', 'user_id', 'type', 'title', 'description', 'solved', 'solved_at'];

    
    
}
