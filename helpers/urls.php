<?php

if(!function_exists('get_root_path')){
    function get_root_path($f = null){
        $path = env('HOSTING_MANAGER_PATH', '/var/www/html/users') . ($f ? '/' . $f : '');
        return $path;
    }
}

if(!function_exists('get_content_path')){
    /**
     * lay path chua id bi mat cua user
     *
     * @param string $f
     * @return void
     */
    function get_content_path($f = null, $ownor_id = 0){
        $path = env('STATIC_CONTENT_PATH', 'static/contents'). ($f ? '/' . ltrim($f, '/') : '');
        return $path;
    }
}

if(!function_exists('content_path')){
    /**
     * lay path chua id bi mat cua user
     *
     * @param string $f
     * @return void
     */
    function content_path($f = null, $ownor_id = 0){
        $path = env('STATIC_CONTENT_PATH', 'static/contents'). ($f ? '/' . ltrim($f, '/') : '');
        return $path;
    }
}



if (!function_exists('parse_query_data')) {

    /**
     * chuẩn hóa query string
     * @param string $query query or url
     * @param array $data mang cac tham so
     * @param string|array $ignore ten tham so dc bo qua
     * @return array
     */
    function parse_query_data($query = null)
    {
        $arr = [];
        if ($query && is_string($query)) {
            try {
                $a = explode('?', $query);
                if(count($a) == 2) $query = $a[1];
                parse_str($query, $d);
                if ($d) {
                    $arr = $d;
                }
            } catch (Exception $e) {
                // eo can lam gi cung dc
            }
        }
        $arr;

    }
}
if (!function_exists('parse_query_string')) {

    /**
     * chuẩn hóa query string
     * @param string $query co san
     * @param array $data mang cac tham so
     * @param string|array $ignore ten tham so dc bo qua
     * @return string
     */
    function parse_query_string($query = null, array $data = [], $ignore = null, $raw = false)
    {
        $arr = [];
        if ($query && is_string($query)) {
            try {
                parse_str($query, $d);
                if ($d) {
                    $arr = $d;
                }
            } catch (Exception $e) {
                // eo can lam gi cung dc
            }
        }

        if (is_array($data)) {
            foreach ($data as $name => $value) {
                if (!is_null($value) && (is_numeric($value) || is_string($value)) && strlen($value) > 0) {
                    $arr[$name] = $value;
                }
            }
        }
        $s = '';
        if ($arr) {
            // nếu là array
            if(is_array($ignore)){
                foreach ($arr as $n => $v) {
                    if(!in_array($n, $ignore)){
                        $s .= "$n=".($raw ? $v: urlencode($v))."&";
                    }
                }
                $s = trim($s, '&');
                return $s;
            }
            // neu la string
            elseif($ignore && isset($arr[$ignore])) unset($arr[$ignore]);
            foreach ($arr as $n => $v) {
                $s .= "$n=".($raw ? $v: urlencode($v))."&";
            }
            $s = trim($s, '&');
        }
        return $s;
    }
}

if(!function_exists('url_merge')){
    /**
     *
     * add quey string to url
     * @param string $url dung dan lien ket
     * @param array $name mang query hoac ten bien neu la bien don
     * @param string $val gia tri bien
     * @return string $url
     */

    function url_merge($url, $name = null, $val = null, $ignore = null, $raw = false){
        $u = $url;
        $r = [];
        $f = explode('?',$url);
        $q = '';
        $u = $f[0];
        if(count($f)>1){
            $q = $f[1];
        }
        if($name){
            if(is_string($name)) $r[$name] = $val;
            elseif(is_array($name)){
                foreach($name as $n => $v){
                    if(is_string($n)){
                        $r[$n] = $v;
                    }
                }
            }

        }
        if($q || $r){
            $u.='?'.parse_query_string($q, $r, $ignore, $raw);
        }
        return $u;
    }
}


if(!function_exists('url_relative')){
    /**
     * xóa link asset và thay bằng /
     * @param string $url đường dẫn tuyệt đối
     *
     * @return string trả về url
     */
    function url_relative(string $url) : string
    {
        // tìm các chứa địa chỉ trang chủ thay bằng /
        $search = rtrim(asset('/'), '/').'/';
        $replace = '/';
        $newUrl = str_replace($search, $replace, $url);
        return $newUrl;
    }
}




if(!function_exists('get_post_url')){
    /**
     * lấy dường dẩn url
     * @param \App\Models\Post|App\Transformers\PostTransformer|\Gomee\Helpers\Arr $post
     * @return string
     */
    function get_post_url($post)
    {
        // kiểm tra 1 vai trường hợp
        if($post->dynamic_slug) $dynamic_slug = $post->dynamic_slug;
        elseif ($dynamic = get_model_data('dynamic', $post->dynamic_id)) $dynamic_slug = $dynamic->slug;
        elseif($dynamic = get_dynamic(['dynamic_id' => $post->dynamic_id])){
            set_model_data('dynamic', $dynamic->id, $dynamic);
            $dynamic_slug = $dynamic->slug;
        }
        else{
            return null;
        }
        return route('web.posts.view', ['dynamic' => $dynamic_slug, 'post' => $post->slug]);
    }
}


if(!function_exists('get_post_category_url')){
    /**
     * lấy dường dẩn danh mục bài viết
     * @param \App\Models\Category|App\Transformers\PostCategoryTransformer|\Gomee\Helpers\Arr $category
     * @return string
     */
    function get_post_category_url($category)
    {
        // nếu ko phải là post category thì trả về null
        if($category->type != 'post') return null;
        // nếu kênh ko tồn tại
        if(!($dynamic = get_model_data('dynamic', $category->dynamic_id))) return null;
        // nếu không có danh mục cha
        $params = ['dynamic' => $dynamic->slug];
        $route = 'web.posts.categories.';
        if(!$category->parent_id || ($t = count($categories = $category->getTree())) < 2){
            $route .= 'view-simple';
            $params['slug'] = $category->slug;
        }
        // nếu chỉ có 2 level
        elseif($t == 2){
            $route .= 'view-child';
            $params['parent'] = $categories[0]->slug;
            $params['child'] = $category->slug;
        }
        // 2 level tro len
        else{
            $route .= 'view-'.$t.'-level';
            $arrParams = ['first', 'second', 'third', 'fourth'];
            for ($i=0; $i < $t; $i++) {
                $params[$arrParams[$i]] = $categories[$i]->slug;
            }
        }
        return route($route, $params);
    }
}


if(!function_exists('get_project_category_url')){
    /**
     * lấy dường dẩn danh mục bài viết
     * @param \App\Models\Category|
     * @return string
     */
    function get_project_category_url($category)
    {
        // nếu ko phải là post category thì trả về null
        if($category->type != 'project') return null;
        // nếu kênh ko tồn tại
        // nếu không có danh mục cha
        $params = [];
        $route = 'web.projects.categories.';
        if(!$category->parent_id || ($t = count($categories = $category->getTree())) < 2){
            $route .= 'view-simple';
            $params['slug'] = $category->slug;
        }
        // nếu chỉ có 2 level
        elseif($t == 2){
            $route .= 'view-child';
            $params['parent'] = $categories[0]->slug;
            $params['child'] = $category->slug;
        }
        // 2 level tro len
        else{
            $route .= 'view-'.$t.'-level';
            $arrParams = ['first', 'second', 'third', 'fourth'];
            for ($i=0; $i < $t; $i++) {
                $params[$arrParams[$i]] = $categories[$i]->slug;
            }
        }
        return route($route, $params);
    }
}



if(!function_exists('get_product_category_url')){
    /**
     * lấy dường dẩn danh mục bài viết
     * @param \App\Models\Category|
     * @return string
     */
    function get_product_category_url($category)
    {
        // nếu ko phải là post category thì trả về null
        if($category->type != 'product') return null;
        // nếu kênh ko tồn tại
        // nếu không có danh mục cha
        $params = [];
        $route = 'web.products.categories.';

        if(product_setting()->category_url_type == 'unique' || !$category->parent_id || ($t = count($categories = $category->getTree())) < 2){
            $route .= 'view-simple';
            $params['slug'] = $category->slug;
            // $params['id'] = $category->id;

        }
        // nếu chỉ có 2 level
        elseif($t == 2){
            $route .= 'view-child';
            $params['parent'] = $categories[0]->slug;
            $params['child'] = $category->slug;
            // $params['id'] = $category->id;
        }
        // 2 level tro len
        else{
            $route .= 'view-'.$t.'-level';
            $arrParams = ['first', 'second', 'third', 'fourth'];
            for ($i=0; $i < $t; $i++) {
                $params[$arrParams[$i]] = $categories[$i]->slug;
            }
            // $params['id'] = $category->id;
        }
        return route($route, $params);
    }
}


if(!function_exists('get_page_url')){
    /**
     * lấy dường dẩn danh mục bài viết
     * @param \App\Models\Page|App\Mask\Pages\PageMask|\Gomee\Helpers\Arr $page
     * @return string
     */
    function get_page_url($page)
    {
        // nếu ko phải là post page thì trả về null
        $params = [];
        $route = 'web.pages.';
        if(!$page->parent_id || ($t = count($pages = $page->getTree())) < 2){
            $route .= 'view-simple';
            $params['slug'] = $page->slug;
        }
        // nếu chỉ có 2 level
        elseif($t == 2){
            $route .= 'view-child';
            $params['parent'] = $pages[0]->slug;
            $params['child'] = $page->slug;
        }
        // 2 level tro len
        else{
            $route .= 'view-'.$t.'-level';
            $arrParams = ['first', 'second', 'third', 'fourth'];
            for ($i=0; $i < $t; $i++) {
                $params[$arrParams[$i]] = $pages[$i]->slug;
            }
        }
        return route($route, $params);
    }
}




if(!function_exists('get_project_url')){
    /**
     * lấy dường dẩn sản phẩm bài viết
     * @param \App\Models\Project|App\Masks\Projects\ProjectMask|\Gomee\Helpers\Arr
     * @return string
     */
    function get_project_url($project)
    {
        // nếu ko phải là post page thì trả về null
        $params = [];
        return route('web.projects.detail', ['slug' => $project->slug]);
    }
}


if(!function_exists('get_product_url')){
    /**
     * lấy dường dẩn sản phẩm
     * @param \App\Models\Product|App\Masks\Products\ProductMask|\Gomee\Helpers\Arr
     * @return string
     */
    function get_product_url($product)
    {
        if(!$product->slug) return null;
        return route('web.products.detail', ['slug' => $product->slug]);
    }
}


if(!function_exists('get_dynamic_url')){
    /**
     * lấy dường dẩn sản phẩm bài viết
     * @param \App\Models\Dynamic|App\Masks\Dynamics\DynamicMask|\Gomee\Helpers\Arr
     * @return string
     */
    function get_dynamic_url($dynamic)
    {
        // nếu ko phải là post page thì trả về null
        $params = [];
        return route('web.posts', ['dynamic' => $dynamic->slug]);
    }
}



if(!function_exists('get_course_url')){
    /**
     * lấy dường dẩn url
     * @param \App\Models\Course
     * @return string
     */
    function get_course_url($course)
    {
        return route('web.courses.detail', ['slug' => $course->slug]);
    }
}



if(!function_exists('get_lesson_url')){
    /**
     * lấy dường dẩn url
     * @param \App\Models\Lesson
     * @return string
     */
    function get_lesson_url($lesson)
    {
        // kiểm tra 1 vai trường hợp
        if($lesson->course_slug) $course_slug = $lesson->course_slug;
        elseif ($course = get_model_data('course', $lesson->course_id)) $course_slug = $course->slug;
        elseif($course = get_course(['id' => $lesson->course_id])){
            set_model_data('course', $course->id, $course);
            $course_slug = $course->slug;
        }
        else{
            return null;
        }
        return route('web.courses.lesson', ['course' => $course_slug, 'lesson' => $lesson->slug]);
    }
}

if(!function_exists('unique_multidim_array')){
    function unique_multidim_array($obj, $key) {
        $totalObjs = count($obj);
        if (is_array($obj) && $totalObjs > 0 && is_object($obj[0]) && ($key && !is_numeric($key))) {
            for ($i = 0; $i < $totalObjs; $i++) {
                if (isset($obj[$i])) {
                    for ($j = $i + 1; $j < $totalObjs; $j++) {
                        if (isset($obj[$j]) && $obj[$i]->{$key} === $obj[$j]->{$key}) {
                            unset($obj[$j]);
                        }
                    }
                }
            }
            return array_values($obj);
        } else {
            throw new Exception('Invalid argument or your array of objects is empty');
        }
    }
}


if(!function_exists('get_app_storage_path')){
    function get_app_storage_path($path = null)
    {
        $config = env('APP_STORAGE_PATH', storage_path());
        if($path) return rtrim($config, '/') . '/' . ltrim($path, '/');
        return $config;
    }
}

if(!function_exists('get_app_storage_url')){
    function get_app_storage_url($path = null)
    {
        $config = env('APP_STORAGE_URL', url(content_path()));
        if($path) return rtrim($config, '/') . '/' . ltrim($path, '/');
        return $config;
    }
}

if(!function_exists('get_app_storage_path')){
    function get_app_storage_path($path = null)
    {
        $config = env('APP_STORAGE_PATH', storage_path());
        if($path) return rtrim($config, '/') . '/' . ltrim($path, '/');
        return $config;
    }
}

if(!function_exists('get_app_content_url')){
    function get_app_content_url($path = null)
    {
        $config = env('APP_CONTENT_URL', asset(content_path()));
        if($path) return rtrim($config, '/') . '/' . ltrim($path, '/');
        return $config;
    }
}

if(!function_exists('get_app_content_url')){
    function get_app_content_path($path = null)
    {
        $config = env('APP_CONTENT_PATH', public_path(content_path()));
        if($path) return rtrim($config, '/') . '/' . ltrim($path, '/');
        return $config;
    }
}

if(!function_exists('get_url_param')){
    function get_url_param(...$keys)
    {
        if (isset($keys[0]) && is_array($keys[0])) {
            $list = $keys[0];
        } else {
            $list = $keys;
        }
        $request = request();
        foreach ($list as $key) {
            $a = $request->{$key};
            if(strlen($a) > 0 || $a!==null) return $a;
        }
        return null;
    }
}


if(!function_exists('service_asset')){
    /**
     * xóa link asset và thay bằng /
     * @param string $url đường dẫn tuyệt đối
     *
     * @return string trả về url
     */
    function service_asset($path = '') : string
    {
        return asset('static/services' . ($path?ltrim($path, '/'):''));
    }
}

if(!function_exists('sub_asset')){
    /**
     * xóa link asset và thay bằng /
     * @param string $url đường dẫn tuyệt đối
     *
     * @return string trả về url
     */
    function sub_asset($path = '') : string
    {
        return asset('static/sub-systems' . ($path?ltrim($path, '/'):''));
    }
}

