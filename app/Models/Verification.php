<?php

namespace App\Models;

use Carbon\Carbon;
use Gomee\Models\Model;

/**
 * Verification class
 *
 * @property string $id ID của verification
 * @property string $type Loại verify
 * @property string $method Phương thức
 * @property string $send_to Nhận mã thông qua giá trị cụ thể
 * @property string $ref Bảng liên quan
 * @property string $ref_id id của bảng liên quan
 * @property string $code Mã xác minh
 * @property string $ip_address địa chỉ ip
 * @property string $status trạng thái
 * @property Carbon $expired_at Hạn xác thực
 */
class Verification extends Model
{
    const METHOD_PHONE = 'phone';
    const METHOD_EMAIL = 'email';

    const ALL_METHOD = [self::METHOD_PHONE, self::METHOD_EMAIL];

    const METHOD_LABELS = [
        self::METHOD_EMAIL => 'Địa chỉ E-Mail',
        self::METHOD_PHONE => 'Số điện thoại'
    ];

    const STATUS_IDLE = 'idle';
    const STATUS_VERIFIED = 'verified';

    const TYPE_VERIFY = 'verify';
    const TYPE_CONFIRM = 'confirm';
    const TYPE_REGISTER = 'register';

    const ALL_TYPE = [self::TYPE_VERIFY, self::TYPE_CONFIRM, self::TYPE_REGISTER];

    /**
     * Thời gian gữa 2 lần verify tính bằng phút
     */
    const TIME_BETWEEN = 2;

    public $table = 'verifications';
    public $fillable = ['id', 'type', 'method', 'send_to', 'ref', 'ref_id', 'code', 'ip_address', 'status', 'expired_at'];



}
