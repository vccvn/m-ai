<?php

namespace App\Repositories\Products;

use App\Masks\Products\ProductCollection;
use App\Masks\Products\ProductMask;
use App\Masks\Products\ProductReviewCollection;
use App\Masks\Products\ProductReviewMask;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ProductReview;
use App\Repositories\Orders\OrderItemRepository;
use App\Validators\Products\ReviewValidator;
use Gomee\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReviewRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = ReviewValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = ProductReviewMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = ProductReviewCollection::class;
    /**
     * @var array $defaultSortBy Mảng key value là twen6 cộ và kiểu sắp xếp
     */
    protected $defaultSortBy = [
        'product_reviews.id' => 'DESC'
    ];

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProductReview::class;
    }

    public function init()
    {   
        $this->setJoinable([
            ['join', 'products', 'products.id', '=', 'product_reviews.product_id'],
            ['leftJoin', 'customers', 'customers.id', '=', 'product_reviews.customer_id'],
            ['leftJoin', 'metadatas', function ($join) {
                $join->on('metadatas.ref_id', '=', 'product_reviews.id')->on('metadatas.ref', '=', DB::raw("'" . ProductReview::REF_KEY . "'"));
            }]
        ]);
        $columns = [
            'product_name' => 'products.name',
            'customer_name' => 'customers.name',
            'customer_email' => 'customers.email',
            'attr_values' => 'metadatas.value'

        ];
        $this->setSearchable($columns);
        $this->setSortable($columns);

        $this->setSelectable(array_merge(['product_reviews.*'], $columns));
    }

    public function getReviews($args = [], $advance = [])
    {
        $select = ['product_reviews.*'];
        if (in_array('customer', $advance) || in_array('user', $advance)) {
            $this->leftJoin('customers', 'customers.id', '=', 'product_reviews.customer_id');
            $select = array_merge($select, ['customers.name as customer_name', 'customers.email as customer_email']);
        }
        if (in_array('user', $advance)) {
            $this->leftJoin('users', 'users.id', '=', 'customers.user_id');
            $select = array_merge($select, ['users.name as user_name', 'users.email as user_email', 'users.avatar']);
        }
        if (in_array('product', $advance)) {
            $this->leftJoin('products', 'products.id', '=', 'product_reviews.product_id');
            $select = array_merge($select, ['products.name as product_name', 'products.slug as product_slug', 'products.feature_image as feature_image']);
        }
        $this->select(...$select);
        return $this->getData($args);
    }

    /**
     * lấy review theo id san pham
     *
     * @param integer $product_id
     * @param integer $paginate
     * @param string $orderBy
     * @return void
     */
    public function getProductReviews($product_id, $paginate = 10, $orderBy = 'DESC', $review_approve_request = false)
    {
        return $this
            ->leftJoin('customers', 'customers.id', '=', 'product_reviews.customer_id')
            ->leftJoin('users', 'users.id', '=', 'customers.user_id')
            ->leftJoin('metadatas', function ($join) {
                $join->on('metadatas.ref_id', '=', 'product_reviews.id')->on('metadatas.ref', '=', DB::raw("'" . ProductReview::REF_KEY . "'"));
            })
            ->select(
                'product_reviews.*',
                'customers.name as customer_name',
                'customers.email as customer_email',
                'users.avatar',
                'users.name as user_name',
                'users.email as user_email',
                'metadatas.value as attr_values'
            )
            ->orderBy('product_reviews.created_at', $orderBy)
            ->mode('mask')
            ->getData([
                'product_id' => $product_id,
                'approved' => $review_approve_request?1: [1, 0],
                '@paginate' => $paginate
            ]);
    }
    /**
     * @Description
     *
     * @Author phuongnam
     * @Date   Aug 29, 2022
     *
     * @param $userId
     *
     * @return Builder|Model|object|null
     */
    public function getCustomerByUser($userId)
    {
        return Customer::query()->where('user_id', $userId)->first();
    }

    /**
     * @Description
     *
     * @Author phuongnam
     * @Date   Aug 30, 2022
     *
     * @param $userId
     * @param $productId
     *
     * @return bool
     */
    public function isRated($customerId, $productId): bool
    {

        $order = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.customer_id', '=', $customerId)
            ->where('order_items.product_id', '=', $productId)
            ->whereIn('orders.status',[Order::COMPLETED, Order::REFOUND])
            ->first();
        if (!$order)
            return false;
        return true;
    }


    /**
     * kiểu tra dữ liệu dc phép review
     *
     * @param array $data
     * @return array
     */
    public function checkReviewedProducts($customer_id, $product_id, $data = [])
    {
        if ($data) {
            $keys = array_keys($data);
            $ignore = [];

            $reviews = $this->select('product_reviews.id', 'product_reviews.product_id', 'metadatas.value as attr_values')->join('metadatas', function ($join) {
                $join->on('metadatas.ref_id', '=', 'product_reviews.id')
                    ->on('metadatas.ref', '=', DB::raw("'" . ProductReview::REF_KEY . "'"));
            })->get([
                'metadatas.name' => 'attr_values',
                'metadatas.value' => $keys,
                'product_reviews.product_id' => $product_id,
                'product_reviews.customer_id' => $customer_id
            ]);
            if (count($reviews)) {
                foreach ($reviews as $key => $review) {
                    $ignore[] = $review->attr_values;
                }
            }
            $d = [];
            foreach ($data as $key => $value) {
                if (!in_array($key, $ignore)) {
                    $d[$key] = $value;
                }
            }
            return $d;
        }
        return $data;
    }

    public function canReview($customer_id, $product_id, $attr_values = null)
    {
        $reviews = $this->join('metadatas', function ($join) {
            $join->on('metadatas.ref_id', '=', 'product_reviews.id')
                ->on('metadatas.ref', '=', DB::raw("'" . ProductReview::REF_KEY . "'"));
        })->count([
            'product_reviews.product_id' => $product_id,
            'product_reviews.customer_id' => $customer_id,
            'metadatas.name' => 'attr_values',
            'metadatas.value' => $attr_values
        ]);
        return $reviews == 0;
    }

    public function getCustomerReview($customer_id, $product_id, $attr_values = null)
    {
        $args = [
            'product_reviews.product_id' => $product_id,
            'product_reviews.customer_id' => $customer_id,

        ];
        if ($attr_values) {
            $this->select('product_reviews.id', 'product_reviews.product_id', 'metadatas.value as attr_values')
                ->join('metadatas', function ($join) {
                    $join->on('metadatas.ref_id', '=', 'product_reviews.id')
                        ->on('metadatas.ref', '=', DB::raw("'" . ProductReview::REF_KEY . "'"));
                });
            $args = array_merge($args, [
                'metadatas.name' => 'attr_values',
                'metadatas.value' => $attr_values
            ]);
        }
        $review = $this->first($args);
        return $review;
    }
}
