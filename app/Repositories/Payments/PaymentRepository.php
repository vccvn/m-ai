<?php

namespace App\Repositories\Payments;

use Gomee\Repositories\BaseRepository;
use App\Masks\Payments\PaymentMask;
use App\Masks\Payments\PaymentCollection;
use App\Models\Payment;
use App\Validators\Payments\PaymentValidator;
use Illuminate\Http\Request;

/**
 * @method PaymentCollection<PaymentMask>|Payment[] filter(Request $request, array $args = []) lấy danh sách Payment được gán Mask
 * @method PaymentCollection<PaymentMask>|Payment[] getFilter(Request $request, array $args = []) lấy danh sách Payment được gán Mask
 * @method PaymentCollection<PaymentMask>|Payment[] getResults(Request $request, array $args = []) lấy danh sách Payment được gán Mask
 * @method PaymentCollection<PaymentMask>|Payment[] getData(array $args = []) lấy danh sách Payment được gán Mask
 * @method PaymentCollection<PaymentMask>|Payment[] get(array $args = []) lấy danh sách Payment
 * @method PaymentCollection<PaymentMask>|Payment[] getBy(string $column, mixed $value) lấy danh sách Payment
 * @method PaymentMask|Payment getDetail(array $args = []) lấy Payment được gán Mask
 * @method PaymentMask|Payment detail(array $args = []) lấy Payment được gán Mask
 * @method PaymentMask|Payment find(integer $id) lấy Payment
 * @method PaymentMask|Payment findBy(string $column, mixed $value) lấy Payment
 * @method PaymentMask|Payment first(string $column, mixed $value) lấy Payment
 * @method Payment create(array $data = []) Thêm bản ghi
 * @method Payment update(integer $id, array $data = []) Cập nhật
 *
 * @property Payment $_model Model
 */
class PaymentRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = PaymentValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = PaymentMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = PaymentCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Payment::class;
    }



}
