<?php

namespace App\Repositories\Pages;

use App\Masks\Pages\PageCollection;
use App\Masks\Pages\PageMask;
use Gomee\Repositories\BaseRepository;
use App\Repositories\Metadatas\MetadataRepository;
use Gomee\Helpers\Arr;
use Illuminate\Http\Request;

class PageRepository extends BaseRepository
{


    /**
     * tên class mặt nạ. Thược có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = PageMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = PageCollection::class;
    /**
     * @var string $system
     */
    protected $system = 'both';

    /**
     * @var MetadataRepository $metadataRepository
     */
    public $metadataRepository;



    public static $sonLevel = 0;

    public static $maxLevel = 2;

    protected static $activeID = 0;



    public function setActiveID($id = null)
    {
        if($id){
            self::$activeID = $id;
        }
    }

    public function getActiveID()
    {
        return self::$activeID;
    }

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Page::class;
    }

    /**
     * thiết lập
     * @return void
     */
    public function init()
    {
        $this->metadataRepository = new MetadataRepository();
        $this->addDefaultParam('type', 'type', 'page');
        $this->addDefaultValue('type', 'page');
        $this->setJoinable([
            ['leftJoin', 'posts AS pages', 'pages.id', '=', 'posts.parent_id']
        ])->setSelectable(['posts.*', 'parent_title'=>'pages.title']);

        $this->registerCacheMethod('getPageDetail');
    }


    public function searchOptions(Request $request, $args = [])
    {
        $this->buildSearch($request);
        return $this->getPageOptions($args);
    }


    /**
     * lay danh sach cha
     * @param integer $maxLevel level cao nhat cua 1 danh muc
     *
     * @return Collection
     */
    public static function getParentSelectOptions($maxLevel = 2, $args = [])
    {
        self::$maxLevel = $maxLevel;
        $list = ["Không"];
        $repository = app(static::class);
        $repository->where('parent_id',0);
        if(self::$activeID){
            if($page = $repository->find(self::$activeID)){
                self::$sonLevel = $page->getSonLevel();
            }

        }
        if($pages = $repository->notTrashed()->get($args)){
            $list = self::toParentSelectOptions($pages, $list);
        }
        return $list;
    }
    protected static function toParentSelectOptions($list, $opts = [], $prefix='')
    {
        if(count($list)>0){
            foreach($list as $c){
                $name = $prefix.$c->title;
                $cond = ($c->getLevel() + self::$sonLevel < self::$maxLevel);
                if($c->id != self::$activeID && $cond){

                    if(count($ch = $c->getChildren())>0){

                        $data = self::toParentSelectOptions($ch,[]);
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

    public function getPageOptions(array $args = [])
    {
        $a = [];
        if(count($args)){
            foreach ($args as $key => $value) {
                if(strlen($value)) $a[$key] = $value;
            }
        }
        $list = ["-- Trang --"];
        $this->notTrashed()->where('parent_id', 0);
        if($categories = $this->get($a)){
            $list = static::toPageSelectOptions($categories, $list);
        }
        return $list;
    }

    public static function getPageSelectOptions(array $args = [])
    {
        return app(static::class)->getPageOptions($args);
    }
    protected static function toPageSelectOptions($list, $opts = [])
    {
        if(count($list)>0){
            foreach($list as $c){
                $name = $c->title;

                if(count($ch = $c->getChildren())){
                    $data = self::toPageSelectOptions($ch,[]);
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
    /**
     * lấy chi tiết bài viết
     *
     * @param array $args
     * @return \App\Models\Page|\App\Masks\Pages\PageMask
     */
    public function getPageDetail(array $args = [])
    {
        if(!$args) return null;
        $data = $this->with(['metadatas'])->first($args);
        return $this->parseDetail($data);
    }


}
