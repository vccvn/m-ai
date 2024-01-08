<?php
namespace App\Masks\Users;

use App\Masks\Accounts\AgentMask;
use App\Masks\Accounts\WalletMask;
use App\Masks\Files\FileCollection;
use App\Masks\Hobbies\HobbyCollection;
use App\Masks\Locations\RegionMask;
use App\Models\User;
use Gomee\Masks\Mask;
/**
 * user class
 * @property int $agent_id
 * @property int $id
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
 * @property Region $region
 * @property integer $trashed_status
 * @property-read AgentPaymentLog[] $agentPaymentUnreportedLogs
 * @property AgentMask $agentAccount
 * @property WalletMask $wallet
 *
 */
class UserMask extends Mask
{

    // xem thêm ExampleMask
    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        $this->allow('getAvatar', 'getBirthday', 'getServiceExpiredInfo');
        $this->map([
            // 'hobbies' => HobbyCollection::class,
            // 'images' => FileCollection::class,
            'region' => RegionMask::class,
            'agentAccount' => AgentMask::class,
            'wallet' => WalletMask::class
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
        $this->metadata = is_array($this->metadata) ? $this->metadata : ($this->metadata?json_decode($this->metadata, true) :[]);
    }


    // khai báo thêm các hàm khác bên dưới nếu cần
}
