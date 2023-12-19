<?php

namespace App\Models;
use Gomee\Models\Model;
class ContactReply extends Model
{
    public $table = 'contact_replies';
    public $fillable = ['contact_id', 'user_id', 'message'];

    
    
}
