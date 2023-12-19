<?php

namespace App\Models;

use App\Services\Encryptions\HashService;
use Gomee\Models\CommonMethods;
use Gomee\Models\ModelEventMethods;
use Gomee\Models\ModelFileMethods;
use Gomee\Models\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

/**
 * user class
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $birthday
 * @property string $username
 * @property mixed $email
 * @property mixed $password
 * @property mixed $phone_number
 * @property mixed $ci_card_number
 * @property mixed $ci_card_front_scan
 * @property mixed $ci_card_back_scan
 * @property boolean $ci_status
 * @property mixed $type
 * @property mixed $level
 * @property mixed $avatar
 * @property mixed $status
 * @property mixed $secret_id
 * @property mixed $google2fa_secret
 * @property mixed $trashed_status
 * @property mixed $address
 * @property mixed $region_id
 * @property mixed $district_id
 * @property mixed $ward_id
 * @property mixed $school_id
 * @property int $test_turns
 */
class Authenticatable extends AuthUser
{
    use HasApiTokens, HasFactory, Notifiable, ModelEventMethods, ModelFileMethods, CommonMethods,
        Uuid
    ;
}
