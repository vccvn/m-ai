<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AgentAccount class
 *
 * @property integer $user_id User Id
 * @property integer $policy_id Policy Id
 * @property integer $month_balance Month Balance
 * @property float $commission_level_1 Commission Level 1
 * @property float $commission_level_2 Commission Level 2
 * @property float $commission_level_3 Commission Level 3
 * @property float $commission_level_4 Commission Level 4
 * @property float $revenue revenue
 * @property CommissionPolicy $policy
 * @property User $user
 */
class AgentAccount extends Model
{
    public $table = 'agent_accounts';
    public $fillable = [
        'user_id',
        'policy_id',
        'month_balance',
        'commission_level_1',
        'commission_level_2',
        'commission_level_3',
        'commission_level_4'
];

    /**
     * Get the user that owns the AgentAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the policy that owns the AgentAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(CommissionPolicy::class, 'policy_id', 'id');
    }

}
