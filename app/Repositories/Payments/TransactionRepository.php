<?php

namespace App\Repositories\Payments;

use Gomee\Repositories\BaseRepository;
use App\Masks\Payments\TransactionMask;
use App\Masks\Payments\TransactionCollection;
use App\Models\PaymentTransaction;
use App\Validators\Payments\TransactionValidator;
use Illuminate\Http\Request;

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
    protected $validatorClass = TransactionValidator::class;
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
            'user_name' => 'users.full_name',
            'user_phone_number' => 'users.phone_number',
            'user_email' => 'users.email',
            'package_name' => 'upload_packages.name',
            'upload_count' => 'upload_packages.upload_count'

        ];
        $this->setJoinable([
            ['leftJoin', 'users', 'users.id', '=', 'payment_transactions.user_id'],
            ['leftJoin', 'upload_packages', 'upload_packages.id', '=', 'payment_transactions.order_id']
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
}
