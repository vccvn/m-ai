<?php

namespace App\DTOs\Users;

use Gomee\DTOs\DTO;

/**
 * User DTO class
 *
 * @property string $uuid
 * @property string $full_name
 * @property string $gender
 * @property string $birthday
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property string $avatar
 * @property string $type
 * @property string $affiliate_code
 * @property string $ref_code
 * @property float $wallet_balance
 * @property integer $connect_count
 * @property string $country_code
 * @property string $locale
 * @property string $mbti
 * @property integer $trust_score
 * @property string $bio
 * @property string $region_id
 * @property string $district_id
 * @property string $ward_id
 * @property string $address
 * @property string $identity_card_id
 * @property boolean $is_verified_phone
 * @property boolean $is_verified_email
 * @property boolean $is_verified_identity
 * @property integer $status
 * @property string $google2fa_secret
 * @property string $email_verified_at
 * @property integer $trashed_status
 */
class UserDTO extends DTO{
    protected $accessible = ['id', 'full_name', 'gender', 'birthday', 'email', 'username', 'password', 'phone_number', 'avatar', 'type', 'affiliate_code', 'ref_code', 'wallet_balance', 'connect_count', 'country_code', 'locale', 'mbti', 'trust_score', 'bio', 'region_id', 'district_id', 'ward_id', 'address', 'identity_card_id', 'is_verified_phone', 'is_verified_email', 'is_verified_identity', 'status', 'google2fa_secret', 'email_verified_at', 'trashed_status'];
}
