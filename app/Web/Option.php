<?php

namespace App\Web;

use Gomee\Engines\CacheEngine;
use App\Repositories\Options\OptionRepository;
use Gomee\Helpers\Arr;

class Option
{
    /**
     * mảng các group dưới dạng
     *
     * @var Arr[]
     */
    protected $groups = [];

    protected $cached = false;

    protected $cacheArgs = [];

    /**
     * khoi tao doi tuong option de truy cap cho de
     *
     * @param \App\Models\Option $Option
     */
    public function __construct($option)
    {

        $this->addOptionGroup($option);
    }

    /**
     * thêm option
     *
     * @param object $option
     * @return void
     */
    public function addOptionGroup($option)
    {
        if (count($option->groups)) {
            foreach ($option->groups as $group) {
                $groupData = [];
                // lấy thông tin các thiết lập trong mỗi nhóm
                if (count($group->datas)) {
                    foreach ($group->datas as $optData) {
                        $groupData[$optData->name] = $optData->value;
                    }
                }
                $this->groups[$group->slug] = new Arr($groupData);
            }
        }
    }

    public function get($name, $default = null)
    {
        $value = $default;
        if (strlen($name)) {
            $nameArr = explode('.', $name);
            $groupSlug = array_pop($nameArr);
            if (array_key_exists($groupSlug, $this->groups)) {
                $group = $this->groups[$groupSlug];
                if (count($nameArr)) {
                    $value = $group->getByKeyLevels($nameArr, $default);
                } else {
                    $value = $group;
                }
            }
        }
        return $value;
    }

    public function updateCache()
    {
        if (!$this->cached && $this->groups && $time = system_setting('cache_data_time', 0)) {
            CacheEngine::set('option-group-data', $this->groups, $time, $this->cacheArgs);
        }
    }
    /**
     * truy cập group
     */
    public function __get($name)
    {
        return $this->get(str_slug($name));
    }

    /**
     * gọi hàm mặc định
     */
    public function __call($name, $arguments)
    {
        return $this->get($name, ...$arguments);
    }
}
