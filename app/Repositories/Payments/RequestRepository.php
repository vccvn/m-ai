<?php

namespace App\Repositories\Payments;

use Gomee\Repositories\BaseRepository;
use App\Masks\Payments\RequestCollection;
use App\Models\PaymentMethod;
use App\Models\PaymentRequest;
use App\Services\Payments\AlePayResponse;
use App\Validators\Payments\RequestValidator;
use Illuminate\Http\Request;

/**
 * @method PaymentRequestCollection<PaymentRequestMask>|PaymentRequest[] filter(Request $request, array $args = []) lấy danh sách PaymentRequest được gán Mask
 * @method PaymentRequestCollection<PaymentRequestMask>|PaymentRequest[] getFilter(Request $request, array $args = []) lấy danh sách PaymentRequest được gán Mask
 * @method PaymentRequestCollection<PaymentRequestMask>|PaymentRequest[] getResults(Request $request, array $args = []) lấy danh sách PaymentRequest được gán Mask
 * @method PaymentRequestCollection<PaymentRequestMask>|PaymentRequest[] getData(array $args = []) lấy danh sách PaymentRequest được gán Mask
 * @method PaymentRequestCollection<PaymentRequestMask>|PaymentRequest[] get(array $args = []) lấy danh sách PaymentRequest
 * @method PaymentRequestCollection<PaymentRequestMask>|PaymentRequest[] getBy(string $column, mixed $value) lấy danh sách PaymentRequest
 * @method PaymentRequestMask|PaymentRequest getDetail(array $args = []) lấy PaymentRequest được gán Mask
 * @method PaymentRequestMask|PaymentRequest detail(array $args = []) lấy PaymentRequest được gán Mask
 * @method PaymentRequestMask|PaymentRequest find(integer $id) lấy PaymentRequest
 * @method PaymentRequestMask|PaymentRequest findBy(string $column, mixed $value) lấy PaymentRequest
 * @method PaymentRequestMask|PaymentRequest first(string $column, mixed $value) lấy PaymentRequest
 * @method PaymentRequest create(array $data = []) Thêm bản ghi
 * @method PaymentRequest update(integer $id, array $data = []) Cập nhật
 *
 */
class RequestRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = RequestValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = RequestMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = RequestCollection::class;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PaymentRequest::class;
    }


    public function init()
    {
        $this->defaultSortBy = [
            'created_at' => 'DESC'
        ];
        $columns = [
            'user_name' => 'users.full_name',
            'user_phone_number' => 'users.phone',
            'user_email' => 'users.email',
            'package_name' => 'upload_packages.name',
            'upload_count' => 'upload_packages.upload_count',
            'status' => 'payment_requests.status',

        ];
        $this->setJoinable([
            ['leftJoin', 'users', 'users.id', '=', 'payment_requests.user_id'],
            ['leftJoin', 'upload_packages', 'upload_packages.id', '=', 'payment_requests.order_id']
        ]);
        $this->setSearchable($columns)->setWhereable($columns)->setSortable($columns)
            ->setSelectable(array_merge($columns, ['payment_requests.*']));

    }

    /**
     * build download
     *
     * @return $this
     */
    public function buildDownloadQuery()
    {
        $this->buildJoin();
        $this->buildSelect();
        return $this;
    }

    public function beforeFilter($request)
    {
        if ($request->from_date && ($frmDate = strtodate($request->from_date))) {
            $this->whereDate('payment_requests.created_at', '>=', "$frmDate[year]-$frmDate[month]-$frmDate[day]");
        }
        if ($request->to_date && ($toDate = strtodate($request->to_date))) {
            $this->whereDate('payment_requests.created_at', '<=', "$toDate[year]-$toDate[month]-$toDate[day]");
        }
    }



    /**
     * update payment status
     *
     * @param AlePayResponse $response
     * @param boolean $status
     * @return PaymentRequest|false
     */
    public function updatePaymentStatus($response, $status = false, $message = null)
    {
        if ($pr = $this->with('method')->first(['transaction_code' => $response->transactionCode, 'status' => PaymentRequest::STATUS_PROCESSING])) {
            $this->update($pr->id, $pr->method && $pr->method->method == PaymentMethod::PAYMENT_ALEPAY ? [
                'status' => $status ? PaymentRequest::STATUS_COMPLETED : PaymentRequest::STATUS_CANCELED,
                'payment_method_name' => $response->method ?? $response->paymentMethod,
                'message' => $message
            ] : ['status' => $status ? PaymentRequest::STATUS_COMPLETED : PaymentRequest::STATUS_CANCELED, 'message' => $message]);
            return $this->mode('mask')->with('method')->detail(['id' => $pr->id]);
        }
        return null;
    }

}
