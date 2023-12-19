<?php

namespace App\Repositories\Html;

use App\Masks\Html\AreaCollection;
use App\Masks\Html\AreaMask;
use App\Models\HtmlArea;
use App\Models\HtmlComponent;
use Gomee\Engines\JsonData;
use Gomee\Helpers\Arr;
use Gomee\Repositories\BaseRepository;

class AreaRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = 'App\Validators\Themes\HtmlAreaValidator';

    protected $maskClass = AreaMask::class;
    protected $maskCollectionClass = AreaCollection::class;

    protected $areas = [];
    protected $areaMap = [];
    protected $components = [];
    protected $componentMap = [];
    protected $mediaMap = [];
    protected $galleryMap = [];
    protected $componentGalleryMap = [];


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\HtmlArea::class;
    }

    public function init()
    {
        $this->registerCacheMethods('getAreaData');
    }

    public function createDataIfNotExists($data = [], $columns = [])
    {
        $return = null;
        $param = array_copy($data, 'slug', 'ref');
        // if(isset($data['owner_id'])){
        //     $param['owner_id'] = $data['owner_id'];
        // }
        if (isset($data['ref_id'])) {
            $param['ref_id'] = $data['ref_id'];
        }
        if (!$this->count($param)) {
            $return = $this->create($data);
        }
        return $return;
    }

    public function createAreaList($list, $ref = null, $ref_id = 0)
    {
        if (is_array($list)) {
            foreach ($list as $i => $area) {
                $a = is_array($area) ? $area : ['slug' => $i, 'name' => $area];
                $a['ref'] = $ref;
                $a['ref_id'] = $ref_id;
                if (!isset($a['slug'])) $a['slug'] = $i;
                $this->createDataIfNotExists($a);
            }
        }
    }

    /**
     * create default area
     *
     * @return void
     */
    public function createDefaultArea()
    {
        $return = [];
        $engine = new JsonData();
        $htmlArea = $engine->getData('data/web/html.areas');
        $data = $htmlArea ? $htmlArea['data'] : [];
        if ($data) {
            foreach ($data as $area) {
                // $area['owner_id'] = $owner_id;
                $return[] = $this->createDataIfNotExists($area);
            }
        }
        return $return;
    }

    public function getEmbedAreas($args = [])
    {
        $data = ['default' => [], 'theme' => []];

        $areas = $this->where('ref', 'default')
            ->with([
                'embeds' => function ($query) {
                    $query->orderBy('priority', 'asc');
                }
            ])->get($args);

        if (count($areas)) {
            foreach ($areas as $area) {
                $data['default'][$area->slug] = $area;
            }
        }

        if ($theme = get_active_theme()) {
            $args = array_merge($args, [
                'ref' => 'theme',
                'ref_id' => $theme->id
            ]);
            if (count($areas = $this->with(['embeds' => function ($query) {
                $query->orderBy('priority', 'asc');
            }])->get($args))) {
                foreach ($areas as $area) {
                    $data['theme'][$area->slug] = $area;
                }
            }
        }
        return $data;
    }

    public function getClientEmbeds($args = [])
    {
        $data = [];

        $areas = $this->where('ref', 'default')->with([
            'embeds' => function ($query) {
                $query->where('status', 1)->orderBy('priority', 'asc');
            }
        ])->get($args);
        if (count($areas)) {
            foreach ($areas as $area) {
                $embeds = [];
                if (count($area->embeds)) {
                    foreach ($area->embeds as $embed) {
                        $embeds[$embed->slug] = $embed->code;
                    }
                }
                $data[str_slug($area->slug, '_')] = $embeds;
            }
        }

        if ($theme = get_active_theme()) {
            $args = array_merge($args, [
                'ref' => 'theme',
                'ref_id' => $theme->id
            ]);
            $areas = $this->with([
                'embeds' => function ($query) {
                    $query->where('status', 1)->orderBy('priority', 'asc');
                }
            ])->get($args);
            if (count($areas)) {
                foreach ($areas as $area) {
                    $embeds = [];
                    if (count($area->embeds)) {
                        foreach ($area->embeds as $embed) {
                            $embeds[$embed->slug] = $embed->code;
                        }
                    }
                    $data[str_slug($area->slug, '_')] = $embeds;
                }
            }
        }
        return $data;
    }


    public function getComponentAreas($args = [])
    {
        $data = ['default' => [], 'theme' => []];
        $areas = $this->where('ref', 'default')->with(['components' => function ($query) {
            $query->where('parent_id', 0)->with([
                'children' => function ($query) {
                    $query->with([
                        'children' => function ($query) {
                            $query->with([
                                'children' => function ($query) {
                                    $query->orderBy('priority', 'ASC');
                                }
                            ])->orderBy('priority', 'ASC');
                        }
                    ])->orderBy('priority', 'ASC');
                }
            ])->orderBy('priority', 'ASC');
        }])->get($args);

        if (count($areas)) {
            foreach ($areas as $area) {
                $data['default'][$area->slug] = $area;
            }
        }

        if ($theme = get_active_theme()) {
            $args = array_merge($args, [
                'ref' => 'theme',
                'ref_id' => $theme->id
            ]);
            $areas = $this->with(['components' => function ($query) {
                $query->where('parent_id', 0)->with([
                    'children' => function ($query) {
                        $query->with([
                            'children' => function ($query) {
                                $query->with([
                                    'children' => function ($query) {
                                        $query->orderBy('priority', 'ASC');
                                    }
                                ])->orderBy('priority', 'ASC');
                            }
                        ])->orderBy('priority', 'ASC');
                    }
                ])->orderBy('priority', 'ASC');
            }])->get($args);
            if (count($areas)) {
                foreach ($areas as $area) {
                    $data['theme'][$area->slug] = $area;
                }
            }
        }
        return $data;
    }



    /**
     * lấy một area chứa component
     *
     * @param array $args
     * @return \App\Models\HtmlArea
     */
    public function getComponentArea($args = null)
    {
        if (!$args) return null;
        return $this->with(['components' => function ($query) {
            $query->where('parent_id', 0)->with([
                'children' => function ($query) {
                    $query->with([
                        'children' => function ($query) {
                            $query->with([
                                'children' => function ($query) {
                                    $query->orderBy('priority', 'ASC');
                                }
                            ])->orderBy('priority', 'ASC');
                        }
                    ])->orderBy('priority', 'ASC');
                }
            ])->orderBy('priority', 'ASC');
        }])
            ->orderBy('ref', 'desc')->first($args);
    }



    public function getClientComponents($args = [])
    {
        $data = [];

        $areas = $this->where('ref', 'default')->with([
            'components' => function ($query) {
                $query->where('parent_id', 0)->with([
                    'children' => function ($query) {
                        $query->with([
                            'children' => function ($query) {
                                $query->with([
                                    'children' => function ($query) {
                                        $query->orderBy('priority', 'ASC');
                                    }
                                ])->orderBy('priority', 'ASC');
                            }
                        ])->orderBy('priority', 'ASC');
                    }
                ])->orderBy('priority', 'ASC');
            }
        ])->get($args);
        if (count($areas)) {
            foreach ($areas as $area) {
                $components = [];
                if (count($area->components)) {
                    foreach ($area->components as $component) {
                        $components[$component->slug] = $component->code;
                    }
                }
                $data[str_slug($area->slug, '_')] = $components;
            }
        }

        if ($theme = get_active_theme()) {
            $args = array_merge($args, [
                'ref' => 'theme',
                'ref_id' => $theme->id
            ]);
            $areas = $this->with([
                'components' => function ($query) {
                    $query->where('parent_id', 0)->with([
                        'children' => function ($query) {
                            $query->with([
                                'children' => function ($query) {
                                    $query->with([
                                        'children' => function ($query) {
                                            $query->orderBy('priority', 'ASC');
                                        }
                                    ])->orderBy('priority', 'ASC');
                                }
                            ])->orderBy('priority', 'ASC');
                        }
                    ])->orderBy('priority', 'ASC');
                }
            ])->get($args);
            if (count($areas)) {
                foreach ($areas as $area) {
                    $components = [];
                    if (count($area->components)) {
                        foreach ($area->components as $component) {
                            $components[$component->slug] = $component->code;
                        }
                    }
                    $data[str_slug($area->slug, '_')] = $components;
                }
            }
        }
        return $data;
    }


    public function getAreaData()
    {
        $areas = $this->where(function ($query) {
            $query->where('ref', 'default');
            if ($theme = get_active_theme()) {
                $query->orWhere(function ($query) use ($theme) {
                    $query->where('ref', 'theme')->where('ref_id', $theme->id);
                });
            }
        })
            ->orderBy('ref', 'ASC')
            ->with([
                'components' => function ($query) {
                    $query->where('parent_id', 0)->with([
                        'children' => function ($query) {
                            $query->with([
                                'children' => function ($query) {
                                    $query->with([
                                        'children' => function ($query) {
                                            $query->orderBy('priority', 'ASC');
                                        }
                                    ])->orderBy('priority', 'ASC');
                                }
                            ])->orderBy('priority', 'ASC');
                        }
                    ])->orderBy('priority', 'ASC');
                },
                'embeds' => function ($query) {
                    $query->where('status', 1)->orderBy('priority', 'asc');
                }
            ])->mode('mask')->getData();
        $this->analyticAreas($areas);
        $this->mergeData();


        $this->areas = [];
        $this->areaMap = [];
        $this->components = [];
        $this->componentMap = [];
        $this->mediaMap = [];
        $this->galleryMap = [];
        $this->componentGalleryMap = [];

        return $areas;
    }

    public function mergeData()
    {
        if (count($this->mediaMap) || count($this->galleryMap)) {
            if (count($files = get_media_files(['id' => array_merge(array_keys($this->mediaMap), array_keys($this->galleryMap))]))) {
                foreach ($files as $i => $file) {
                    $url = $file->url;
                    if (array_key_exists($file->id, $this->mediaMap)) {
                        $components = $this->mediaMap[$file->id];
                        foreach ($components as $id => $names) {
                            foreach ($names as $name) {
                                $this->components[$id]->data->set($name, $url);
                            }
                        }
                    }
                    if (array_key_exists($file->id, $this->galleryMap)) {
                        $components = $this->galleryMap[$file->id];
                        foreach ($components as $id => $names) {
                            foreach ($names as $name) {
                                if (array_key_exists($id, $this->componentGalleryMap) && array_key_exists($name, $this->componentGalleryMap[$id])) {
                                    $this->componentGalleryMap[$id][$name][] = $url;
                                }
                            }
                        }
                    }
                }
            }
            if (count($this->componentGalleryMap)) {
                foreach ($this->componentGalleryMap as $id => $data) {
                    foreach ($data as $name => $value) {
                        $this->components[$id]->data->set($name, $value);
                    }
                }
            }
        }
    }

    /**
     * phân tích area
     *
     * @param HtmlArea[] $areas
     * @return void
     */
    public function analyticAreas($areas)
    {
        foreach ($areas as $i => $area) {
            if ($area->components && count($area->components)) {
                $this->analyticComponents($area->components);
            }
        }
    }
    /**
     * phan tic component
     *
     * @param HtmlComponent[] $components
     * @return void
     */
    public function analyticComponents($components)
    {
        foreach ($components as $i => $component) {
            $this->analyticComponent($component);
        }
    }

    /**
     * phan tic component
     *
     * @param HtmlComponent $component
     * @return void
     */
    public function analyticComponent($component)
    {
        $inputs = is_array($component->inputs) ? $component->inputs : json_decode($component->inputs, true);
        $data = new Arr($component->data);
        $this->components[$component->id] = $component;
        if ($inputs) {
            foreach ($inputs as $key => $input) {
                $name = isset($input['name']) ? $input['name'] : $key;
                $t = isset($input['type']) ? strtolower($input['type']) : '';
                $vt = isset($input['value_type']) ? strtolower($input['value_type']) : '';
                $val = $data->get($name);
                if ($t == 'file') {
                    $data->set($name, asset(file_exists(public_path($p = content_path('themes/components/' . $val))) ? $p : 'static/images/default.png'));
                } elseif ($t == 'media' && $val) {
                    if (!array_key_exists($val, $this->mediaMap)) {
                        $this->mediaMap[$val] = [
                            $component->id => [$name]
                        ];
                    } elseif (!array_key_exists($component->id, $this->mediaMap[$val])) {
                        $this->mediaMap[$val][$component->id] = [$name];
                    } elseif (!in_array($name, $this->mediaMap[$val][$component->id])) {
                        $this->mediaMap[$val][$component->id][] = $name;
                    }

                    // if ($file = get_media_file(['id' => $val])) {
                    //     $data->set($name, $file->source);
                    // }
                    $data->set($name, null);
                } elseif ($t == 'gallery') {
                    $g =  is_array($val) ? $val : json_decode($val, true);
                    $d = [];

                    if(is_array($g)){
                        foreach ($g as $id) {
                            if (!array_key_exists($id, $this->galleryMap)) {
                                $this->galleryMap[$id] = [
                                    $component->id => [$name]
                                ];
                            } elseif (!array_key_exists($component->id, $this->galleryMap[$id])) {
                                $this->galleryMap[$id][$component->id] = [$name];
                            } elseif (!in_array($name, $this->galleryMap[$id][$component->id])) {
                                $this->galleryMap[$id][$component->id][] = $name;
                            }
                        }
                    }
                    if (!array_key_exists($component->id, $this->componentGalleryMap)) {
                        $this->componentGalleryMap[$component->id] = [
                            $name => []
                        ];
                    } else {
                        $this->componentGalleryMap[$component->id][$name] = [];
                    }
                    $data->set($name, []);
                } elseif ($t == 'checklist' && !is_array($val)) {
                    $data->set($name, json_decode($val?$val:'', true));
                } elseif ($t == 'checktree' && !is_array($val)) {
                    $data->set($name, json_decode($val, true));
                } elseif ($t == 'number' || $vt == 'number') {
                    $data->set($name, to_number($val));
                } elseif ($vt == 'boolean') {
                    $data->set($name, ($val && $val !== '0' && $val !== 0  && $val !== 'false') ? true : false);
                }
            }
        }
        $component->data = $data;
        if ($component->children && count($component->children)) {
            $this->analyticComponents($component->children);
        }
    }

    /**
     * lấy area theo slug và ref hoặc tự do. tóm lại hơi khó hiểu
     *
     * @param string $slug
     * @param string $ref
     * @param integer $ref_id
     * @return \App\Models\HtmlArea
     */
    public function getAreaBySlug($slug, $ref = null, $ref_id = 0)
    {
    }
}
