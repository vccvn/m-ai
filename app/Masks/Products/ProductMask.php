<?php

namespace App\Masks\Products;

use App\Engines\ViewManager;
use App\Masks\Categories\CategoryMask;
use App\Masks\Comments\CommentCollection;
use App\Masks\Files\FileCollection;
use App\Masks\Files\GalleryCollection;
use App\Masks\MaskCollection;
use App\Masks\Metadatas\MetadataCollection;
use App\Masks\Promos\PromoCollection;
use App\Masks\Tags\TagCollection;
use App\Masks\Users\AuthorMask;
use App\Models\ProductReview;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Products\ReviewRepository;
use Gomee\Helpers\Arr;
use Gomee\Masks\Mask;
use Illuminate\Support\Facades\DB;

class ProductMask extends Mask
{

    public $meta = [];
    public $attributes = [];

    protected $options = [];

    /**
     * reviewRepository
     *
     * @var ReviewRepository
     */
    protected $reviewRepository;
    /**
     * thiết lập các thông số
     *
     * @return void
     */
    protected function init()
    {
        $this->reviewRepository = app(ReviewRepository::class)->mode('mask');
        $this->allow([
            'getFeatureImage', 'hasPromo', 'promoAvailable', 'priceFormat', 'getFinalPrice',
            'countTotal', 'getViewUrl', 'getFullTitle', 'getReviewPoints', 'countReview',
            'getOrderOptions', 'getVariantAttributes', 'getOrderAttributes', 'getProductAttributes', 'hasVariant',
            'hasOption', 'getImage',  'getThumbnail', 'canReview', 'getDownPercent',
            'pivot',
            'checkPrice'
        ]);
        $this->map([
            'metadatas'        => MetadataCollection::class,
            'category'         => CategoryMask::class,
            'shop'             => AuthorMask::class,
            'tags'             => TagCollection::class,
            'attrs'            => MaskCollection::class,
            'gallery'          => GalleryCollection::class,
            'files'            => FileCollection::class,
            'media'            => FileCollection::class,
            'comments'         => CommentCollection::class,
            'publishComments'  => CommentCollection::class,
            'reviews'          => ProductReviewCollection::class,
            'promos'           => PromoCollection::class,
            'promoAvailable'   => PromoCollection::class
        ]);
    }

    // xem thêm ExampleMask

    protected function onLoaded()
    {
        if ($metadatas = $this->relation('metadatas')) {
            $jsf = $this->model->getJsonFields();
            foreach ($metadatas as $m) {
                if (in_array($m->name, $jsf)) {
                    $value = json_decode($m->value, true);
                } else {
                    $value = $m->value;
                }

                $this->meta[$m->name] = $value;
            }
        }

        if ($this->content) $this->content = do_shortcode($this->content);
        $this->image_url = $this->getFeaturedImage();
        $this->has_promo = $this->hasPromo();
        $this->has_option = $this->hasOption();
        $this->final_price = $this->getFinalPrice();

        if(!is_array($this->features)){
            if($this->features) $this->features = explode(',', $this->features);
            else{
                $this->features = [];
            }
        }

        if($this->has_option){
            $this->options = $this->getOrderOptionData();
        }

    }


    /**
     * gán dự liệu meta cho product
     * @return void
     */
    public function applyMeta()
    {
        if (!$this->meta) {
            if ($metadatas = $this->relation('metadatas', true)) {
                $jsf = $this->model->getJsonFields();
                foreach ($metadatas as $m) {
                    if (in_array($m->name, $jsf)) {
                        $value = json_decode($m->value, true);
                    } else {
                        $value = $m->value;
                    }
                    $this->data[$m->name] = $value;
                    $this->meta[$m->name] = $value;
                }
            }
        } elseif ($this->meta) {
            foreach ($this->meta as $key => $value) {
                $this->data[$key] = $value;
            }
        }
        
        if(!is_array($this->features)){
            if($this->features) $this->features = explode(',', $this->features);
            else{
                $this->features = [];
            }
        }
    }



    public function getSuggestionProducts($args = null)
    {
        $params = [];
        if (is_numeric($args)) {
            $params['@limit'] = $args;
        } elseif (is_array($args)) {
            $params = $args;
        } elseif (is_string($args) && $args) {
            $params['@order_by'] = $args;
        }

        if (!isset($params['@sort']) && !isset($params['@sorttype']) && !isset($params['@sortType']) && !isset($params['@sort_type']) && !isset($params['@order_by']) && !isset($params['@orderBy']) && !isset($params['@orderby'])) {
            $params['@sorttype'] = 'rand()';
        }
        if (!isset($params['@limit'])) $params['limit'] = 12;
        /**
         * @var \App\Repositories\Products\ProductRepository
         */
        $repository = app(ProductRepository::class)->mode('mask')->notTrashed();
        $res = $repository->getSuggestionProducts($this->id, $params);
        return $res;
    }

    /**
     * lấy ra các bài viết liên quan
     *
     * @param array|int|str $args
     * @return \App\Masks\MaskCollection
     */
    public function getRelated($args = null)
    {
        $params = [];
        if (is_numeric($args)) {
            $params['@limit'] = $args;
        } elseif (is_array($args)) {
            $params = $args;
        } elseif (is_string($args) && $args) {
            $params['@order_by'] = $args;
        }

        if (!isset($params['@sort']) && !isset($params['@sorttype']) && !isset($params['@sortType']) && !isset($params['@sort_type']) && !isset($params['@order_by']) && !isset($params['@orderBy']) && !isset($params['@orderby'])) {
            $params['@sorttype'] = 'rand()';
        }
        $params['category_id'] = $this->category_id;
        if (!isset($params['@limit'])) $params['limit'] = 12;
        /**
         * @var \App\Repositories\Products\ProductRepository
         */
        $repository = app(ProductRepository::class)->mode('mask')->notTrashed();
        // dd($params);
        // DB::enableQueryLog(); // Enable query log

        $res = $repository->where('id', '!=', $this->id)->getProducts($params);
        // Your Eloquent query executed by using get()

        // dump(DB::getQueryLog()); // Show results of log

        // dump($res);
        return $res;
    }


    public function getSameProducts($same_field = 'category_id', $args = null)
    {
        $params = [];
        if (is_numeric($args)) {
            $params['@limit'] = $args;
        } elseif (is_array($args)) {
            $params = $args;
        } elseif (is_string($args) && $args) {
            $params['@order_by'] = $args;
        }
        if (!$same_field) $same_field = 'category_id';
        if (!isset($params['@sort']) && !isset($params['@sorttype']) && !isset($params['@sortType']) && !isset($params['@sort_type']) && !isset($params['@order_by']) && !isset($params['@orderBy']) && !isset($params['@orderby'])) {
            $params['@sorttype'] = 'rand()';
        }
        $params['same_field'] = $this->{$same_field};
        if (!isset($params['@limit'])) $params['limit'] = 5;
        /**
         * @var \App\Repositories\Products\ProductRepository
         */
        $repository = app(ProductRepository::class)->mode('mask');
        return $repository->where('id', '!=', $this->id)->getProducts($params);
    }


    /**
     * lấy thong tin danh gia san phẩm
     *
     * @return Arr
     */
    public function getReviewData()
    {
        $data = [
            'total' => 0,
            'ratings' => [
            ],
            'rating_avg' => 0,
            'rating_int' => 0,
            'data' => [],
        ];
        if($this->has_review_analytics){
            $count = 0;
            $total = 0;
            $perfect = 5;
            for ($i=1; $i < $perfect+1; $i++) { 
                $stars = $this->{"rating_{$i}_count"};
                $count+= $stars;
                $total += $stars*$i;
                $data['ratings'][$i] = [
                    'count' => $stars,
                    'percent' => 0
                ];
            }
            $data['total'] = $count;
            if($count>0){
                $totalStartPerfect = $count * $perfect;
                $avg = $total / $totalStartPerfect * $perfect; 
                for ($i=1; $i < $perfect+1; $i++) { 
                    $data['ratings'][$i]['percent'] = round($data['ratings'][$i]['count'] / $count, 2);
                }
                $data['rating_avg'] = round($avg, 2);
                $data['rating_int'] = round($avg, 0);
            }
            
            return new Arr($data);
        }
        return null;
        
        $t = count($this->reviews);
        $data = [
            'total' => $t,
            'ratings' => [
                [
                    'count' => 0,
                    'percent' => 0
                ],
                [
                    'count' => 0,
                    'percent' => 0
                ],
                [
                    'count' => 0,
                    'percent' => 0
                ],
                [
                    'count' => 0,
                    'percent' => 0
                ],
                [
                    'count' => 0,
                    'percent' => 0
                ],
                [
                    'count' => 0,
                    'percent' => 0
                ]
            ],
            'rating_avg' => 0,
            'rating_int' => 0,
            'data' => $this->reviews,
        ];
        if ($t > 0) {
            $total = 0;
            $perfect = 5;
            foreach ($this->reviews as $review) {
                $data['ratings'][$review->rating]['count']++;
                $total += $review->rating;
            }
            $avg = $total / ($t * $perfect) * $perfect;
            $data['rating_avg'] = round($avg, 2);
            $data['rating_int'] = round($avg, 0);
            foreach ($data['ratings'] as $i => $rating) {
                $data['ratings'][$i+1]['percent'] = round($rating['count'] / $t * 100, 2);
            }
        }
        return new Arr($data);
    }

    public function attributesToHtml($options = [], $onlyOrder = false)
    {
        return ViewManager::libTemplate('product-order-attributes', array_merge($options, ['product' => $this]));
    }

    public function getReviews($paginate = 10, $orderBy = 'DESC', $review_approve_request = false)
    {
        return $this->reviewRepository->leftJoin('customers', 'customers.id', '=', 'product_reviews.customer_id')
            ->leftJoin('users', 'users.id', '=', 'customers.user_id')
            ->leftJoin('metadatas', function ($join) {
                $join->on('metadatas.ref_id', '=', 'product_reviews.id')
                    ->on('metadatas.ref', '=', DB::raw("'" . ProductReview::REF_KEY . "'"));
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
            ->with('metadatas')
            ->mode('mask')
            ->getData([
                'product_id' => $this->id,
                'approved' => $review_approve_request?1: [1, 0],
                '@paginate' => $paginate
            ]);
    }

    
    public function getOrderOption($name, $type = null)
    {
        if(!$this->options) return null;
        if($type){
            foreach ($this->options as $option) {
                if($option->name == $name && $option->advance_value_type == $type) return $option;
            }
        }
        else{
            foreach ($this->options as $option) {
                if($option->name == $name) return $option;
            }
        }
        return null;
    }

    
    public function getTypeOrderOption($type)
    {
        if(!$this->options) return null;
        foreach ($this->options as $option) {
            if($option->advance_value_type == $type) return $option;
        }
        return null;
    }
    
    public function getThumbnailOrderOption()
    {
        if(!$this->options) return [];
        $data = [];
        foreach ($this->options as $option) {
            if($option->use_thumbnail == 1) $data[] = $option;
        }
        return $data;
    }

    // khai báo thêm các hàm khác bên dưới nếu cần
}
