<?php

namespace App\Repositories\Policies;

use Gomee\Repositories\BaseRepository;
use App\Masks\Policies\CommissionMask;
use App\Masks\Policies\CommissionCollection;
use App\Models\CommissionPolicy;
use App\Validators\Policies\CommissionValidator;
use Illuminate\Http\Request;

/**
 * @method CommissionCollection<CommissionMask>|CommissionPolicy[] filter(Request $request, array $args = []) lấy danh sách CommissionPolicy được gán Mask
 * @method CommissionCollection<CommissionMask>|CommissionPolicy[] getFilter(Request $request, array $args = []) lấy danh sách CommissionPolicy được gán Mask
 * @method CommissionCollection<CommissionMask>|CommissionPolicy[] getResults(Request $request, array $args = []) lấy danh sách CommissionPolicy được gán Mask
 * @method CommissionCollection<CommissionMask>|CommissionPolicy[] getData(array $args = []) lấy danh sách CommissionPolicy được gán Mask
 * @method CommissionCollection<CommissionMask>|CommissionPolicy[] get(array $args = []) lấy danh sách CommissionPolicy
 * @method CommissionCollection<CommissionMask>|CommissionPolicy[] getBy(string $column, mixed $value) lấy danh sách CommissionPolicy
 * @method CommissionMask|CommissionPolicy getDetail(array $args = []) lấy CommissionPolicy được gán Mask
 * @method CommissionMask|CommissionPolicy detail(array $args = []) lấy CommissionPolicy được gán Mask
 * @method CommissionMask|CommissionPolicy find(integer $id) lấy CommissionPolicy
 * @method CommissionMask|CommissionPolicy findBy(string $column, mixed $value) lấy CommissionPolicy
 * @method CommissionMask|CommissionPolicy first(string $column, mixed $value) lấy CommissionPolicy
 * @method CommissionPolicy create(array $data = []) Thêm bản ghi
 * @method CommissionPolicy update(integer $id, array $data = []) Cập nhật
 */
class CommissionRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = CommissionValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = CommissionMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = CommissionCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\CommissionPolicy::class;
    }

}