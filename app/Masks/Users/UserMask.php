<?php
namespace App\Masks\Users;

use App\Masks\Files\FileCollection;
use App\Masks\Hobbies\HobbyCollection;
use App\Masks\Locations\RegionMask;
use App\Models\User;
use Gomee\Masks\Mask;

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
        $this->allow('getAvatar', 'getBirthday');
        $this->map([
            'hobbies' => HobbyCollection::class,
            'images' => FileCollection::class,
            'region' => RegionMask::class
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
