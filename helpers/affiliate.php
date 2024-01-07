<?php

use App\Models\User;
use App\Repositories\Accounts\AgentRepository;
use App\Repositories\Accounts\WalletRepository;
use App\Repositories\Policies\CommissionRepository;

if(!function_exists('get_agent_policy_options')){
    function get_agent_policy_options(){
        $repo = new CommissionRepository();
        return $repo->getDataOptions(['type' => 'agent', '@orderBy'=>['level', 'ASC']]);
    }
}

if(!function_exists('get_agent_policy')){
    function get_agent_policy($id){
        static $containers = [];
        if(!array_key_exists($id, $containers)){
            if($policy = app(CommissionRepository::class)->first(['id' => $id, 'type' => User::AGENT])){
                $containers[$id] = $policy;
            }
            else{
                return null;
            }
        }
        return $containers[$id];
    }
}

if(!function_exists('get_agent_account')){
    function get_agent_account($user_id){
        static $containers = [];
        if(!array_key_exists($user_id, $containers)){
            if($policy = app(AgentRepository::class)->with('policy')->first(['user_id' => $user_id])){
                $containers[$user_id] = $policy;
            }
            else{
                return null;
            }
        }
        return $containers[$user_id];
    }
}
if(!function_exists('get_user_wallet')){
    function get_user_wallet($user_id){
        static $containers = [];
        if(!array_key_exists($user_id, $containers)){
            if($policy = app(WalletRepository::class)->createDefaultWallet(['user_id' => $user_id])){
                $containers[$user_id] = $policy;
            }
            else{
                return null;
            }
        }
        return $containers[$user_id];
    }
}
if(!function_exists('push_to_wallet')){
    function push_to_wallet($user_id, $money = 0, $note = ''){
        static $containers = [];
        if(!array_key_exists($user_id, $containers)){
            if($policy = app(WalletRepository::class)->createDefaultWallet(['user_id' => $user_id])){
                $containers[$user_id] = $policy;
            }
            else{
                return null;
            }
        }
        return app(WalletRepository::class)->pushMoneyToWallet($user_id, $money, $note);
    }
}



if(!function_exists('get_user_service_expired')){
    function get_user_service_expired($user_id, $format = null){
        static $containers = [];
        if(!array_key_exists($user_id, $containers)){
            if($policy = get_user(['id' => $user_id])){
                $containers[$user_id] = $policy;
            }
            else{
                return false;
            }
        }
        if(!array_key_exists($user_id, $containers)){
            return false;
        }
        if(!$containers[$user_id]->expired_at) return false;
        $time = strtotime($containers[$user_id]->expired_at);
        if($time > time())
            return $format?date($format, $time):$containers[$user_id]->expired_at;
        return false;


    }
}
