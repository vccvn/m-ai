<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionModuleGroupAction extends Model
{
    public $table = 'permission_module_group_actions';
    public $fillable = ['group_id', 'action_id'];

    public $timestamps = false;


    /**
     * Get the group that owns the PermissionModuleGroupAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionModule::class, 'group_id');
    }

    /**
     * Get the action that owns the PermissionModuleGroupAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(PermissionModule::class, 'action_id');
    }
}
