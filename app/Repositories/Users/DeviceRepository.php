<?php

namespace App\Repositories\Users;

use Gomee\Repositories\BaseRepository;
use App\Masks\Users\DeviceMask;
use App\Masks\Users\DeviceCollection;
use App\Models\UserDevice;
use App\Validators\Users\DeviceValidator;
use Illuminate\Http\Request;

/**
 * @method DeviceCollection<DeviceMask>|UserDevice[] filter(Request $request, array $args = []) lấy danh sách UserDevice được gán Mask
 * @method DeviceCollection<DeviceMask>|UserDevice[] getFilter(Request $request, array $args = []) lấy danh sách UserDevice được gán Mask
 * @method DeviceCollection<DeviceMask>|UserDevice[] getResults(Request $request, array $args = []) lấy danh sách UserDevice được gán Mask
 * @method DeviceCollection<DeviceMask>|UserDevice[] getData(array $args = []) lấy danh sách UserDevice được gán Mask
 * @method DeviceCollection<DeviceMask>|UserDevice[] get(array $args = []) lấy danh sách UserDevice
 * @method DeviceCollection<DeviceMask>|UserDevice[] getBy(string $column, mixed $value) lấy danh sách UserDevice
 * @method DeviceMask|UserDevice getDetail(array $args = []) lấy UserDevice được gán Mask
 * @method DeviceMask|UserDevice detail(array $args = []) lấy UserDevice được gán Mask
 * @method DeviceMask|UserDevice find(integer $id) lấy UserDevice
 * @method DeviceMask|UserDevice findBy(string $column, mixed $value) lấy UserDevice
 * @method DeviceMask|UserDevice first(string $column, mixed $value) lấy UserDevice
 * @method UserDevice create(array $data = []) Thêm bản ghi
 * @method UserDevice update(integer $id, array $data = []) Cập nhật
 */
class DeviceRepository extends BaseRepository
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\UserDevice::class;
    }

}
