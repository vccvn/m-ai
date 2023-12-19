<?php

use App\Models\Campaign;
use App\Repositories\Products\ProductUtilityRepository;
use App\Repositories\Promotions\CampaignRepository;
use App\Repositories\Promotions\VoucherRepository;

if(!function_exists('get_campaign_options')){
    function get_campaign_options($args = []){
        $rep = new CampaignRepository();
        return $rep->getDataOptions($args, null, 'id', 'name');
    }
}

if(!function_exists('get_voucher_options')){
    function get_voucher_options($args = []){
        $rep = new VoucherRepository();
        return $rep->getDataOptions($args, null, 'id', 'name');
    }
}


if(!function_exists('get_product_utility_options')){
    function get_product_utility_options($args = []) : array {
        $rep = new ProductUtilityRepository();
        return $rep->getDataOptions($args, null, 'id', 'name');
    }
}

if(!function_exists('get_campaign_status_options')){
    /**
     * lấy trạng thái của chiến dịch
     *
     * @param array $args
     * @return array
     */
    function get_campaign_status_options($args = []) : array {
        return Campaign::getStatusOptions();
    }
}
