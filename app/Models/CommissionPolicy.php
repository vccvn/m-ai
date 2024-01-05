<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * CommissionPolicy class
 *
 * @property string $name Name
 * @property string $type type
 * @property integer $receive_times receive_times
 * @property integer $level Level
 * @property string $description Description
 * @property float $commission_level_1 Commission Level 1
 * @property float $commission_level_2 Commission Level 2
 * @property float $commission_level_3 Commission Level 3
 * @property float $commission_level_4 Commission Level 4
 * @property float $revenue_target revenue_target
 */
class CommissionPolicy extends Model
{
    public $table = 'commission_policies';
    public $fillable = ['name', 'type', 'receive_times', 'level', 'revenue_target', 'description', 'commission_level_1', 'commission_level_2', 'commission_level_3', 'commission_level_4'];



}
