<?php
namespace App\Masks\Users;

use App\Masks\Files\FileCollection;
use App\Masks\Hobbies\HobbyCollection;
use App\Masks\Profiles\ProfileMask;
use App\Models\User;
use Gomee\Masks\Mask;
/**
 * MerchantMask class
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
 * @property string $local
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
 * @property HobbyCollection $hobbies
 * @property FileCollection $images
 * @property ProfileMask $connectedPartner
 */
class MerchantMask extends Mask
{

    protected $hidden = [
        'username',
        'password',
        'type',
        'affiliate_code',
        'ref_code',
        'wallet_balance',
        'connect_count',
        'identity_card_id',
        'is_verified_phone',
        'is_verified_email',
        'is_verified_identity',
        'status',
        'google2fa_secret',
        'email_verified_at',
        'trashed_status',
        'account_data'

    ];
    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        $this->allow('getAvatar', 'getBirthday');
        $this->alias([
            // 'authPartner' => 'connectedPartner'
        ]);
        $this->map([
            'hobbies' => HobbyCollection::class,
            'images' => FileCollection::class,
            'region' => RegionMask::class
            // 'connectedPartner' => self::class
        ]);

    }
    /**
     * lấy data từ model sang mask
     * @param User $user Tham số không bắt buộc phải khai báo.
     * Xem thêm ExampleMask
     */
    // public function toMask()
    // {
    //     $data = $this->getAttrData();
    //     // thêm data tại đây.
    //     // Xem thêm ExampleMask
    //     return $data;

    // }

    /**
     * sẽ được gọi sau khi thiết lập xong
     *
     * @return void
     */
    protected function onLoaded()
    {

        if($this->avatar || (!$this->images || !count($this->images))){
            $this->avatar_url = $this->getAvatar();
        }
        else{
            $this->avatar_url = $this->images[0]->getUrl();
        }
    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
