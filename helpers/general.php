<?php

use App\Engines\Breakcrumb;
use App\Masks\Files\FileCollection;
use App\Masks\Files\FileMask;
use App\Repositories\Categories\CategoryRepository;
use App\Repositories\Locations\DistrictRepository;
use App\Repositories\Locations\RegionRepository;
use App\Repositories\Locations\WardRepository;
use App\Repositories\Sliders\SliderRepository;
use App\Repositories\Sliders\ItemRepository;
use App\Repositories\Files\FileRepository;
use App\Repositories\Metadatas\MetadataRepository;
use App\Repositories\Exams\ExamRepository;
use App\Repositories\Locations\CountryRepository;
use App\Repositories\Locations\PlaceRepository;
use App\Repositories\Locations\PlaceTypeRepository;
use App\Repositories\MBTI\DetailRepository;
use Carbon\Carbon;

$now = Carbon::now(env('APP_TIMEZONE'));
define('CURRENT_TIME_STRING', $now->toDateTimeString());
define('CURRENT_TIME_INT', strtotime(CURRENT_TIME_STRING));

if (!function_exists('get_carbon_time')) {
    /**
     * lấy thời gian theo carbon
     * @param string $datetime
     * @param string $format
     * @return string|Carbon
     */
    function get_carbon_time($time = null, $format = null)
    {
        if (!$time) return false;
        try {
            $carbon = Carbon::parse($time);
            if (!$format) return $carbon;
            switch ($format) {
                case 'date':
                    return $carbon->toDateString();
                    break;
                case 'locale':
                    return $carbon->toDateTimeLocalString();
                    break;
                case 'datetime':
                    return $carbon->toDateTimeString();
                    break;
                case 'time':
                    return $carbon->getTimestamp();
                    break;
                case 'timestamp':
                    return $carbon->getTimestamp();
                    break;
                case 'timezone':
                    return $carbon->getTimezone();
                    break;

                case 'dbdate':
                    return $carbon->format('Y-m-d');
                    break;
                case 'timetz':
                    return $carbon->format('Y-m-d\TH:i:s.uP');
                    break;


                default:
                    return $carbon->format($format);
                    break;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return null;
    }
}


if (!function_exists('json_path')) {
    /**
     * lấy dường dẫn từ thư mục json
     * @param string $path
     * @return string
     */
    function json_path($path = null)
    {
        return base_path('json') . ($path ? '/' . ltrim($path, '/') : '');
    }
}


if (!function_exists('get_gallery_data')) {
    /**
     * lấy file mà người dùng đã upload len và được lưu trong mục quản lý file
     * @param array $args
     *
     * @return FileCollection
     */
    function get_gallery_data(array $args = [])
    {
        return (new FileRepository())->get($args);
    }
}
if (!function_exists('get_gallery_items')) {
    /**
     * lấy file mà người dùng đã upload len và được lưu trong mục quản lý file
     * @param array $args
     *
     * @return FileCollection
     */
    function get_gallery_items(array $args = [])
    {
        return (new FileRepository())->mode('mask')->getData($args);
    }
}

if (!function_exists('get_client_file_uploads')) {
    /**
     * lấy file mà người dùng đạ upload len và được lưu trong mục quản lý file
     * @param array $args
     *
     * @return FileCollection
     */
    function get_client_file_uploads(array $args = [])
    {
        if (!isset($args['ref_id']) || !$args['ref_id']) return [];
        return (new FileRepository())->get($args);
    }
}

if (!function_exists('get_media_file')) {
    /**
     * lấy file mà người dùng đạ upload len và được lưu trong mục quản lý file
     * @param array $args
     *
     * @return FileMask
     */
    function get_media_file(array $args = [])
    {
        return (new FileRepository())->mode('mask')->detail($args);
    }
}

if (!function_exists('get_media_files')) {
    /**
     * lấy file mà người dùng đạ upload len và được lưu trong mục quản lý file
     * @param array $args
     *
     * @return FileMask[]
     */
    function get_media_files(array $args = [])
    {
        return app(FileRepository::class)->mode('mask')->getData($args);
    }
}



if (!function_exists('get_ref_files')) {
    /**
     * lấy file mà người dùng đạ upload len và được lưu trong mục quản lý file
     * @param array $args
     *
     * @return Collection
     */
    function get_ref_files(array $args = [])
    {
        if (!isset($args['ref_id']) || !$args['ref_id']) return [];
        $params = [];
        foreach ($args as $key => $value) {
            if (in_array($key, ['ref', 'ref_id'])) {
                $params['file_refs.' . $key] = $value;
            } else {
                $params[$key] = $value;
            }
        }
        return (new FileRepository())->join('file_refs', 'file_refs.file_id', '=', 'files.uuid')->select('files.*')->get($params);
    }
}





if (!function_exists('get_web_data')) {
    /**
     * lấy data dc luu
     * @param string $args
     * @param mixed $default
     * @return mixed
     */
    function get_web_data($key = null, $default = null)
    {
        return App\Web\Data::get($key, $default);
    }
}


if (!function_exists('set_web_data')) {
    /**
     * lưu data
     * @param string|array $args
     */
    function set_web_data($key, $value = null)
    {
        return App\Web\Data::set($key, $value);
    }
}

if (!function_exists('get_slider_active_id')) {
    /**
     * lấy dương dẫn mục nội dung
     * @return int
     */
    function get_slider_active_id()
    {
        admin_check_slider();
        return ItemRepository::getSliderID();
    }
}

if (!function_exists('admin_slider_item_url')) {
    /**
     * lấy dương dẫn mục nội dung
     * @param string $module
     * @param array $params
     * @return string
     */
    function admin_slider_item_url($module, array $params = [])
    {
        admin_check_slider();
        $url = null;
        if (isset($params['slider']) && $params['slider']) {
            $url = route('admin.sliders.items.' . $module, $params);
        } elseif ($slider = get_web_data('slider')) {
            $url = route('admin.sliders.items.' . $module, array_merge($params, [
                'slider' => $slider->id,
            ]));
        }
        return $url;
    }
}



if (!function_exists('admin_check_slider')) {
    /**
     * kiểm tra mục nội dung đã được set hay chưa
     * @param string $module
     * @param array $params
     * @return string
     */
    function admin_check_slider()
    {
        static $isCheck = false;
        static $status = false;
        if ($isCheck) return $status;
        $request = request();

        if (($slider_id = $request->route('slider')) && $slider = (new SliderRepository)->first(['id' => $slider_id, 'deleted' => 0])) {
            $isCheck = true;
            if ($slider->deleted) {
                $status = false;
            } else {
                ItemRepository::getSliderID($slider->id);
                set_web_data('slider', $slider);
                view()->share('slider', $slider);
                $isCheck = true;
                return true;
            }
        }

        return false;
    }
}





if (!function_exists('get_category')) {
    /**
     * lấy mục nội dung
     * @param array $params
     * @return \App\Models\Category
     */
    function get_category(array $params = [])
    {
        return app(CategoryRepository::class)->first(get_parse_query_args($params));
    }
}



if (!function_exists('get_categories')) {
    /**
     * lấy mục nội dung
     * @param array $params
     * @return \App\Models\Category[]
     */
    function get_categories(array $params = [])
    {
        return app(CategoryRepository::class)->getData(get_parse_query_args($params));
    }
}






if (!function_exists('check_model_data')) {
    /**
     * lấy thông tin của data theo model nào đó
     * @param string $model
     * @param int $id
     * @return bool
     */
    function check_model_data(string $model = 'post', $id = 0)
    {
        return get_web_data('model_data.' . $model . '.' . $id) ? true : false;
    }
}

if (!function_exists('get_model_data')) {
    /**
     * lấy thông tin của data theo model nào đó
     * @param string $model
     * @param int $id
     * @param bool $getifnotexists
     * @return \App\Models\Model
     */
    function get_model_data(string $model = 'post', $id = 0, $getifnotexists = true)
    {
        $list = [
            'post', 'dynamic', 'page', 'product', 'project',
            'category', 'post_category', 'project_category', 'product_category',
            'product_attribute', 'user', 'owner_user', 'course'
        ];
        if (!($data = get_web_data('model_data.' . $model . '.' . $id))) {
            if ($getifnotexists && in_array($model, $list) && function_exists($fn = 'get_' . $model)) {
                $data = call_user_func_array('get_' . $model, [['id' => $id]]);
                set_web_data('model_data.' . $model . '.' . $id, $data);
            }
        }
        return $data;
    }
}

if (!function_exists('set_model_data')) {
    /**
     * set model data để không bị query quá nhiều
     * @param string $model
     * @param int $id
     * @param mixed $data
     */
    function set_model_data(string $model = 'post', $id = 0, $data = null)
    {
        $list = [
            'post', 'dynamic', 'page', 'product', 'project',
            'category', 'post_category', 'project_category', 'product_category',
            'product_attribute', 'user', 'owner_user', 'course'
        ];
        if (in_array($model, $list)) {
            set_web_data('model_data.' . $model . '.' . $id, $data);
        }
        return $data;
    }
}





if (!function_exists('set_pagination')) {
    /**
     * thiết lập phân trang
     * @param mixed $pagination
     */
    function set_pagination($pagination)
    {
        set_web_data('__pagination', $pagination);
    }
}


if (!function_exists('get_pagination')) {
    /**
     * lấy nút phân trang
     * @param string $blade
     * @param array $args
     * @return mixed
     */
    function get_pagination($blade, $args = [])
    {
        $pagination = get_web_data('__pagination');
        if ($pagination) {
            if ($args) {
                $pagination->appends($args);
            }
            return $pagination->links($blade, $args);
        }
        return null;
    }
}




if (!function_exists('set_current')) {
    /**
     * cai dat du lieu hien tai
     * @param string $key
     * @param mixed $data
     */
    function set_current($key, $data)
    {
        set_web_data('__current__data__.' . str_slug($key), $data);
    }
}


if (!function_exists('get_current')) {
    /**
     * lấy ra data hiện tai của một đối tượng thiết lập trước đó
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_current($key, $default = null)
    {
        return get_web_data('__current__data__.' . str_slug($key), $default);
    }
}


if (!function_exists('set_active_model')) {
    /**
     * cai dat du lieu hien tai
     * @param string $key
     * @param App\Models\Model $model
     */
    function set_active_model($key, $model)
    {
        if (!check_model_data($key, $model->id)) {
            set_model_data($key, $model->id, $model);
        }
        set_web_data('__active__data__.' . str_slug($key), $model->id);
    }
}




if (!function_exists('get_active_model')) {
    /**
     * lấy ra data hiện tai của một đối tượng thiết lập trước đó
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_active_model($key, $default = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if ($id = get_web_data('__active__data__.' . str_slug($v))) {
                    $model = get_model_data($v, $id);
                    return $model ? $model : $default;
                }
            }
        } else {
            if ($id = get_web_data('__active__data__.' . str_slug($key))) {
                $model = get_model_data($key, $id);
                return $model ? $model : $default;
            }
        }

        return $default;
    }
}

if (!function_exists('get_exam')) {
    /**
     * lấy dữ liệu đề thi
     * @param array|int $args
     */
    function get_exam($args = null)
    {
        $exam = app(ExamRepository::class)->getExam($args);
        return $exam;
    }
}

if (!function_exists('get_slider')) {
    /**
     * lấy dữ liệu slider
     * @param array|int $args
     * @return \App\Masks\Sliders\SliderMask
     */
    function get_slider($args = null)
    {
        $slider = app(SliderRepository::class)->getSlider($args);
        return $slider;
    }
}
if (!function_exists('get_slider_options')) {
    /**
     * lấy dữ liệu slider
     * @param array $args
     * @return array
     */
    function get_slider_options($args = [])
    {
        return app(SliderRepository::class)->getDataOptions($args);
    }
}


if (!function_exists('get_mbti_options')) {
    /**
     * lấy dữ liệu tỉnh
     * @param array $args
     * @return array
     */
    function get_mbti_options($args = [], $defaultFirst = null, $keyVal = "mbti", $keyText = 'mbti')
    {
        /**
         * @var DetailRepository
         */
        $repo = app(DetailRepository::class);
        return $repo->getDataOptions($args, $defaultFirst, $keyVal, $keyText);
    }
}

if (!function_exists('get_country_options')) {
    /**
     * lấy dữ liệu tỉnh
     * @param array $args
     * @return array
     */
    function get_country_options($args = [], $defaultFirst = "Chọn Tỉnh / Thành phố", $keyVal = "uuid", $keyText = 'name')
    {
        /**
         * @var CountryRepository
         */
        $repo = app(CountryRepository::class);
        return $repo->getDataOptions($args, $defaultFirst, $keyVal, $keyText);
    }
}

if (!function_exists('get_region_options')) {
    /**
     * lấy dữ liệu tỉnh
     * @param array $args
     * @return array
     */
    function get_region_options($args = [], $defaultFirst = "Chọn Tỉnh / Thành phố")
    {
        /**
         * @var RegionRepository
         */
        $repo = app(RegionRepository::class);
        return $repo->getDataOptions($args, $defaultFirst, "uuid");
    }
}


if (!function_exists('get_district_options')) {
    /**
     * lấy dữ liệu huyện
     * @param array $args
     * @return array
     */
    function get_district_options($args = [], $defaultFirst = "Chọn Quận / Huyện")
    {
        return app(DistrictRepository::class)->getDataOptions($args, $defaultFirst, 'id');
    }
}
if (!function_exists('get_ward_options')) {
    /**
     * lấy dữ liệu xã
     * @param array $args
     * @return array
     */
    function get_ward_options($args = [], $defaultFirst = "Chọn Xã / Phường")
    {
        return app(WardRepository::class)->getDataOptions($args, $defaultFirst, 'id');
    }
}

if (!function_exists('get_place_type_options')) {
    /**
     * lấy dữ liệu xã
     * @param array $args
     * @return array
     */
    function get_place_type_options($args = [], $defaultFirst = null)
    {
        return app(PlaceTypeRepository::class)->orderBy('created_at', 'ASC')->getDataOptions($args, $defaultFirst, 'id', 'label');
    }
}

if (!function_exists('get_place_options')) {
    /**
     * lấy dữ liệu xã
     * @param array $args
     * @return array
     */
    function get_place_options($args = [], $defaultFirst = null)
    {
        return app(PlaceRepository::class)->orderBy('created_at', 'ASC')->getSelectOptions($args, $defaultFirst, 'id', 'name');
    }
}


if (!function_exists('get_metadatas')) {

    /**
     * lấy dữ liệu meta
     *
     * @param string $ref
     * @param string $ref_id
     * @param boolean $convert_to_gomee_object
     * @return array|\Gomee\Helpers\Arr|null
     */
    function get_metadatas($ref = 'data', $ref_id = __RANDOM_VALUE__, $convert_to_gomee_object = false)
    {
        $metadata = app(MetadataRepository::class)->getMetaMeta($ref, $ref_id);
        return $metadata && $ref_id != __RANDOM_VALUE__ && $convert_to_gomee_object ? crazy_arr($metadata) : $metadata;
    }
}




if (!function_exists('get_metadata')) {

    /**
     * lấy dữ liệu meta
     *
     * @param string $ref
     * @param int $ref_id
     * @param string $name
     * @param mixed $default
     * @param boolean $convert_to_crazy_object
     * @return array|\Gomee\Helpers\Arr|null
     */
    function get_metadata($ref = 'data', $ref_id = 0, $name = null, $default = null, $convert_to_crazy_object = false)
    {
        $metadata = app(MetadataRepository::class)->getMetaMeta($ref, $ref_id);
        if ($metadata) {
            if ($name != null)
                return array_key_exists($name, $metadata) ? $metadata[$name] : $default;
            return $convert_to_crazy_object ? crazy_arr($metadata) : $metadata;
        }
        if ($name !== null) return $default;
        if ($convert_to_crazy_object) return null;
        return [];
    }
}




if (!function_exists('text2array')) {
    /**
     * text to array key => value
     */
    function text2array($str = null)
    {
        $data = [];
        if (is_string($str)) {
            $lines = explode("\n", $str);
            if (count($lines)) {
                foreach ($lines as $line) {
                    if ($trimLine = trim($line)) {
                        $parts = explode(';', $trimLine);
                        if (count($parts)) {
                            foreach ($parts as $part) {
                                if ($p = trim($part)) {
                                    if (count($keyVal = explode(',', $p))) {
                                        foreach ($keyVal as $st) {
                                            $data[] = $st;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return array_filter(array_map('trim', $data), function ($s) {
            return strlen($s) > 0;
        });
    }
}




if (!function_exists('smart_compare')) {
    /**
     * so sánh hai giá trị
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $operator
     * @return boolean
     */
    function smart_compare($value1, $value2, $operator = '='): bool
    {
        $operatorList = ['=', '==', '!', '!=', '<>', '<', '<=', '>', '>=', 'in', 'notin', 'not'];
        if (is_string($value2) && in_array($v = strtolower($value2), $operatorList) && (!is_string($operator) || !in_array($o = strtolower($operator), $operatorList))) {
            $value2 = $operator;
            $operator = $v;
        } else {
            $operator = strtolower($operator);
        }
        if (is_array($value2)) {
            if (in_array($operator, ['!', '!=', 'not', 'notin', '<>', '>']))
                return !in_array($value1, $value2);
            return in_array($value1, $value2);
        }
        switch ($operator) {
            case '<':
                return $value1 < $value2;
                break;
            case '<=':
                return $value1 <= $value2;
                break;
            case '>':
                return $value1 > $value2;
                break;
            case '>=':
                return $value1 >= $value2;
                break;
            case '!':
            case '!=':
            case '<>':
            case 'not':
                return $value1 != $value2;
                break;
            default:
                return $value1 == $value2;
                break;
        }

        return $value1 == $value2;
    }
}
