<?php

namespace App\Models;

use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CoverLetter class
 *
 * @property integer $user_id User Id
 * @property string $message Message
 * @property integer $status Status
 * @property User $user
 */
class CoverLetter extends Model
{
    public $table = 'cover_letters';
    public $fillable = ['user_id', 'message', 'status'];

    const PENDING = 0;
    const ACCEPTED = 1;
    const DECLINED = -1;


    const STATUS_LIST = [
        self::PENDING, self::ACCEPTED, self::DECLINED
    ];

    const STATUS_LABELS = [
        self::PENDING => 'Đang chờ', self::ACCEPTED => 'Chấp thuận', self::DECLINED => 'Từ chối'
    ];

    public static function getStatusOptions(){
        return static::STATUS_LABELS;
    }

    public function getStatusLabel(){
        return static::STATUS_LABELS[$this->status]??'';
    }

    /**
     * Get the user that owns the CoverLetter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
