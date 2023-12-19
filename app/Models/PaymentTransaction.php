<?php

namespace App\Models;

use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * PT class
 *
 * @property integer $merchant_id Merchant Id
 * @property integer $user_id User Id
 * @property integer $payment_method_id Payment Method Id
 * @property integer $order_id Order Id
 * @property string $transaction_id Transaction Id
 * @property string $transaction_code Transaction Code
 * @property string $type Type
 * @property string $order_code Order Code
 * @property string $ref_code Ref Code
 * @property string $method Method
 * @property string $note Note
 * @property string $description Description
 * @property float $amount Amount
 * @property float $discount Discount
 * @property string $currency Currency
 * @property integer $total_item Total Item
 * @property string $payment_method_name Payment Method Name
 * @property array $ref_data Ref Data
 * @property integer $is_reported Is Reported
 */
class PaymentTransaction extends Model
{
    const DEPOSIT = 1;
    const WITHDRAW = -1;
    const ALL_TYPE = [
        self::DEPOSIT,
        self::WITHDRAW
    ];
    public $table = 'payment_transactions';
    public $fillable = ['merchant_id', 'user_id', 'payment_method_id', 'order_id', 'transaction_id', 'transaction_code', 'type', 'order_code', 'ref_code', 'method', 'note', 'description', 'amount', 'discount', 'currency', 'total_item', 'payment_method_name', 'ref_data', 'is_reported'];





    protected $casts = [
        'is_reported' => 'boolean',
        'ref_data' => 'json'
    ];
    /**
     * Get the user that owns the PaymentRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the method that owns the PaymentRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }


    /**
     * Get the examPackage that owns the PaymentTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function package(): BelongsTo
    // {
    //     return $this->belongsTo(ConnectPackage::class, 'order_id', 'id');
    // }

    public function getUserName()
    {
        return htmlentities($this->user_name??($this->user?$this->user->name:''));
    }

    public function getPackageName()
    {
        return $this->exam_name??($this->examPackage?$this->examPackage->name:'');
    }
    public function getMoneyFormat()
    {
        return number_format($this->amount, 0, '.', ',');
    }

    public static function getTypeLabels()
    {
        return [
            self::DEPOSIT     => __('payment.deposit'),
            self::WITHDRAW    => __('payment.withdraw'),
        ];
    }

    /**
     * nguoi tao giao dich
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_id', 'id')->select('id','name', 'email', 'avatar');
    }

    /**
     * nguoi tao giao dich
     */
    public function apprevedBy()
    {
        return $this->belongsTo(User::class, 'appreved_id', 'id')->select('id','name', 'email', 'avatar');
    }

    /**
     * nguoi tao giao dich
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->select('id','name', 'email');
    }

    /**
     * nguoi tao giao dich
     */
    public function userCustomer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id')->select('id', 'name', 'email');
    }


    public function bills()
    {
        return $this->hasMany(File::class, 'ref_id', 'id')->where('ref', 'transaction');
    }

    /**
     * Get the bill associated with the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bill(): HasOne
    {
        return $this->hasOne(File::class, 'ref_id', 'id')->where('ref', 'transaction');
    }


    /**
     * xóa dữ liệu
     */
    public function beforeDelete()
    {
        // delete bills
        if(count($this->bills)){
            foreach ($this->bills as $bill) {
                $bill->delete();
            }
        }

    }


}
