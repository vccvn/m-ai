<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public $table = 'customers';
    public $fillable = ['user_id', 'name', 'email', 'phone_number', 'address', 'region_id', 'district_id', 'ward_id', 'balance', 'remember_token', 'trashed_status'];

    
    
    
    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }
    public function district()
    {
        return $this->belongsTo('App\Models\District', 'district_id');
    }
    public function ward()
    {
        return $this->belongsTo('App\Models\Ward', 'ward_id');
    }

    /**
     * rewviews
     */
    public function reviews()
    {
        return $this->hasMany('App\Models\ProductReview', 'customer_id');
    }

    /**
     * feedback
     */
    public function feedback()
    {
        return $this->hasMany('App\Models\OrderFeedback', 'customer_id');
    }

    /**
     * Get all of the orders for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get all of the transactions for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }

    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $data = $this->toArray();
        return $data;
    }

    /**
     * kiểm tra xem có dược xóa hay không
     *
     * @return boolean|string
     */
    public function canDelete()
    {
        if(count($this->orders) || count($this->transactions)){
            return 'Tài khoản này đã có đơn hàng hoặc thực hiện giao dịch, việc xóa tài khoản này có thẻ làm sai lệch số liệu hệ thống!';
        }
        return true;
    }

    public function canMoveToTrash()
    {
        return $this->canDelete();
    }


    public function beforeDelete()
    {
        $this->reviews()->delete();
        $this->feedback()->delete();
        $this->orders()->delete();
        $this->transactions()->delete();

    }
}
