<?php

namespace App\Web;

use Gomee\Helpers\Arr;

class Data
{
    /**
     * chứa tất cả cá loại data
     *
     * @var \Gomee\Helpers\Arr
     */
    protected static $data = null;

    /**
     * kiểm tra xem biến data được set chưa
     * nếu chưa thì set mới
     *
     * @return void
     */
    public static function check()
    {
        if(!static::$data){
            static::$data = new Arr();
        }
    }
    /**
     * set data
     * @param string|array $key
     * @param mixed $value
     * 
     * @return boolean
     */
    public static function set($key, $value = null)
    {
        static::check();
        static::$data->set($key, $value);
        return true;
    }

    /**
     * lấy dữ liệu
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        static::check();
        return static::$data->get($key, $default);
    }

    public static function all()
    {
        static::check();
        return static::$data->all();
    }
    
    public static function addHtmlPlugin($type, $file){
        $plugins = static::get('___html___plugins___', ['css' => [], 'javascript' => []]);
        if(!array_key_exists($type, $plugins)) $plugins[$type] = [];
        if(!in_array($file, $plugins[$type]))$plugins[$type][] = $file;
        static::set('___html___plugins___', $plugins);
    }
    public static function getHtmlPlugins($type = null)
    {
        $plugins = static::get('___html___plugins___', ['css' => [], 'javascript' => []]);
        if($type) return array_key_exists($type, $plugins) ? $plugins[$type] : [];
        return $plugins;
    }
    
}
