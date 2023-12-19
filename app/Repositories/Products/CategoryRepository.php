<?php

namespace App\Repositories\Products;

use App\Repositories\Categories\CategoryRepository as BaseRepository;
use App\Repositories\Orders\OrderRepository;

/**
 * validator 
 * 
 */

use App\Validators\Products\CategoryValidator;
use App\Masks\Products\CategoryMask;
use App\Masks\Products\CategoryCollection;
use App\Models\Order;
use Gomee\Helpers\Arr;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass 
     */
    protected $validatorClass = CategoryValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = CategoryMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = CategoryCollection::class;

    /**
     * @var string $system
     */
    protected $system = 'both';

    protected $joinProducts = false;

    /**
     * @var array $sortByRules kiểu sắp xếp
     */
    protected $sortByRules = [
        1 => 'id-DESC',
        2 => 'name-ASC',
        3 => 'name-DESC',
        4 => 'best-seller',
        5 => 'rand()',
        6 => 'id-ASC',

    ];
    /**
     * thiết lập ban đầu
     */
    public function init()
    {
        $this->addDefaultValue('type', 'product')->addDefaultParam('type', 'type', 'product')
            ->setWith('parent');
        $this->perPage = 20;
    }


    public function beforeGetData($data = [])
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $key => $value) {
                $k = strtolower($key);
                $a = substr($k, 0, 1);
                if ($a != '@') continue;
                $f = substr($k, 1);
                switch ($f) {
                    case 'sort':
                    case 'sort_type':
                    case 'sorttype':
                        $this->parseSortBy($value);
                        unset($data[$key]);
                        break;
                    case 'countproduct':
                        $this->withCount('notTrashedProducts as product_count');
                        unset($data[$key]);
                        $this->joinProducts = true;
                        break;


                    case 'countattribute':
                        $this->withCount('refAttributes as attribute_count');
                        unset($data[$key]);
                        break;


                    default:
                        # code...
                        break;
                }
            }
        }
        return $data;
    }


    
    public function withCountProductQuery()
    {
        $this->withCount('notTrashedProducts as product_count');
        $this->withCount('refAttributes as attribute_count');
        $this->joinProducts = true;
        return $this;
    }


    public function withMoveToTrashQuery()
    {
        $this->withCount('refProducts as product_count');
        $this->withCount('refAttributes as attribute_count');
        // dd($this);
        $this->joinProducts = true;
        return $this;
    }

    public function detailQuery($args = [])
    {
        if (!((array_key_exists('id', $args) && $args['id']) || array_key_exists('parent_id', $args) && $args['parent_id'])) {
            $this->where('parent_id', 0);
        }

        $this->withCount('notTrashedProducts as product_count')->with(['children' => function ($query) {
            $query->withCount('notTrashedProducts as product_count')->with(['children' => function ($query) {
                $query->withCount('notTrashedProducts as product_count')->with(['children' => function ($query) {
                    $query->withCount('notTrashedProducts as product_count');
                }]);
            }]);
        }]);
        $this->joinProducts = true;
        return $this;
    }

    /**
     * lấy danh sach danh mục
     *
     * @param array $args
     * @return Collection|\App\Masks\Categories\CategoryCollection|[]
     */
    public function getCategories($args = [])
    {
        // tham số nâng cao
        if (isset($args['@advance']) && is_array($args['@advance']) && in_array('product_count', $args['@advance'])) {
            // nếu cần đếm toàn bộ số sản phẩm

            if (!((array_key_exists('id', $args) && $args['id']) || array_key_exists('parent_id', $args) && $args['parent_id'])) {
                $this->where('parent_id', 0);
            }

            $this->withCount('notTrashedProducts as product_count')->with(['children' => function ($query) {
                $query->withCount('notTrashedProducts as product_count')->with(['children' => function ($query) {
                    $query->withCount('notTrashedProducts as product_count')->with(['children' => function ($query) {
                        $query->withCount('notTrashedProducts as product_count');
                    }]);
                }]);
            }]);
            $this->joinProducts = true;
        }

        // sap xep danh sach

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
                    } elseif (strtolower($column) == 'seller') {
                        $this->orderBySeller($type);
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
        } elseif (strtolower($sortBy) == 'seller') {
            $this->orderBySeller($type ? $type : 'DESC');
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
        } elseif ($rule == 'best-seller') {
            $this->orderBySeller();
        } else {
            $a = explode('-', $rule);
            $this->order_by($a[0], $a[1]);
        }
    }

    /**
     * sap xep theo ban nhieu
     *
     * @param string $type
     * @return void
     */
    protected function orderBySeller($type = 'DESC')
    {
        if (strtoupper($type) != 'ASC') $type = 'DESC';
        if (!$this->joinProducts) {
            $this->withCount('notTrashedProducts as product_count');
        }
        $this->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::COMPLETED)
            ->orderByRaw('product_count ' . $type);
    }
}
