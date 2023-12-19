<?php

namespace App\Repositories\Permissions;

use Gomee\Repositories\BaseRepository;
/**
 * validator 
 * 
 */
class ModuleGroupActionRepository extends BaseRepository
{

    /**
     * @var \App\Models\PermissionModuleGroupAction
     */
    static $__Model__;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\PermissionModuleGroupAction::class;
    }

}