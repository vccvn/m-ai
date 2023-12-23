<?php

use App\Engines\MenuEngine;
use App\Engines\WebMenuEngine;
use App\Web\Data;
use Gomee\Engines\JsonData;
use Gomee\Html\Html;
use Gomee\Html\Menu;
use Gomee\Html\Form;
use Gomee\Html\Input;

if(!function_exists('html')){
    /**
     * tạo ra một đối tượng dom html
     * @param string $tagName thẻ html
     * @param mixed ...$params các tham số nhu content, attribute []
     */
    function html($tagName = 'div', ...$params)
    {

        return new Html($tagName, ...$params);
    }
}

if(!function_exists('html_menu')){
    /**
     * tạo ra một đối tượng dom html menu
     * @param array $data dữ liệu menu
     * @param array $options
     * @param int $level
     *
     * @return Menu
     */
    function html_menu($data, array $options = [], int $level = 0) : Menu
    {
        $menu = new Menu($data, $options, $level);
        return $menu;
    }
}

if(!function_exists('get_frontend_menu')){
    /**
     * lấy về mảng thông tin menu
     * @param string $filename
     * @return array
     */
    function get_frontend_menu($filename = null){
        // DynamicPost::check($request);
        if(!$filename) $filename = 'default';
        $menu = new WebMenuEngine('frontend/menus');
        return $menu->get($filename);

    }
}


if(!function_exists('get_web_menu')){
    /**
     * lấy về mảng thông tin menu
     * @param string $filename
     * @return array
     */
    function get_web_menu($filename = null){
        // DynamicPost::check($request);
        if(!$filename) $filename = 'default';
        $menu = new WebMenuEngine('web/menus');
        return $menu->get($filename);

    }
}


if(!function_exists('get_html_menu')){
    /**
     * tạo ra một đối tượng dom html menu
     * @param string $position vị trí menu
     * @param array $options
     * @param int $level
     *
     * @return Menu|null
     */
    function get_html_menu($position = null, array $options = [], int $level = 0) : Menu
    {
        $d = [];
        if($data = get_menu($position)){
            $d = $data;
        }
        $menu = new Menu($d, $options, $level);
        return $menu;

    }
}




if(!function_exists('get_custom_menu')){
    /**
     * lấy menu theo vị trí hoặc id
     *
     * @param array|int $position
     * @param integer $depth
     * @param array $attrs
     * @param array $options
     * @param \Closure
     * @return Menu
     */
    function get_custom_menu($position = null, $depth = 4, $attrs = [], $options = [], $action = null){
        if(!is_array($position)){
            if(is_numeric($position)){
                $a = [
                    'id' => $position,
                    'depth' => $depth
                ];
            }else{
                $a = compact('position', 'depth');

            }
            $position = $a;
        }
        elseif(!isset($position['depth']))$position['depth'] = $depth;
        // lấy menu theo
        $menu = get_html_menu($position, array_merge(
            [
                'prop_type' => 'loop',
                'props' => [

                ]
            ], $options
        ));
        $menu->attr($attrs);
        if(is_callable($action)){
            $menu->addAction($action);
        }

        return $menu;
    }
}



if(!function_exists('get_main_menu')){
    /**
     * lấy thông tin menu chính
     * @param array $options
     * @param int $level
     *
     * @return Menu
     */
    function get_main_menu(array $options = [], int $level = 0) : Menu
    {
        return get_html_menu('main', $options, $level);
    }
}

if(!function_exists('get_primary_menu')){
    /**
     * lấy thông tin menu  chính
     * @param array $options
     * @param int $level
     *
     * @return Menu
     */
    function get_primary_menu(array $options = [], int $level = 0){
        return get_html_menu('primary', $options, $level);
    }
}






if(!function_exists('html_form')){
    /**
     * tạo ra một đối tượng dom html form
     * @param array $data dữ liệu form
     * @param array $options
     * @param array $attrs
     *
     * @return Form
     */
    function html_form($data, array $options = [], array $attrs = []) : Form
    {
        $form = new Form($data, $options, $attrs);
        return $form;
    }
}

if(!function_exists('html_input')){
    /**
     * tạo ra một đối tượng dom html input
     * @param array $data dữ liệu input
     *
     * @return Input
     */
    function html_input($data) : Input
    {
        $input = new Input($data);
        return $input;
    }
}



if(!function_exists('get_register_form')){
    /**
     * tạo ra một đối tượng dom html form
     * @param array $data dữ liệu input
     *
     * @return Form
     */
    function get_register_form(array $config = []) : Form
    {
        $a = json_decode(file_get_contents(json_path('clients/forms/register.json')), true);
        $form = new Form([
            'inputs' => $a && isset($a['inputs']) ? $a['inputs'] : [],
            'errors' => request()->session()->get('errors')
        ], $config);
        $form->password->value = "";
        $form->password_confirmation->value = "";
        // dd($form);
        return $form;
    }
}

if(!function_exists('get_login_form')){
    /**
     * tạo ra một đối tượng dom html form
     * @param array $data dữ liệu input
     *
     * @return Form
     */
    function get_login_form(array $config = []) : Form
    {
        $a = json_decode(file_get_contents(json_path('clients/forms/login.json')), true);

        $form = new Form([
            'inputs' => $a && isset($a['inputs']) ? $a['inputs'] : [],
            'errors' => request()->session()->get('errors')
        ], $config);
        $form->password->value = "";

        return $form;
    }
}


if(!function_exists('parse_html_style')){
    function parse_html_style($str, $convert_to_obj = false){
        $data = [];
        $lines = explode(';', $str);
        if($lines){
            foreach ($lines as $line) {
                $a = trim($line);
                if(count($b = explode(':', $a)) > 1){
                    $attr = trim(array_shift($b));
                    if($attr){
                        $value = trim(implode(':', $b));
                        $data[$attr] = $value;
                    }
                }
            }
        }
        return $data?( $convert_to_obj?crazy_arr($data): $data): [];
    }
}


if(!function_exists('js_data')){
    /**
     * thêm biến để dùng cho js
     * @param string $name
     * @param string|array $key
     * @param mixed $value
     * @return null|array
     */
    function js_data($name = null, $key = null, $value = '<!-- DEFAULT -->'){
        static $data = [];

        if(!$name || !preg_match('/^[A-z]+[A-z0-9_\$\.]*$/i', $name)) return $data;

        if(!isset($data[$name])) $data[$name] = [];

        if(is_array($key)){
            $data[$name] = array_merge($data[$name], $key);
        }elseif($value !== '<!-- DEFAULT -->'){
            $data[$name][$key] = $value;
        }else{
            $data[$name][] = $key;
        }
    }
}

if(!function_exists('get_js_data')){
    /**
     * @param string $name ten bien
     * @param string $key
     * @return mixed
     */
    function get_js_data($name = null, $key = null){
        $data = js_data();
        if(!$name) return $data;
        if(isset($data[$name])){
            if(!is_null($key)) return isset($data[$name][$key])?$data[$name][$key]:null;
            return $data[$name];
        }
        return null;
    }
}

if(!function_exists('add_js_data')){
    /**
     * thêm biến để dùng cho js
     * @param string $name
     * @param string|array $key
     * @param mixed $value
     *
     */
    function add_js_data($name, $key = null, $value = '<!-- DEFAULT -->'){
        if($name && !is_null($key)){
            return js_data($name, $key, $value);
        }
    }
}

if(!function_exists('js_src')){
    /**
     * them nguon js
     * @param string|array
     *
     * @return array
     */
    function js_src($src = null){
        static $list = [];
        if($src){
            if(!is_array($src)){
                $src = [$src];
            }
            foreach($src as $s){
                if(preg_match('/^(http\:\/\/|https\:\/\/|\/\/)/i', $s)){
                    $r = $s;
                }else{
                    $r = asset($s);
                }
                if(!in_array($r, $list)){
                    $list[] = $r;
                }
            }
            return true;
        }
        return $list;
    }
}

if(!function_exists('get_js_src')){
    /**
     * lay danh sach nguon js
     * @return array
     */
    function get_js_src(){
        return js_src();
    }
}

if(!function_exists('add_js_src')){
    /**
     * them danh sach nguon js
     * @param array|string $src
     * @return array
     */
    function add_js_src(...$src){
        if(is_array($src) && count($src)){
            foreach ($src as $s) {
                js_src($s);
            }
        }
    }
}




if(!function_exists('css_link')){
    /**
     * them link
     * @param string|array
     *
     * @return array
     */
    function css_link($link = null){
        static $list = [];
        if($link){
            if(!is_array($link)){
                $link = [$link];
            }
            foreach($link as $s){
                if(preg_match('/^(http\:\/\/|https\:\/\/|\/\/)/i', $s)){
                    $r = $s;
                }else{
                    $r = asset($s);
                }
                if(!in_array($r, $list)){
                    $list[] = $r;
                }
            }
            return true;
        }
        $a = $list;
        $list = [];
        return $a;
    }
}

if(!function_exists('get_css_link')){
    /**
     * lay danh sach css link
     * @return array
     */
    function get_css_link(){
        return css_link();
    }
}

if(!function_exists('add_css_link')){
    /**
     * them danh sach link
     * @param array|string $link
     * @return array
     */
    function add_css_link(...$link){
        if(is_array($link) && count($link)){
            foreach ($link as $s) {
                css_link($s);
            }
        }
    }
}

if(!function_exists('is_url')){
    /**
     * kiểm tra xem có phải là url hay ko
     * @param string $str
     *
     * @return boolean
     */
    function is_url($str = null)
    {
        if(is_string($str)){
            if(preg_match('/^(http|https):\/\//i', $str)) return true;
        }
        return false;
    }
}

if(!function_exists('get_result_blade_vars')){
    /**
     * lấy các biến tromg result view
     * @param string $item_name tên mục
     * @param string $list_type ví dụ: default / active / trash
     */
    function get_result_blade_vars($item_name, $list_type = 'default'){
        $list_config = [
            'default' => [
                'title' => 'Danh sách '.$item_name,
                'btn_class' => 'btn-move-to-trash',
                'tooltip' => 'Xóa tạm thời',
            ],
            'trash' => [
                'title' => 'Danh sách '.$item_name.' đã xóa',
                'btn_class' => 'btn-delete',
                'tooltip' => 'Xóa vĩnh viễn',
            ],
        ];
        $list_type = $list_type == 'trash' ? $list_type : 'default';
        $title = $list_config[$list_type]['title'];
        $btn_class = $list_config[$list_type]['btn_class'];
        $btn_tooltip = $list_config[$list_type]['tooltip'];
        return compact('list_config', 'list_type', 'title', 'btn_class', 'btn_tooltip');

    }
}


if(!function_exists('add_html_plugin')){
    /**
     * theme html plugin
     * @param string $type
     * @param string $file
     *
     * @return mixed
     */
    function add_html_plugin($type, $file)
    {
        return Data::addHtmlPlugin($type, $file);
    }
}
if(!function_exists('get_html_plugins')){
    /**
     * get html plugin
     * @param string $type
     *
     * @return array
     */
    function get_html_plugins($type)
    {
        return Data::getHtmlPlugins($type);
    }
}


if(!function_exists('is_support_template')){

    function is_support_template($template, $type){
        return Input::checkSupportTemplate($template, $type);
    }
}


if(!function_exists('add_tinymce_assets')){

    function add_tinymce_assets(){
        add_js_src('static/plugins/tinymce/tinymce.min.js');
        add_js_src('static/plugins/tinymce/jquery.tinymce.min.js');
        add_js_src('static/plugins/tinymce/gallery.js');



        add_css_link('static/plugins/codemirror/lib/codemirror.css');
        add_css_link('static/plugins/codemirror/addon/hint/show-hint.css');
        add_css_link('static/plugins/codemirror/addon/fold/foldgutter.css');

        add_js_src('static/plugins/codemirror/lib/codemirror.js');

        add_js_src('static/plugins/codemirror/addon/hint/show-hint.js');
        add_js_src('static/plugins/codemirror/addon/hint/xml-hint.js');
        add_js_src('static/plugins/codemirror/addon/hint/html-hint.js');


        add_js_src('static/plugins/codemirror/addon/fold/foldcode.js');
        add_js_src('static/plugins/codemirror/addon/fold/foldgutter.js');
        add_js_src('static/plugins/codemirror/addon/fold/brace-fold.js');
        add_js_src('static/plugins/codemirror/addon/fold/xml-fold.js');
        add_js_src('static/plugins/codemirror/addon/fold/indent-fold.js');
        add_js_src('static/plugins/codemirror/addon/fold/markdown-fold.js');
        add_js_src('static/plugins/codemirror/addon/fold/comment-fold.js');

        add_js_src('static/plugins/codemirror/mode/markdown/markdown.js');


        add_js_src('static/plugins/codemirror/addon/selection/selection-pointer.js');
        add_js_src('static/plugins/codemirror/mode/xml/xml.js');
        add_js_src('static/plugins/codemirror/mode/javascript/javascript.js');
        add_js_src('static/plugins/codemirror/mode/css/css.js');
        add_js_src('static/plugins/codemirror/mode/vbscript/vbscript.js');
        add_js_src('static/plugins/codemirror/mode/php/php.js');
        add_js_src('static/plugins/codemirror/mode/htmlmixed/htmlmixed.js');


        add_js_src('static/plugins/codemirror/addon/edit/matchtags.js');
        add_js_src('static/plugins/codemirror/addon/selection/active-line.js');
        add_js_src('static/plugins/codemirror/addon/edit/matchbrackets.js');



        add_js_src('static/manager/js/tinymce.js');
        set_admin_template_data('modals', 'modal-library');

    }
}

if(!function_exists('add_ai_prompt_assets')){

    function add_ai_prompt_assets(){
        add_js_src('static/plugins/tinymce/tinymce.min.js');
        add_js_src('static/plugins/tinymce/jquery.tinymce.min.js');



        add_js_src('static/manager/js/ai-prompt.js');

    }
}



if(!function_exists('get_prefix_classname')){
    /**
     * lấy prefix class của hệ thống. dùng cho jquery hoặc css
     *
     * @return string
     */
    function get_prefix_classname()
    {
        static $class = -1;
        if($class == -1){
            $class = get_system_config('web.css.prefix_class', env('CSS_PREFIX_CLASS'));
        }
        return $class;
    }
}

if(!function_exists('parse_classname')){
    /**
     * tạo ra các class đã được thêm prefix hệ thống
     * @param array|string[]|array[] ...$classes
     * @return string
     */
    function parse_classname(...$classes)
    {
        if($classes){
            $array = [];
            foreach ($classes as $ind => $class) {
                if(is_string($class)){
                    $array[] = get_prefix_classname(). $class;
                }
                elseif(is_array($class)){
                    $a = array_values($class);
                    foreach ($a as $key => $c) {
                        if(is_string($c)) $array[] = get_prefix_classname().$c;
                    }
                }
            }
            return implode(" ", $array);
        }
        return null;
    }
}

if(!function_exists('get_js_object_name')){
    /**
     * lấy lấy ra tên đối tượng (js) để tương tác với hệ thống
     *
     * @return string
     */
    function get_js_object_name()
    {
        static $object = -1;
        if($object == -1){
            $object = get_system_config('web.js.object', env('js_object'));
        }
        return $object;
    }
}


if(!function_exists('get_mobile_detect')){
    /**
     * lấy doi tuong mobike detect
     *
     * @return Mobile_Detect
     */
    function get_mobile_detect()
    {
        static $device = null;
        if(!$device) $device = app(Mobile_Detect::class);
        return $device;
    }
}

if(!function_exists('is_mobile')){
    /**
     * kiểm tra có phải mobile hay không
     *
     * @return bool
     */
    function is_mobile(...$args)
    {
        return get_mobile_detect()->isMobile(...$args);
    }
}



if(!function_exists('is_tablet')){
    /**
     * kiểm tra có phải table hay không
     *
     * @return bool
     */
    function is_tablet(...$args)
    {
        return get_mobile_detect()->isTablet(...$args);
    }
}


if(!function_exists('is_desktop')){
    /**
     * kiểm tra có phải mobile hay không
     *
     * @return bool
     */
    function is_desktop(...$args)
    {
        $device = get_mobile_detect();
        return !$device->isMobile(...$args) && !$device->isTablet(...$args);
    }
}




if(!function_exists('get_json_form_data')){
    /**
     * lấy lấy ra tên đối tượng (js) để tương tác với hệ thống
     *
     * @return string
     */
    function get_json_form_data($path)
    {
        return app(JsonData::class)->getData($path);
    }
}


if(!function_exists('get_breadcrumb_schema_json')){
    /**
     * lấy lấy ra tên đối tượng (js) để tương tác với hệ thống
     *
     * @return string
     */
    function get_breadcrumb_schema_json()
    {
        $data = [
            "@context" => "https://schema.org/",
            "@type" => "BreadcrumbList",
            "itemListElement" => []
        ];
        $items = [];
        if($breadcrumbs = get_breadcrumbs()){
            foreach ($breadcrumbs as $i => $item) {
                $items[] = [
                    "@type" => "ListItem",
                        "position" => $i+1,
                        "name" => $item->fullText,
                        "item" => $item->url
                ];
            }
        }
        $data['itemListElement'] = $items;
        return $data;
    }
}

if(!function_exists('json_schema_encode')){
    function json_schema_encode($schema){
        $data = json_encode($schema, JSON_PRETTY_PRINT);
        return str_replace("\\/", "/", $data);
    }
}
if (!function_exists('get_schema_data')) {
    function get_schema_data()
    {
        if(!is_array($data = get_web_data('json_schema_data'))) $data = [];
        return $data;
    }
}


if (!function_exists('add_schema_data_item')) {
    function add_schema_data_item($data)
    {
        $list = get_schema_data();
        $list[] = $data;

        set_web_data('json_schema_data', $list);
    }
}


if (!function_exists('get_time_string')) {
    function get_time_string($time, $hours = true)
    {
        $h = '';
        if($hours){
            $h = (int) ($time / 3600);
            $m = (int) (($time % 3600) / 60);
        }else{
            $m = (int) ($time / 60);
        }
        $s = $time % 60;
        return ($hours && $h?$h . ':': '').($m < 10? '0'.$m:$m) . ':' . ($s < 10?'0'.$s:$s);
    }
}



if(!function_exists('htmlGetLastParent')){
    function htmlGetLastParent($wrapper)
    {
        $t = count($children = $wrapper->children());
        if ($t == 0) {
            return $wrapper;
        } else {
            $txt = str_replace('-', '', str_slug(trim(strip_tags($wrapper->innertext()))));
            $a = '';
            foreach ($children as $child) {
                $a .= str_replace('-', '', str_slug(trim(strip_tags($child->innertext()))));
            }
            if ($t > 1 || $txt != $a) {
                return $wrapper;
            }
        }
        return htmlGetLastParent($children[0]);
    }
}

if(!function_exists('htmlGetFirstDomChild')){
    function htmlGetFirstDomChild($wrapper, $filter = null)
    {
        $wrapper = htmlGetLastParent($wrapper);
        $t = count($children = $wrapper->children());
        if ($t == 0) {
            return is_callable($filter) ? ($filter($wrapper) == true? $wrapper : null) : $wrapper;
        } else {
            $txt = str_replace('-', '', str_slug(trim(strip_tags($wrapper->innertext()))));
            $a = '';
            foreach ($children as $child) {
                $a .= str_replace('-', '', str_slug(trim(strip_tags($child->innertext()))));
            }
            if ($txt != $a) {
                return is_callable($filter) ? ($filter($wrapper) == true? $wrapper : null) : $wrapper;
            }

            foreach ($children as $i => $child) {
                $a = htmlGetFirstDomChild($child, $filter);
                if($a ==-1 ) {
                    return $i > 0 ? $children[$i-1]: $a;
                }
                if($a) return $child;
            }
        }
        return null;
    }
}
