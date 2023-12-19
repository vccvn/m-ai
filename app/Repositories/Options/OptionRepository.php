<?php

namespace App\Repositories\Options;
use Gomee\Repositories\BaseRepository;
use Illuminate\Support\Str;
use Gomee\Helpers\Arr;
use Gomee\Files\Filemanager;

class OptionRepository extends BaseRepository
{
    /**
     * trinh quan ly file
     *
     * @var \Gomee\Files\Filemanager
     */
    public $filemanager;
    /**
     * option group
     *
     * @var \App\Repositories\Options\GroupRepository
     */
    public $groupRepository;
    /**
     * option data
     *
     * @var \App\Repositories\Options\DataRepository
     */
    public $dataRepository;

    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    // protected $validatorClass = 'App\Validators\Options\OptionValidator';
    /**
     * @var string $resourceClass
     */
    protected $resourceClass = 'OptionResource';
    /**
     * @var string $collectionClass
     */
    protected $collectionClass = 'OptionCollection';


    public $currentGroupLabel = '';

    public $currentOptionTitle = '';

    protected $setted = false;


    protected $groupData = [];

    protected $mediaMap = [];
    protected $galleryMap = [];
    protected $groupDataGalleryMap = [];
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Option::class;
    }

    public function init(){
        $this->registerCacheMethods([
            'getData' => 'getOptionData'
        ]);
    }

    /**
     * kich hoạt data và group
     *
     * @return void
     */
    public function activeDataGroup()
    {
        if($this->setted) return true;
        $this->groupRepository = app(GroupRepository::class);
        $this->dataRepository = app(DataRepository::class);
        $this->setted = true;

    }

    /**
     * lấy thông tin các cột cần kiểm tra
     *
     * @return array
     */
    public function getCheckColimns()
    {
        $columns = ['slug', 'ref', 'ref_id'];
        // if(!$this->getOwnerID()){
        //     $columns[] = 'owner_id';
        // }
        return $columns;
    }

    /**
     * Tạo ra một bảng option nếu chưa tồn tại
     *
     * @param array $data
     *
     * @param mixed $callback
     *
     * @return \App\Models\Option
     */
    public function createDataIfNotExists(array $data = [], $callback = null)
    {
        // giá trị mặc định
        $default = ['slug' => null, 'title' => null, 'ref' => null, 'ref_id' => 0];
        $checkColumns = $this->getCheckColimns();
        // thông tim đầu vào
        $inputs = new Arr(array_merge($default, $data));
        // nếu có yêu cầu kiểm tra owner_id mà trong input không có
        // if(in_array('owner_id', $checkColumns) && !$inputs->isset('owner_id')) return null;
        // // nếu thiếu cả title và slug thì trả về false
        // else
        if(!$inputs->slug && !$inputs->title) return null;
        // nếu không có slug thì gán title cho slug
        elseif(!$inputs->slug) $inputs->slug = Str::slug($inputs->title);
        // nếu không có title thì gán slug cho title
        elseif(!$inputs->title) $inputs->title = $inputs->slug;
        // để cho chắc chắn là slug sẽ luôn đúng định dạng
        $inputs->slug = Str::slug($inputs->slug);
        // tham số để kiểm tra xem tồn tại hay không
        $params = $inputs->copy($checkColumns);
        // kiểm tra xem tồn tại hay chưa
        if(!($option = $this->first($params))){
            // nếu chưa tồn tại thì tạo mới
            $option = $this->create($inputs->all());
        }
        if(is_callable($callback)){
            $callback($option);
        }
        return $option;
    }

    /**
     * build group params
     *
     * @param Arr $params
     * @return array
     */
    public function getGroupParams($params)
    {
        if(!$params->option || !$params->group) return [];
        $optionParams = [
            'slug' => $params->option
        ];
        if($params->ref_id){
            $optionParams['ref'] = $params->ref;
            $optionParams['ref_id'] = $params->ref_id;
        }else{
            $optionParams['ref_id'] = 0;
        }
        // die(json_encode($optionParams));
        if(!($option = $this->first($optionParams))) return [];


        $this->activeDataGroup();
        if(!($group = $this->groupRepository->first(['option_id' => $option->id, 'slug' => $params->group]))) return [];
        $params->remove('option', 'ref', 'ref_id', 'group');
        return compact('option', 'group');
    }

    /**
     * lấy thông tin input và data
     * @param array $args
     *
     * @return array
     */
    public function getOptionFormData(array $args = [])
    {
        $params = new Arr($args);
        if(!count($optionGroup = $this->getGroupParams($params))) return [];
        extract($optionGroup);
        $params->merge(['group_id' => $group->id, '@order_by' => ['priority' => 'ASC']]);
        $data = [];
        $inputs = [];
        $option_title = $option->title;
        $group_label = $group->label;
        $this->currentOptionTitle = $option->title;
        $this->currentGroupLabel = $group->label;
        $listData = $this->dataRepository->get($params->all());
        // dd($listData);
        if(count($listData)){
            foreach ($listData as $oData) {
                $d = new Arr($oData->toArray());
                $d->remove('props', 'value', 'id');
                if(is_array($oData->props)){
                    $d->merge($oData->props);
                }
                $d->remove('validate', 'rules', 'messages');
                $inputs[$oData->name] = $d->all();
                $data[$oData->name] = $oData->value;
            }
        }
        return compact('inputs', 'data', 'option_title', 'group_label');
    }


    /**
     * lấy thông tin input và data
     * @param array $args
     *
     * @return array
     */
    public function getOptionItems(array $args = [])
    {

        $params = new Arr($args);
        if(!count($optionGroup = $this->getGroupParams($params))) return [];
        extract($optionGroup);

        $this->currentOptionTitle = $option->title;
        $this->currentGroupLabel = $group->label;
        $params->merge(['group_id' => $group->id, '@order_by' => ['priority' => 'ASC']]);
        return $this->dataRepository->get($params->all());
    }

    /**
     * lấy thông tin input và data
     * @param array $args
     *
     * @return array
     */
    public function getOptionItem(array $args = [])
    {

        $params = new Arr($args);
        if(!count($optionGroup = $this->getGroupParams($params))) return [];
        extract($optionGroup);

        $this->currentOptionTitle = $option->title;
        $this->currentGroupLabel = $group->label;
        $params->merge(['group_id' => $group->id, '@order_by' => ['priority' => 'ASC']]);
        return $this->dataRepository->first($params->all());
    }




    /**
     * lấy thông tin input và data
     * @param array $args
     * @param array $data Dữ liệu cần cập nhật, là một mảng kry => value
     * @return boolean
     */
    public function updateOptionData(array $args = [], array $data = []) : bool
    {
        if(!$data) return false;
        $params = new Arr($args);

        if(!count($optionGroup = $this->getGroupParams($params))) return false;
        extract($optionGroup);

        $params->merge(['group_id' => $group->id, 'name' => array_keys($data)]);
        $list = $this->dataRepository->get($params->all());
        if(count($list)){
            foreach ($list as $item) {
                if(array_key_exists($item->name, $data)){
                    $v = $data[$item->name];
                    if(is_array($v)) $v = json_encode($v);
                    $item->value = $v;
                    $item->save();
                }
            }
            return true;
        }
        return false;
    }



    /**
     * lấy thông tin input và data
     * @param array $args
     *
     * @return array
     */
    public function getOptionGroup(array $args = [])
    {

        $params = new Arr($args);
        if(!$params->option || !$params->group) return null;
        $optionParams = [
            'slug' => $params->option
        ];
        if($params->ref_id){
            $optionParams['ref'] = $params->ref;
            $optionParams['ref_id'] = $params->ref_id;
        }else{
            $optionParams['ref_id'] = 0;
        }

        if(!($option = $this->first($optionParams))) return null;
        $this->activeDataGroup();
        if($params->group){
            $params->slug = $params->group;
        }
        $params->option_id = $option->id;
        $params->remove('option', 'ref', 'ref_id', 'group');

        return $this->groupRepository->first($params->all());
    }



    /**
     * lấy thông tin input và data
     * @param array $args
     *
     * @return array
     */
    public function getOptionGroups(array $args = [])
    {

        $params = new Arr($args);
        if(!$params->option || !$params->group) return [];
        $optionParams = [
            'slug' => $params->option
        ];
        if($params->ref_id){
            $optionParams['ref'] = $params->ref;
            $optionParams['ref_id'] = $params->ref_id;
        }else{
            $optionParams['ref_id'] = 0;
        }

        if(!($option = $this->first($optionParams))) return [];
        $this->activeDataGroup();
        $params->option_id = $option->id;
        $params->remove('option', 'ref', 'ref_id', 'group');
        return $this->groupRepository->get($params->all());
    }

    /**
     * lấy thong tin data group
     *
     * @param array $args
     * @return \App\Models\Option
     */
    public function getOptionData(array $args = [])
    {
        return $this->with(['groups' => function($query){
            $query->with(['datas' => function($q){
                $q->orderBy('priority', 'ASC');
            }]);
        }])->first($args);
    }

    /**
     * lấy thong tin data group
     *
     * @param array $args
     * @return \App\Models\Option
     */
    public function getOptionList(array $args = [])
    {
        return $this->with(['groups' => function($query){
            $query->with(['datas' => function($q){
                $q->orderBy('priority', 'ASC');
            }]);
        }])->get($args);
    }

    /**
     * lấy input data theo group
     *
     * @param array $args
     * @return \App\Models\Option
     */
    public function getOptionGroupData(array $args = [])
    {
        if($args){
            return $this->with([
                'groups' => function($query){
                    $query->with('datas');
                }
            ])->first($args);
        }
        return null;
    }

    /**
     * kiểm tra theme có option hay ko
     *
     * @param integer $theme_id
     * @return boolean
     */
    public function hasThemeOption($theme_id)
    {
        $total = $this->join('option_groups', 'option_groups.option_id', '=', 'options.id')
                        ->count(['ref'=>'theme', 'ref_id' => $theme_id]);
        return $total > 0;
    }


    public function getClientOptions($groups = [])
    {
        $options = $this->with(['groups' => function($query){
            $query->with(['datas' => function($q){
                $q->orderBy('priority', 'ASC');
            }]);
        }])->where(function($query) use($groups){
            $t = 0;
            $a = ['slug', 'ref', 'ref_id'];
            foreach ($groups as $optParam) {
                if(!$t){
                    $query->where(function($q) use($a, $optParam){
                        foreach ($a as $key) {
                            if(array_key_exists($key, $optParam)){
                                $q->where($key, $optParam[$key]);
                            }
                        }
                    });
                }else{
                    $query->orWhere(function($q) use($a, $optParam){
                        foreach ($a as $key) {
                            if(array_key_exists($key, $optParam)){
                                $q->where($key, $optParam[$key]);
                            }
                        }
                    });
                }
                $t++;
            }
        })->get();
        $this->analyticOptions($options);
        return $options;
    }

    public function analyticOptions($options)
    {
        foreach ($options as $i => $option) {
            if($option->groups && count($option->groups)){
                foreach ($option->groups as $group) {
                    if($group->datas && count($group->datas)){
                        foreach ($group->datas as $data) {
                            $this->analyticOptionData($data, $group, $option);
                        }
                    }
                }
            }
        }
        $this->mergeData();
        $this->groupData = [];
        $this->mediaMap = [];
        $this->galleryMap = [];
        $this->groupDataGalleryMap = [];
    }


    public function mergeData()
    {
        if (count($this->mediaMap) || count($this->galleryMap)) {
            if (count($files = get_media_files(['id' => array_merge(array_keys($this->mediaMap), array_keys($this->galleryMap))]))) {
                foreach ($files as $i => $file) {
                    $url = $file->url;
                    if (array_key_exists($file->id, $this->mediaMap)) {
                        $groupDatas = $this->mediaMap[$file->id];
                        foreach ($groupDatas as $id => $names) {
                            foreach ($names as $name) {
                                $this->groupData[$id]->value = $url;
                            }
                        }
                    }
                    if (array_key_exists($file->id, $this->galleryMap)) {
                        $groupDatas = $this->galleryMap[$file->id];
                        foreach ($groupDatas as $id => $names) {
                            foreach ($names as $name) {
                                if (array_key_exists($id, $this->groupDataGalleryMap) && array_key_exists($name, $this->groupDataGalleryMap[$id])) {
                                    $this->groupDataGalleryMap[$id][$name][] = $url;
                                }
                            }
                        }
                    }
                }
            }
            if (count($this->groupDataGalleryMap)) {
                foreach ($this->groupDataGalleryMap as $id => $data) {
                    foreach ($data as $name => $value) {
                        $this->groupData[$id]->value = $value;
                    }
                }
            }
        }
    }


    public function analyticOptionData($data, $group, $option)
    {
        $this->groupData[$data->id] = $data;
        $_val = null;
        // kiểm tra và ep kiểu dử liệu
        $name = $data->name;
        $value = $data->value;
        $value_type = $data->value_type;

        if ($data->type == 'file' && $value) {
            $path = content_path('options/' . $option->slug . '/' . $group->slug . '/' . $value);
            if (file_exists(public_path($path))) {
                $_val = asset($path);
            } else {
                $_val = asset('static/images/default.png');
            }
        } elseif ($data->type == 'media' && $value) {
            if (!array_key_exists($value, $this->mediaMap)) {
                $this->mediaMap[$value] = [
                    $data->id => [$name]
                ];
            } elseif (!array_key_exists($data->id, $this->mediaMap[$value])) {
                $this->mediaMap[$value][$data->id] = [$name];
            } elseif (!in_array($name, $this->mediaMap[$value][$data->id])) {
                $this->mediaMap[$value][$data->id][] = $name;
            }
        }elseif ($data->type == 'gallery') {
            $g =  is_array($value) ? $value : json_decode($value, true);
            foreach ($g as $id) {
                if (!array_key_exists($id, $this->galleryMap)) {
                    $this->galleryMap[$id] = [
                        $data->id => [$name]
                    ];
                } elseif (!array_key_exists($data->id, $this->galleryMap[$id])) {
                    $this->galleryMap[$id][$data->id] = [$name];
                } elseif (!in_array($name, $this->galleryMap[$id][$data->id])) {
                    $this->galleryMap[$id][$data->id][] = $name;
                }
            }
            if (!array_key_exists($data->id, $this->groupDataGalleryMap)) {
                $this->groupDataGalleryMap[$data->id] = [
                    $name => []
                ];
            } else {
                $this->groupDataGalleryMap[$data->id][$name] = [];
            }

        }  elseif ($data->type == 'checklist' && !is_array($value)) {
            $_val = json_decode($value, true);
        } elseif ($data->type == 'checktree' && !is_array($value)) {
            $_val = json_decode($value?$value:'', true);
        } elseif ($data->type == 'number' || $value_type == 'number') {
            $_val = to_number($value);
        } elseif ($value_type == 'boolean') {
            $_val = ($value && $value !== '0' && $value !== 0  && $value !== 'false') ? true : false;
        } else {
            $_val = $value;
        }
        $data->value = $_val;
        // $groupData[$data->name] = $val;
    }

}
