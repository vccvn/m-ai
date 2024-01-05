<?php
namespace App\Masks\Accounts;

use App\Models\AgentAccount;
use Gomee\Masks\Mask;
/**
 * AgentMask class
 * 
 * @property integer $user_id User Id
 * @property integer $policy_id Policy Id
 * @property integer $month_balance Month Balance
 * @property float $commission_level_1 Commission Level 1
 * @property float $commission_level_2 Commission Level 2
 * @property float $commission_level_3 Commission Level 3
 * @property float $commission_level_4 Commission Level 4
 * @property string $bank_name Bank Name
 * @property string $bank_brand Bank Brand
 * @property string $bank_account_name Bank Account Name
 * @property string $bank_account_id Bank Account Id
 * @property-read AgentAccount $model
 */
class AgentMask extends Mask
{

    /**
     * thêm các thiết lập của bạn
     * ví dụ thêm danh sách cho phép truy cập vào thuộc tính hay gọi phương thức trong model
     * hoặc map vs các quan hệ dữ liệu
     *
     * @return void
     */
    protected function init(){
        # code...
    }

    /**
     * lấy data từ model sang mask
     * @param AgentAccount $model Tham số không bắt buộc phải khai báo. 
     * Xem thêm ExampleMask
     * @return array>string, miced>
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
        # code...
    }
 
}