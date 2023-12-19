<?php

namespace App\Models;

use Gomee\Helpers\Arr;
use Gomee\Models\Model;

class PaymentMethod extends Model
{

    const PAYMENT_COD = 'cod';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_VNPAY = 'vnpay';
    const PAYMENT_MOMO = 'momo';
    const PAYMENT_ALEPAY = 'alepay';

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const ALL_STATUS = [self::DEACTIVE, self::ACTIVE];
    const STATUS_LABELS = [
        self::DEACTIVE => 'Không kích hoạt', self::ACTIVE => 'Kích hoạt'
    ];


    public $table = 'payment_methods';
    public $fillable = ['name', 'method', 'config', 'description', 'guide', 'priority', 'status', 'trashed_status'];


    public $casts = [
        'config' => 'json',
    ];



    protected $configData = null;

    public function getConfigData() {
        if($this->configData) return $this->configData;
        $this->configData = new Arr(
            is_array($this->config) || is_object($this->config)? $this->config : ($this->config?json_decode($this->config):[])
        );
        return $this->configData;
    }
}
