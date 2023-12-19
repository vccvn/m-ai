<?php

namespace App\Models;
use Gomee\Models\Model;

class EmailToken extends Model
{
    public $table = 'email_tokens';
    public $fillable = ['email', 'type', 'ref', 'ref_id', 'token', 'code', 'expired_at'];


}
