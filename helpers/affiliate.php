<?php

use App\Repositories\Policies\CommissionRepository;

if(!function_exists('get_agent_policy_options')){
    function get_agent_policy_options(){
        $repo = new CommissionRepository();
        return $repo->getDataOptions(['type' => 'agent', '@orderBy'=>['level', 'ASC']]);
    }
}
