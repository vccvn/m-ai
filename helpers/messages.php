<?php



if (!function_exists('get_session_message')) {
    /**
     * lấy dương dẫn mục nội dung
     * @param string $module
     * @param array $params
     * @return string
     */
    function get_session_message($key = null, $value = null)
    {
        if($key){
            if($key === true) {
                $messKeys = ['warning', 'success', 'error', 'info', 'alert'];
                $data = [];
                foreach ($messKeys as $key) {
                    if($a = session($key . '_message')){
                        $data[$key . '_message'] = $a;
                    }
                }
                if($message = session('message')){
                    $data['message'] = $message;
                }
                return $data;
            }
            return session($key.'_message');
        }
        return session('message');
    }
}



