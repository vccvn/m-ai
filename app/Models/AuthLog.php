<?php

namespace App\Models;
use Gomee\Models\Model;
class AuthLog extends Model
{
    public $table = 'auth_logs';
    public $fillable = ['user_id', 'status', 'log_fail_count'];



}

