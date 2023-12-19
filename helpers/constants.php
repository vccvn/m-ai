<?php

if(!defined('MODEL_PRIMARY_KEY')) {
    define('MODEL_PRIMARY_KEY', env('MODEL_PRIMARY_KEY', 'id'));
}
if(!defined('APP_ASSET_VERSION')) {
    define('APP_ASSET_VERSION', env('APP_ASSET_VERSION', '4.0.1.0'));
}

if(!function_exists('get_app_version')){
    function get_app_version()
    {
        return APP_ASSET_VERSION;
    }
}
