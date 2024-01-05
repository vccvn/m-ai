<?php

namespace App\Repositories\Payments;

use Gomee\Repositories\BaseRepository;
use App\Masks\Payments\TransactionMask;
use App\Masks\Payments\TransactionCollection;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Validators\Payments\TransactionValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @method TransactionCollection<TransactionMask>|PaymentTransaction[] filter(Request $request, array $args = []) lấy danh sách PaymentTransaction được gán Mask
 * @method TransactionCollection<TransactionMask>|PaymentTransaction[] getFilter(Request $request, array $args = []) lấy danh sách PaymentTransaction được gán Mask
 * @method TransactionCollection<TransactionMask>|PaymentTransaction[] getResults(Request $request, array $args = []) lấy danh sách PaymentTransaction được gán Mask
 * @method TransactionCollection<TransactionMask>|PaymentTransaction[] getData(array $args = []) lấy danh sách PaymentTransaction được gán Mask
 * @method TransactionCollection<TransactionMask>|PaymentTransaction[] get(array $args = []) lấy danh sách PaymentTransaction
 * @method TransactionCollection<TransactionMask>|PaymentTransaction[] getBy(string $column, mixed $value) lấy danh sách PaymentTransaction
 * @method TransactionMask|PaymentTransaction getDetail(array $args = []) lấy PaymentTransaction được gán Mask
 * @method TransactionMask|PaymentTransaction detail(array $args = []) lấy PaymentTransaction được gán Mask
 * @method TransactionMask|PaymentTransaction find(integer $id) lấy PaymentTransaction
 * @method TransactionMask|PaymentTransaction findBy(string $column, mixed $value) lấy PaymentTransaction
 * @method TransactionMask|PaymentTransaction first(string $column, mixed $value) lấy PaymentTransaction
 * @method PaymentTransaction create(array $data = []) Thêm bản ghi
 * @method PaymentTransaction update(integer $id, array $data = []) Cập nhật
 *
 * @property PaymentTransaction $_model Model
 */
class TransactionRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    // protected $validatorClass = TransactionValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = TransactionMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = TransactionCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PaymentTransaction::class;
    }


    public function init()
    {
        $this->defaultSortBy = [
            'created_at' => 'DESC'
        ];
        $columns = [
            'user_name' => 'users.name',
            'user_phone_number' => 'users.phone_number',
            'user_email' => 'users.email',
            'package_name' => 'service_packages.name',
            'quantity' => 'service_packages.quantity'

        ];
        $this->setJoinable([
            ['leftJoin', 'users', 'users.id', '=', 'payment_transactions.user_id'],
            ['leftJoin', 'service_packages', 'service_packages.id', '=', 'payment_transactions.order_id']
        ]);
        $this->setSearchable($columns)->setWhereable($columns)->setSortable($columns)
            ->setSelectable(array_merge($columns, ['payment_transactions.*']));
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
            $this->whereDate('payment_transactions.created_at', '>=', "$frmDate[year]-$frmDate[month]-$frmDate[day]");
        }
        if ($request->to_date && ($toDate = strtodate($request->to_date))) {
            $this->whereDate('payment_transactions.created_at', '<=', "$toDate[year]-$toDate[month]-$toDate[day]");
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
        if ($pr = $this->with('method')->first(['transaction_code' => $response->transactionCode, 'status' => PaymentTransaction::STATUS_PROCESSING])) {
            DB::beginTransaction();
            try {
                //code...
                $this->update($pr->id, $pr->method && $pr->method->method == PaymentMethod::PAYMENT_ALEPAY ? [
                    'status' => $status ? PaymentTransaction::STATUS_COMPLETED : PaymentTransaction::STATUS_CANCELED,
                    'payment_method_name' => $response->method ?? $response->paymentMethod,
                    'message' => $message
                ] : ['status' => $status ? PaymentTransaction::STATUS_COMPLETED : PaymentTransaction::STATUS_CANCELED, 'message' => $message]);


                $d = $this->mode('mask')->with('method')->detail(['id' => $pr->id]);
                if ($status == PaymentTransaction::STATUS_COMPLETED) {
                    $this->fire('completed', $d);
                }
                DB::commit();
                return $d;
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
            }
        }
        return null;
    }
}
