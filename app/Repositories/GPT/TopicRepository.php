<?php

namespace App\Repositories\GPT;

use Gomee\Repositories\BaseRepository;
use App\Masks\GPT\TopicMask;
use App\Masks\GPT\TopicCollection;
use App\Models\GPTTopic;
use App\Validators\GPT\TopicValidator;
use Illuminate\Http\Request;

/**
 * @method TopicCollection<TopicMask>|GPTTopic[] filter(Request $request, array $args = []) lấy danh sách GPTTopic được gán Mask
 * @method TopicCollection<TopicMask>|GPTTopic[] getFilter(Request $request, array $args = []) lấy danh sách GPTTopic được gán Mask
 * @method TopicCollection<TopicMask>|GPTTopic[] getResults(Request $request, array $args = []) lấy danh sách GPTTopic được gán Mask
 * @method TopicCollection<TopicMask>|GPTTopic[] getData(array $args = []) lấy danh sách GPTTopic được gán Mask
 * @method TopicCollection<TopicMask>|GPTTopic[] get(array $args = []) lấy danh sách GPTTopic
 * @method TopicCollection<TopicMask>|GPTTopic[] getBy(string $column, mixed $value) lấy danh sách GPTTopic
 * @method TopicMask|GPTTopic getDetail(array $args = []) lấy GPTTopic được gán Mask
 * @method TopicMask|GPTTopic detail(array $args = []) lấy GPTTopic được gán Mask
 * @method TopicMask|GPTTopic find(integer $id) lấy GPTTopic
 * @method TopicMask|GPTTopic findBy(string $column, mixed $value) lấy GPTTopic
 * @method TopicMask|GPTTopic first(string $column, mixed $value) lấy GPTTopic
 * @method GPTTopic create(array $data = []) Thêm bản ghi
 * @method GPTTopic update(integer $id, array $data = []) Cập nhật
 */
class TopicRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = TopicValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = TopicMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = TopicCollection::class;




    public static $sonLevel = 0;

    public static $maxLevel = 4;

    protected static $activeID = 0;



    public function setActiveID($id = null)
    {
        if ($id) {
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
        return \App\Models\GPTTopic::class;
    }

    public function init()
    {
        $this->setWith('parent');
        $this->setWithCount('prompts');
    }


    public function buildWithData($query = null, $level = 0)
    {
        if ($level > 4) return;
        if (!$query)
            $query = $this;
        $query->with([
            'prompts' => function ($query) {
                $query->where('trashed_status', 0);
            },
            'children' => function ($query) use ($level) {
                $this->buildWithData($query, $level + 1);
            }
        ]);
        return $this;
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
        if (static::$activeID) {
            if ($cate = $repository->find(static::$activeID)) {
                static::$sonLevel = $cate->getSonLevel();
            }
        }
        if ($categories = $repository->notTrashed()->get($args)) {
            $list = static::toParentSelectOptions($categories, $list);
        }
        return $list;
    }



    public function getTopicOptions(array $args = [], $firstDefault = null)
    {
        $a = [];
        if (count($args)) {
            foreach ($args as $key => $value) {
                if (strlen($value)) $a[$key] = $value;
            }
        }
        $list = [$firstDefault ? $firstDefault : "-- Chủ đề --"];
        $this->where('parent_id', '<', 1);
        if ($categories = $this->get($a)) {
            $list = static::toTopicSelectOptions($categories, $list);
        }
        return $list;
    }


    public function getTopicTreeOptions(array $args = [])
    {
        $a = [];
        if (count($args)) {
            foreach ($args as $key => $value) {
                if (strlen($value)) $a[$key] = $value;
            }
        }
        $list = [];
        $this->where('parent_id', '<', 1);
        if ($categories = $this->get($a)) {
            $list = static::toTopicSelectOptions($categories, $list);
        }
        return $list;
    }

    public static function getTopicSelectOptions(array $args = [], $firstDefault = null)
    {

        $repository = app(static::class);
        $repository->init();
        return $repository->getTopicOptions(array_merge(['trashed_status' => 0], $args), $firstDefault);
    }
    public static function getTopicCheckTreeOptions(array $args = [])
    {
        $repository = app(static::class);
        $repository->init();
        return $repository->getTopicTreeOptions(array_merge(['trashed_status' => 0], $args));
    }


    public static function getTopicMap(array $args = [])
    {
        $repository = app(static::class);
        $repository->init();
        return $repository->getTopicOptions($args);
    }

    protected static function toParentSelectOptions($list, $opts = [], $prefix = '')
    {
        if (count($list) > 0) {
            foreach ($list as $c) {
                $name = $prefix . $c->name;
                $cond = ($c->getLevel() + static::$sonLevel < static::$maxLevel);
                if ($c->id != static::$activeID && $cond && !count($c->prompts)) {

                    if (count($ch = $c->getChildren()) > 0) {

                        $data = static::toParentSelectOptions($ch, []);
                        $opts[$c->id] = [
                            'label' => htmlentities($name),
                            'data' => $data
                        ];
                    } else {
                        $opts[$c->id] = htmlentities($name);
                    }
                }
            }
        }
        return $opts;
    }

    protected static function toTopicSelectOptions($list, $opts = [])
    {
        if (count($list) > 0) {
            foreach ($list as $c) {
                $name = $c->name;
                if (count($ch = $c->getChildren())) {
                    $data = static::toTopicSelectOptions($ch, []);
                    $opts[$c->id] = [
                        'label' => htmlentities($name),
                        'data' => $data
                    ];
                } else {
                    $opts[$c->id] = htmlentities($name);
                }
            }
        }
        return $opts;
    }
}
