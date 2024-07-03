<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * UserDevice class
 *
 * @property integer $user_id User Id
 * @property string $user_agent User Agent
 * @property string $device Device
 * @property string $platform Platform
 * @property string $browser Browser
 * @property string $session_token Session Token
 * @property string $ip IP address
 * @property boolean $approved
 * @property string $last_login_at Last Login At
 */
class UserDevice extends Model
{
    public $table = 'user_devices';
    public $fillable = ['user_id', 'user_agent', 'device', 'platform', 'browser', 'ip', 'session_token','approved', 'last_login_at'];



}
