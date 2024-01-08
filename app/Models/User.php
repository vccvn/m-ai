<?php

namespace App\Models;

use App\Services\Encryptions\HashService;
use App\Services\Notifications\NotificationService;
use Carbon\Carbon;
use Gomee\Helpers\Arr;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;

/**
 * user class
 * @property string $agent_id
 * @property string $id
 * @property string $name Name
 * @property string $gender Gender
 * @property string $birthday Birthday
 * @property string $email Email
 * @property string $username Username
 * @property string $password Password
 * @property string $phone_number Phone Number
 * @property string $avatar Avatar
 * @property string $type Type
 * @property float $balance Balance
 * @property float $money_in Money In
 * @property float $money_out Money Out
 * @property string $google2fa_secret Google2fa Secret
 * @property string $email_verified_at Email Verified At
 * @property string $fcm_token Fcm Token
 * @property array $account_data Account Data
 * @property integer $status Status
 * @property integer $trashed_status Trashed Status
 * @property string $ref_code ma gioi thieu
 * @property-read string $affiliate_code ma marketing
 * @property Carbon $expired_at
 * @property UserHobby[] $userHobbies
 * @property Region $region
 * @property integer $trashed_status
 * @property-read AgentPaymentLog[] $agentPaymentUnreportedLogs
 * @property AgentAccount $agentAccount
 *
 */
class User extends Authenticatable
{

    const MODEL_TYPE = 'default';

    const SCAN_ENCRYPT_FILE_EXTENSION = 'ime';

    const ADMIN = 'admin';
    const USER = 'user';
    const AGENT = 'agent';
    // const AGENT_LV2 = 'agent_lv2';s89
    const MERCHANT = 'merchant';
    const MANAGER = 'manager';
    const NOT_ACTIVATED = 0;
    const DEACTIVATED = 0;
    const ACTIVATED = 1;
    const BLOCKED = -1;
    const BANNED = -2;


    const STATUS_LIST = [
        self::NOT_ACTIVATED, self::ACTIVATED, self::BLOCKED, self::BANNED
    ];



    const TRUST_EMAIL_SCORE = 10;
    const TRUST_PHONE_SCORE = 20;
    const TRUST_EKYC_SCORE = 30; // Citizen Identity
    const TRUST_PAY_SCORE = 10;
    const TRUST_TIME_SCORE = 30;


    const ALL_TYPE = [
        self::USER, self::AGENT, self::MERCHANT, self::ADMIN
    ];
    const TYPE_LABELS = [
        self::USER => 'Người dùng phổ thông',
        self::AGENT => 'Đại lý',
        self::MERCHANT => 'Merchant',
        self::MANAGER => 'Người quản lý',
        self::ADMIN => 'Admin'
    ];

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHER = 'other';

    const GENDER_LABELS = [
        self::GENDER_MALE => 'Nam',
        self::GENDER_FEMALE => 'Nữ',
        self::GENDER_OTHER => 'Khác'
    ];



    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'name',
        'gender',
        'birthday',
        'email',
        'username',
        'password',
        'phone_number',
        'avatar',
        'type',
        'ref_code',
        'affiliate_code',
        'balance',
        'money_in',
        'money_out',
        'google2fa_secret',
        'email_verified_at',
        'fcm_token',
        'account_data',
        'expired_at',
        'status',
        'trashed_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'fcm_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'account_data' => 'json'
    ];

    protected $role_levels = [];

    protected $_meta = [];
    protected $_roles = [];
    protected $isCheckRoles = false;
    /**
     * các giá trị mặc định
     *
     * @var array
     */
    protected $defaultValues = [];


    public function __getModelType__()
    {
        return static::MODEL_TYPE;
    }


    public static function getTypeOptions()
    {
        return self::TYPE_LABELS;
    }


    /**
     * lấy về giá trị mặc định khi muốn fill để create data
     *
     * @return array<string, mixed>
     */
    public function getDefaultValues()
    {
        return $this->defaultValues;
    }

    /**
     * Get the agentAccount associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function agentAccount(): HasOne
    {
        return $this->hasOne(AgentAccount::class, 'user_id', 'id');
    }

    public function getServiceExpiredInfo($format = null){
        if(!$this->expired_at) return false;
        $time = strtotime($this->expired_at);
        if($time > time())
            return $format?date($format, $time):$this->expired_at;
        return false;
    }

    public function getNotificationToken(){
        if($this->fcm_token)
            return $this->fcm_token;
        $token = \Illuminate\Support\Str::uuid()->toString();
        $this->fcm_token = $token;
        $this->save();
        return $token;
    }

    public function setNotificationToken($token){
        $this->fcm_token = $token;
        return $this->save();
    }

    public function pushNotification($title = 'Notification Title', $body = 'Notification Body Content'){
        return NotificationService::class;
    }

    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getAvatar($urlencode = false)
    {

        if ($this->avatar) {
            $avatar = content_path('users/avatar/' . $this->avatar);
        } else {
            $avatar = 'static/images/default/avatar.png';
        }
        $url = url($avatar);
        if ($urlencode) return urlencode($url);
        return $url;
    }

    public function getBirthday($format = null)
    {
        return $format ? date($format, strtotime($this->birthday)) : $this->birthday;
    }


    /**
     * lấy account data
     *
     * @param boolean $convert
     * @return array|Arr
     */
    public function getAccountData($convert = false)
    {
        $a = is_array($this->account_data) ? $this->account_data : json_decode($this->account_data ?? '[]', true);
        if (!is_array($a)) $a = [];
        if ($convert) return new Arr($a);
        return $a;
    }



    /**
     * xoa avatar
     */
    public function deleteAvatar()
    {
        if ($this->avatar && file_exists($path = public_path(content_path('users/avatar/' . $this->avatar)))) {
            unlink($path);
        }
    }

    public function getGenderLabel()
    {
        return __('profile.genders.' . $this->gender);
    }
    public function getTypeLabel()
    {
        $types = self::getTypeOptions();
        return is_array($types) ? ($types[$this->type] ?? $this->type) : null;
    }

    /**
     * ham xóa file cũ
     * @param int $id
     *
     * @return boolean
     */
    public function deleteAttachFile()
    {
        return $this->deleteAvatar();
    }


    /**
     * lấy tên file đính kèm cũ
     */
    public function getAttachFilename()
    {
        return $this->avatar;
    }
    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $a = $this->getAvatar();
        $data = $this->toArray();
        $a = explode(' ', $this->name);
        $data['first_name'] = array_pop($a);
        $data['last_name'] = implode(' ', $a);
        if ($this->avatar) $data['avatar'] = $a;

        if($this->type == User::AGENT && $this->agentAccount){
            $aaa = $this->agentAccount->toArray();
            foreach ($aaa as $key => $value) {
                $data['agent_' . $key] = $value;
            }
        }

        return $data;
    }





    public function canDelete()
    {
        if ($this->type == 'admin') return false;
        return true;
    }



    /**
     * kiểm tra có thể xóa hay không
     * @return boolean
     */
    public function canMoveToTrash()
    {
        if ($this->type == 'admin') return false;
        return true;
    }

    public function beforeDelete()
    {
        $this->metadatas()->delete();
        // $this->userRole()->delete();
        $this->deleteAvatar();
    }


    // ham get username khong dung hang

    public function getUsername($str = null, $id = null)
    {
        if (!$str && !isset($this->id) && !$id) return null;
        elseif ($id) {
            if ($u = self::find($id)) {
                if (!$str) $str = $u->name;
            }
        } elseif (isset($this->id) && $this->id) {
            $id = $this->id;
        }
        $aslug = str_slug($str, '');
        $slug = null;
        $i = 1;
        $c = '';
        $s = true;
        do {
            $sl = $aslug . $c;
            if ($item = self::where('username', $sl)->first()) {
                if ($id && $item->id == $id) {
                    $slug = $sl;
                    $s = false;
                }
                $c = '-' . $i;
            } else {
                $slug = $sl;
                $s = false;
            }

            $i++;
        } while ($s);

        return $slug;
    }
}
