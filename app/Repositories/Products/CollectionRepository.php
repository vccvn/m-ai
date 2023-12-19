<?php

namespace App\Repositories\Products;

use Gomee\Repositories\BaseRepository;
/**
 * validator 
 * 
 */
use App\Validators\Products\CollectionValidator;
use App\Masks\Products\CollectionMask;
use App\Masks\Products\CollectionCollection;
use Gomee\Helpers\Arr;

class CollectionRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = CollectionValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = CollectionMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = CollectionCollection::class;

    /**
     * @var array $sortByRules kiểu sắp xếp
     */
    protected $sortByRules = [
        1 => 'id-DESC',
        2 => 'id-ASC',
        3 => 'name-ASC',
        4 => 'name-DESC',
        5 => 'rand()'
    ];

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProductCollection::class;
    }

    public function getTagData($args = [])
    {
        $data = [];
        if ($list = $this->notTrashed()->get($args)) {
            foreach ($list as $item) {
                $data[] = [
                    'id' => $item->id,
                    'name' => htmlentities($item->name)
                ];
            }
        }
        return $data;
    }

    /**
     * get user option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getCollectionTagData($request, array $args = [])
    {
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('id', $request->ignore);
        }
        $data = [];
        if ($list = $this->getFilter($request, $args)) {
            foreach ($list as $item) {
                $data[] = [
                    'id' => $item->id,
                    'name' => htmlentities($item->name)
                ];
            }
        }
        return $data;
    }

    
    /**
     * lấy danh sach danh mục
     *
     * @param array $args
     * @return Collection|\App\Masks\Categories\CategoryCollection|[]
     */
    public function getCollections($args = [])
    {
        // sap xep danh sach
        $a = false;
        foreach (['', 'type', 'Type', '_type'] as $k) {
            if (isset($args['@sort' . $k])) {
                if (!$a) {
                    $this->parseSortBy($args['@sort' . $k]);
                    $a = true;
                }
                unset($args['@sort' . $k]);
            }
        }

        return $this->parseCollection($this->get($args));
    }



    /**
     * xử lý order by cho hàm lấy sản phẩm
     *
     * @param array|string $sortBy
     * @return void
     */
    public function parseSortBy($sortBy)
    {
        if (is_array($sortBy)) {
            // truong hop mang toan index la so
            if (Arr::isNumericKeys($sortBy)) {
                foreach ($sortBy as $by) {
                    $this->checkSortBy($by);
                }
            } else {
                foreach ($sortBy as $column => $type) {
                    if (is_numeric($column)) {
                        $this->checkSortBy($type);
                    } else {
                        $this->order_by($column, $type);
                    }
                }
            }
        } else {
            $this->checkSortBy($sortBy);
        }
    }


    /**
     * kiểm tra tính hợp lệ của tham sớ truyền vào
     *
     * @param string $sortBy
     * @param string $type
     * @return void
     */
    protected function checkSortBy($sortBy = null, $type = null)
    {
        if (in_array($sortBy, $this->sortByRules)) {
            $this->orderByRule($sortBy);
        } elseif (array_key_exists($sortBy, $this->sortByRules)) {
            $this->orderByRule($this->sortByRules[$sortBy]);
        } elseif (count($a = explode('-', $sortBy)) == 2) {
            $this->order_by($a[0], $a[1]);
        } elseif ($sortBy) {
            $this->order_by($sortBy, $type ? $type : 'ASC');
        }
    }


    /**
     * order by rule
     *
     * @param string $rule
     * @return void
     */
    protected function orderByRule($rule)
    {
        if ($rule == 'rand()') {
            $this->orderByRaw($rule);
        } else {
            $a = explode('-', $rule);
            $this->order_by($a[0], $a[1]);
        }
    }
}