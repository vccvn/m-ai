<?php

namespace App\Repositories\Metadatas;



use Gomee\Repositories\BaseRepository;


use Illuminate\Support\Facades\Schema;

use Gomee\Helpers\Arr;
use Illuminate\Support\Facades\DB;

class MetadataRepository extends BaseRepository
{
    protected static $refMetas = [];

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Metadata::class;
    }

    /**
     * lấy data theo ref, ref_id
     * @param string $ref
     * @param int $ref_id
     * @return mixed
     */
    public function getMetaMeta(string $ref, $ref_id = __RANDOM_VALUE__, $name = null)
    {
        $args = [];
        $notSet = false;
        if (!isset(self::$refMetas[$ref])) {
            $args['ref'] = $ref;
            if ($ref_id != __RANDOM_VALUE__) $args['ref_id'] = $ref_id;
            $notSet = true;
        } elseif (!isset(self::$refMetas[$ref][$ref_id])) {
            if ($ref_id != __RANDOM_VALUE__) $args['ref_id'] = $ref_id;
            $notSet = true;
        }
        if ($notSet) {
            if ($list = $this->get($args)) {

                foreach ($list as $data) {
                    if (!isset(self::$refMetas[$data->ref])) self::$refMetas[$data->ref] = [];
                    if (!isset(self::$refMetas[$data->ref][$data->ref_id])) self::$refMetas[$data->ref][$data->ref_id] = [];
                    if($data->ref == 'json') $data->value = $data->value ? json_decode($data->value, true):[];
                    self::$refMetas[$data->ref][$data->ref_id][$data->name] = $data->value;
                }
            }
        }

        $data = [];
        if ($ref) {

            if (isset(self::$refMetas[$ref])) {
                $data = self::$refMetas[$ref];
                if ($ref_id == __RANDOM_VALUE__) {
                    // khong can lam gi
                }
                elseif (isset($data[$ref_id])) {
                    $data = $data[$ref_id];
                    if ($name) {
                        if (isset($data[$name])) {
                            $data = $data[$name];
                        } else {
                            $data = null;
                        }
                    }
                } else {
                    $data = [];
                }
            }
        }
        return $data;
    }

    public function getJson($name, $ref_id = 0)
    {
        $args = [
            'ref' => 'json',
            'name' => $name,
            'ref_id' => $ref_id
        ];

        if($meta = $this->first($args)){
            if($a = json_decode($meta->value, true)) return $a;
        }
        return [];
    }

    public function saveJson($name, $value = [], $ref_id = 0)
    {
        return $this->saveOne('json', $ref_id, $name, $value);
    }

    /**
     * lấy các bảng được hỗ trợ
     * @param string
     */
    public function getRefSupport($ref)
    {
        $ref = strtolower($ref);
        $posts = ['page', 'project'];
        if (in_array($ref, $posts)) $ref = 'post';
        if(in_array($ref, $posts)) return $ref.'s';
        $tbl = null;
        if (in_array($ref, ['data','json'])) {
            return $ref;
        } elseif (Schema::hasTable($ref)) {
            $tbl = $ref;
        } elseif (Schema::hasTable($tb = \Str::plural($ref))) {
            $tbl = $tb;
        } else {
            return false;
        }
        return $tbl;
    }

    /**
     * xử lý data ta trước khi tạo mới
     * @param array $data
     */
    public function beforeCreate(array $data)
    {
        $d = new Arr($data);
        if ($d->ref && !$d->passed) {
            if (!$this->checkRef($d->ref, $d->ref_id)) return [];
        }
        return $data;
    }

    /**
     * kiểm tra tham chiếu
     * @param string $ref
     * @param int $ref_id
     *
     * @return boolean
     */
    public function checkRef($ref, $ref_id = 0)
    {
        if (in_array($ref, ['data', 'json'])) {
            return true;
        }
        if ($tb = $this->getRefSupport($ref)) {
            if (!DB::table($tb)->where('id', $ref_id)->first()) return false;
            return true;
        }
        return false;
    }


    /**
     * lưu meta
     * @param string $ref
     * @param string $ref_id
     * @param string $name
     * @param mixed $value
     * @param boolean $check_ref
     *
     * @return App\Models\Metadata
     */
    public function saveOne(string $ref = 'data', $ref_id = null, string $name = 'name', $value = null, $check_ref = true)
    {
        if ($check_ref && !$this->getRefSupport($ref)) {
            return false;
        }
        $id = null;
        if ($meta = $this->first($data = compact('ref', 'ref_id', 'name'))) $id = $meta->id;
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $data['value'] = $value;
        $data['passed'] = true;
        return $this->save($data, $id);
    }


    /**
     * lưu nhiều meta
     * @param string $ref
     * @param string $ref_id
     * @param array $metadatas
     * @param boolean $check_ref
     *
     * @return array [ \App\Models\Metadata ]
     */
    public function saveMany(string $ref = 'data', $ref_id = 0, array $metadatas = [], $check_ref = true)
    {

        if ($check_ref && !$this->checkRef($ref, $ref_id)) return [];
        $data = [];
        if (is_array($metadatas) && count($metadatas)) {
            foreach ($metadatas as $name => $value) {
                if ($meta = $this->saveOne($ref, $ref_id, $name, $value, false)) {
                    $data[] = $meta;
                }
            }
        }
        return $data;
    }

    /**
     * xóa meta
     * @param string $ref
     * @param string $ref_id
     * @param string $name
     *
     *
     * @return boolean
     */
    public function remove(string $ref, $ref_id, string $name)
    {
        if ($meta = $this->first($data = compact('ref', 'ref_id', 'name'))) {
            $data['value'] = $meta->value;
            $data['id'] = $meta->id;
            $meta->delete();
            return $data;
        }
        return false;
    }


    /**
     * xóa meta
     * @param string $ref
     * @param string $ref_id
     *
     * @return boolean
     */
    public function removeRefData(string $ref, $ref_id)
    {
        $delete = [];
        if (count($list = $this->get($data = compact('ref', 'ref_id')))) {
            foreach ($list as $meta) {
                $m = $data;
                $m['name'] = $meta->name;
                $m['value'] = $meta->value;
                $m['id'] = $meta->id;
                $delete[] = $m;
                $meta->delete();
            }
        }
        return $delete;
    }
}
