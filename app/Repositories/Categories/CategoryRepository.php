<?php

namespace App\Repositories\Categories;

use Gomee\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = 'App\Validators\Categories\CategoryValidator';

    /**
     * @var string $resource
     */
    protected $resourceClass = 'App\Http\Resources\CategoryResource';

    /**
     * @var string $collectionClass
     */
    protected $collectionClass = 'App\Http\Resources\CategoryCollection';

    /**
     * @var string $system
     */
    protected $system = 'both';


    /**
     * @var int $dynamic_id id muc noi dung
     */
    protected static $dynamic_id = 0;

    
    
    public static $sonLevel = 0;

    public static $maxLevel = 4;
    
    protected static $activeID = 0;

    

    public function setActiveID($id = null)
    {
        if($id){
            static::$activeID = $id;
        }
    }

    public function getActiveID()
    {
        return static::$activeID;
    }
    
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Category::class;
    }

    /**
     * get list id of parents
     *
     * @param int $id
     * @return int[]
     */
    public function getTree($id)
    {
        $cate = $this->find($id);
        if($cate) return $cate->getMap();
        return [];
    }

    /**
     * lấy thông tin max level
     * @return int
     */
    public function getMaxLevel()
    {
        return static::$maxLevel;
    }
    
    /**
     * lấy level con của một danh muc
     * @param int $id id vủa danh mục
     * @return int
     */
    public function getSonLevel($id)
    {
        if($cate = $this->findBy('id', $id)){
            return $cate->getSonLevel();
        }
        return 0;
    }
   
    /**
     * lay danh sach cha
     * @param integer $maxLevel level cao nhat cua 1 danh muc
     * 
     * @return Collection
     */
    public static function getParentSelectOptions($maxLevel = 2, $args = [])
    {
        static::$maxLevel = $maxLevel;
        $list = ["Không"];
        // dd(static::class);
        $repository = app(static::class);
        $repository->init();
        $repository->where('parent_id', '<', 1);
        if(static::$activeID){
            if($cate = $repository->find(static::$activeID)){
                static::$sonLevel = $cate->getSonLevel();
            }
            
        }
        if($categories = $repository->notTrashed()->get($args)){
            $list = static::toParentSelectOptions($categories, $list);
        }
        return $list;
    }
    protected static function toParentSelectOptions($list, $opts = [], $prefix='')
    {
        if(count($list)>0){
            foreach($list as $c){
                $name = $prefix.$c->name;
                $cond = ($c->getLevel() + static::$sonLevel < static::$maxLevel);
                if($c->id != static::$activeID && $cond && !$c->hasPost()){
                    
                    if(count($ch = $c->getChildren())>0){
                        
                        $data = static::toParentSelectOptions($ch,[]);
                        $opts[$c->id] = [
                            'label' => htmlentities($name),
                            'data' => $data
                        ];
                    }else{
                        $opts[$c->id] = htmlentities($name);
                    }
                }
            }
        }
        return $opts;
    }


    public function getCategoryOptions(array $args = [])
    {
        $a = [];
        if(count($args)){
            foreach ($args as $key => $value) {
                if(strlen($value)) $a[$key] = $value;
            }
        }
        $list = ["-- Danh mục --"];
        $this->where('parent_id', '<', 1);
        if($categories = $this->get($a)){
            $list = static::toCategorySelectOptions($categories, $list);
        }
        return $list;
    }


    public function getCategoryTreeOptions(array $args = [])
    {
        $a = [];
        if(count($args)){
            foreach ($args as $key => $value) {
                if(strlen($value)) $a[$key] = $value;
            }
        }
        $list = [];
        $this->where('parent_id', '<', 1);
        if($categories = $this->get($a)){
            $list = static::toCategorySelectOptions($categories, $list);
        }
        return $list;
    }

    public static function getCategorySelectOptions(array $args = [])
    {

        $repository = app(static::class);
        $repository->init();
        return $repository->getCategoryOptions(array_merge(['trashed_status' => 0], $args));
    }
    public static function getCategoryCheckTreeOptions(array $args = [])
    {
        $repository = app(static::class);
        $repository->init();
        return $repository->getCategoryTreeOptions(array_merge(['trashed_status' => 0], $args));
    }

    
    public static function getCategoryMap(array $args = [])
    {
        $repository = app(static::class);
        $repository->init();
        return $repository->getCategoryOptions($args);
    }
    protected static function toCategorySelectOptions($list, $opts = [])
    {
        if(count($list)>0){
            foreach($list as $c){
                $name = $c->name;
                if(count($ch = $c->getChildren())){
                    $data = static::toCategorySelectOptions($ch,[]);
                    $opts[$c->id] = [
                        'label' => htmlentities($name),
                        'data' => $data
                    ];
                }else{
                    $opts[$c->id] = htmlentities($name);
                }
            }
        }
        return $opts;
    }



}