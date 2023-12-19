<?php

namespace App\Repositories\Products;

use Gomee\Repositories\BaseRepository;

/**
 * validator
 *
 */

use App\Validators\Products\ProductValidator;
use App\Masks\Products\ProductMask;
use App\Masks\Products\ProductCollection;
use App\Masks\StyleSets\Personal\StyleSetMask;
use App\Models\Order;
use App\Models\PersonalStyleSet;
use App\Models\Product;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Metadatas\MetadataRepository;
use App\Repositories\Orders\OrderItemRepository;
// use App\Repositories\StyleSets\Personal\StyleSetRepository;
// use App\Repositories\StyleSets\StyleSetItemRepository;
use Gomee\Helpers\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @method Product detail(int|array $args) lấy chi tiết sản phẩm
 * @method Product first(array $args) lấy chi tiết sản phẩm
 * @method Product findBy(string $key, mixed $value=null) lấy chi tiết sản phẩm
 *
 */
class ProductRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = ProductValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = ProductMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = ProductCollection::class;

    /**
     * @var \App\Repositories\Metadatas\MetadataRepository $metadataRepository
     */
    public $metadataRepository;


    /**
     * @var \App\Repositories\Products\AttributeRepository $attributeRepository
     */
    public $attributeRepository;

    /**
     * customer
     *
     * @var \App\Repositories\Customers\CustomerRepository $customerRepository
     */
    protected $customerRepository = null;
    /**
     * collectionRepository
     *
     * @var CollectionRepository
     */
    protected $collectionRepository;

    /**
     * @var array $sortByRules kiểu sắp xếp
     */
    protected $sortByRules = [
        1 => 'id-DESC',
        2 => 'name-ASC',
        3 => 'name-DESC',
        4 => 'list_price-ASC',
        5 => 'list_price-DESC',
        6 => 'rand()',
        7 => 'id-ASC',

    ];


    /**
     * @var array $defaultSortBy Mảng key value là twen6 cộ và kiểu sắp xếp
     */
    protected $defaultSortBy = [
        'products.id' => 'DESC'
    ];



    /**
     * prefix attribute name
     *
     * @var string
     */
    public $attributePrefix = 'product_';


    public $isGroupByProductId = false;


    public $actionStatus = true;
    public $actionMessage = "Thao tác thành công";

    public $pageTitle = '';
    protected $isJoinCategoryRef = false;
    protected $isJoinTagRef = false;
    protected $isJoinProductAttributeValue = false;

    protected $needBuildSelectAndJoinQuery = true;
    protected $isBuildJoin = false;
    protected $isBuildSelect = false;
    protected $needBuildJoin = false;
    protected $needBuildSelect = false;

    /**
     * settings
     *
     * @var Arr<string, mixed> $settings
     */
    protected $settings = null;

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Product::class;
    }



    /**
     * thiết lập
     * @return void
     */
    public function init()
    {
        $this->metadataRepository = new MetadataRepository();
        $this->attributeRepository = new AttributeRepository();
        $this->attributePrefix = get_attribute_request_name_prefix();
        $this->collectionRepository = app(CollectionRepository::class)->mode('mask');
        $this->customerRepository = app(CustomerRepository::class);
        $this->settings = product_setting();
        $this->setJoinable([
            ['leftJoin', 'categories', 'categories.id', '=', 'products.category_id']
        ]);
        $raw = [
            'category_id', 'name', 'sku',
            'list_price', 'sale_price', 'on_sale', 'views', 'privacy', 'status',
        ];
        $columns = [
            'category_name' => 'categories.name',
            'category_keywords' => 'categories.keywords',
        ];
        $this->setSelectable(array_merge($columns, ['products.*']));
        $this->setSearchable(array_merge($columns, [
            'name' => 'products.name',
            'keywords' => 'products.keywords',
            'sku' => 'products.sku'
        ]));
        foreach ($raw as $col) {
            $columns[$col] = 'products.' . $col;
        }
        $this->setSortable($columns);

        $this->registerCacheMethods('getProductDetail', 'search', 'getProducts', 'getSuggestionProducts', 'detail');
    }


    /**
     * lấy chi tiết sản phẩm
     *
     * @param array $args
     * @return \App\Models\Product[]|\App\Masks\Products\ProductCollection
     */
    public function getProducts(array $args = [])
    {
        return $this->getData($args);
    }
    /**
     * @Description
     *
     * @Author DoanLN
     * @Date   Aug 30, 2022
     *
     * @param Product|ProductMask $product
     *
     * @return array<string, string>
     */
    public function getBoughtProductOptions($product)
    {
        $customer = $this->customerRepository->getCurrentCustomer();
        if (!$customer)
            return [];

        $customerId = $customer->id;

        // $orderOptions = $product->getOrderOptions();
        // $attrValues = [];
        // if($orderOptions){
        //     foreach ($orderOptions as $key => $attr) {
        //         $val = $attr['value_ids'];
        //         $attrValues[] = $val;
        //     }
        // }

        // $attrKeyList = array_map(function($item){
        //     sort($item);
        //     return implode('-', $item);
        // }, arr_combine_list($attrValues));

        $orderItemRepository = new OrderItemRepository;
        $rs = $orderItemRepository->select('order_items.*', 'products.name as product_name')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('order_items.attr_values')
            ->get([
                'order_items.product_id' => $product->id,
                'orders.status' => [
                    Order::COMPLETED,
                    Order::REFOUND
                ],
                'orders.customer_id' => $customerId
            ]);
        if (count($rs)) {
            $data = [];
            foreach ($rs as $orderItem) {
                $key = $orderItem->attr_values;
                $label = $product->name;
                $atts = [];
                if ($attrs = $orderItem->getAttrs()) {
                    foreach ($attrs as $i => $attr) {
                        $atts[] = htmlentities($attr->text_value);
                    }
                }
                $label .= ' ' . implode(' / ', $atts);
                $data[$key] = $label;
            }
            return $data;
        }

        return [];
    }
    /**
     * lấy chi tiết sản phẩm
     *
     * @param array $args
     * @return \App\Models\Product|\App\Masks\Products\ProductMask
     */
    public function getProductDetail(array $args = [])
    {
        if (!$args) return null;
        $args['@reviewAnalytics'] = true;
        $this->needBuildSelectAndJoinQuery = true;
        $detail = $this->with([
            'shop', 'category', 'metadatas', 'gallery', 'reviews',
            'notOrderOptions', 'variants', 'notVariantAttributes', 'promoAvailable', 'tags'
        ])->first($args);

        return $this->parseDetail($detail);
    }
    /**
     * lấy chi tiết sản phẩm
     *
     * @param array $args
     * @return \App\Models\Product|\App\Masks\Products\ProductMask
     */
    public function getProductDetailData(array $args = [])
    {
        if (!$args) return null;
        $args['@reviewAnalytics'] = true;
        $detail = $this->with([
            'shop', 'category', 'metadatas', 'gallery',
            'orderOptions', 'variants', 'notVariantAttributes',
        ])->first($args);
        return $this->parseDetail($detail);
    }





    /**
     * tìm kiếm phía client
     *
     * @param Request $request
     * @param array $args
     * @return \App\Models\Product[]|\App\Masks\Products\ProductCollection
     */
    public function search($request, $args = [])
    {
        $keyword = strlen($request->search) ? $request->search : (strlen($request->s) ? $request->s : (strlen($request->keyword) ? $request->keyword : (strlen($request->keywords) ? $request->keywords : (strlen($request->tim) ? $request->tim : ($request->timkiem)))));

        $sortby = $request->sortby ?? ($request->get('sorttype') ?? $request->orderBy);

        if ($request->get('collection') && $collection = $this->collectionRepository->detail($request->get('collection'))) {
            $args = array_merge($args, $collection->getProductParams());
            $this->pageTitle = $collection->name;
        }
        if ($style = $request->get('style')) {
            $args['@style'] = $style;
        }
        if ($request->get('categories') && $ids = array_filter(array_map('trim', explode(',', $request->get('categories'))), function ($id) {
            return strlen($id) > 0;
        })) {
            if (array_key_exists('@hasAnyCategory', $args)) {
                unset($args['@hasAnyCategory']);
            }

            // $args['@match:categories'] = $ids;
            $args['@hasAnyCategory'] = $ids;
        }
        if (($c = $request->category ?? $request->in_any_category) && $cates = array_filter(array_map('trim', explode(',', $c)), function ($id) {
            return strlen($id) > 0;
        })) {
            $args['@hasAnyCategory'] = $cates;
        }

        if (($t = $request->tags ?? $request->match_tags) && $tags = array_filter(array_map('trim', explode(',', $t)), function ($id) {
            return strlen($id) > 0;
        })) {
            $args['@matchAllTag'] = $tags;
        } elseif (($t = $request->has_tag ?? $request->has_tags) && $tags = array_filter(array_map('trim', explode(',', $t)), function ($id) {
            return strlen($id) > 0;
        })) {
            $args['@anyTags'] = $tags;
        }

        if (($l = $request->labels ?? $request->match_labels) && $labels = array_filter(array_map('trim', explode(',', $l)), function ($id) {
            return strlen($id) > 0;
        })) {
            $args['@matchAllLabel'] = $labels;
        } elseif (($l = $request->has_label ?? $request->has_labels) && $labels = array_filter(array_map('trim', explode(',', $l)), function ($id) {
            return strlen($id) > 0;
        })) {
            $args['@anyLabel'] = $labels;
        }

        if (!$sortby && $ps = product_setting('sorttype')) {
            $args['@sorttype'] = $ps;
        } else {
            $args['@sorttype'] = $sortby ?? 1;
        }


        $s = $keyword;
        $category = 0;
        // danh sach category id theo thu tu root > parent > child > ...
        $attributeCategoryMap = [0];
        if (isset($args['category_id'])) {
            $category = $args['category_id'];
            unset($args['category_id']);
        }
        if (isset($args['category'])) {
            $category = $args['category'];
            unset($args['category']);
        }
        if (isset($args['@category'])) {
            $category = $args['@category'];
        } elseif ($category) {
            $args['@category'] = $category;
        }

        if (isset($args['@attribute_category_map'])) {
            $attributeCategoryMap = $args['@attribute_category_map'];
        }

        // tim kiếm theo tu khoa
        if (strlen($s)) {
            $this->where(function ($query) use ($s) {
                $t = 'products.';
                // tu khoa nam trong ten san pham
                $query->where($t . 'name', 'like', "%$s%");
                // tu khoa nam trong han meta keyword
                $query->orWhere($t . 'keywords', 'like', "%$s%");
                // tim theo tag
                $query->orWhereRaw(
                    $t . "id in ("
                        . "SELECT tag_refs.ref_id FROM tag_refs INNER JOIN tags ON tags.id = tag_refs.tag_id "
                        . "WHERE tag_refs.ref = 'product' AND "
                        . "(tags.name_lower like '%" . str_replace("'", "", strtolower($s)) . "%' OR tags.slug like '%" . str_slug($s) . "%')"
                        . ")"
                );
                // hoac bang mã sp
                $query->orWhere($t . 'sku', '=', $s);
            });
        }
        /// build orderby cac thu
        $this->prepareFilter($request);
        $this->buildJoin();
        $this->buildSelect();

        // lọc theo khoảng giá

        if (is_numeric($request->min_price)) {
            $this->where('products.list_price', '>=', $request->min_price);
        }
        if (is_numeric($request->max_price)) {
            $this->where('products.list_price', '<=', $request->max_price);
        }

        // tim san pham theo thuoc tinh


        // lấy thuộc tính từ request
        $reqAttrs = Arr::prefix($request->all(), $this->attributePrefix, true, function ($val) {
            if (is_string($val) && preg_match('/\,/', $v = trim($val, ',\s'))) {
                return array_map('trim', explode(',', $v));
            } elseif (is_array($val)) {
                $value = [];
                foreach ($val as $v) {
                    if (is_numeric($v) || (is_string($v) && strlen($v))) {
                        $value[] = $v;
                    }
                }
                return count($value) ? $value : null;
            }
            return $val;
        }, true);

        // them tham so de lay ra trong viww neu can
        set_attribute_request_data($reqAttrs);
        set_attribute_category_map($attributeCategoryMap);

        // neu co thuoc tinh tu request
        if ($reqAttrs) {
            $params = [
                'name' => array_keys($reqAttrs),
                'category_id' => $attributeCategoryMap,
                'is_query' => 1
            ];

            $product_attr_count = 0;
            if (count($attributes = $this->attributeRepository->get($params))) {
                $attributeById = [];
                // tao danh sach thuoc tinh mac dinh theo danh muc
                $attributeByCategoryId = [];

                // danh sach gia tri thuoc tinh
                $attributeValues = [];

                // duyệt mảng map để điền thông tin mặc định theo thu tu
                foreach ($attributeCategoryMap as $id) {
                    $attributeByCategoryId[$id] = [];
                }
                // duyệt mảng thuộc tính
                foreach ($attributes as $attr) {
                    $attributeById[$attr->id] = $attr;
                    $attributeByCategoryId[$attr->category_id][] = $attr->id;
                }

                // duyệt mảng thuộc tính theo category id để lấy ra giá trị và od của thuộc tính
                // và điền vào mảng $attributeValues với key là id và value là giá trị từ request
                foreach ($attributeByCategoryId as $category_id => $attribute_ids) {
                    if (count($attribute_ids)) {
                        foreach ($attribute_ids as $attribute_id) {
                            $attr = $attributeById[$attribute_id];
                            if (isset($reqAttrs[$attr->name])) {
                                $attributeValues[$attribute_id] = [
                                    'type' => $attr->value_type,
                                    'value' => $reqAttrs[$attr->name]
                                ];
                            }
                        }
                    }
                }


                // nếu chắc chắn có thuộc tính giống như người dùng gừi từ trình duyệt thì mới thực hiện bước truy vấn này
                if ($attributeValues) {
                    if (!$this->isJoinProductAttributeValue) {
                        $this->join('product_attributes', 'product_attributes.product_id', 'products.id');
                        $this->isJoinProductAttributeValue = true;
                    }
                    $this->join('attribute_values', 'attribute_values.id', '=', 'product_attributes.attribute_value_id');


                    //

                    $this->where(function ($query) use ($attributeValues) {
                        $i = 0;
                        foreach ($attributeValues as $attribute_id => $value) {
                            // $f = $i?'orWhere':'where';
                            if (!$i) {
                                $query->where(function ($query) use ($attribute_id, $value) {
                                    $query->where('attribute_values.attribute_id', $attribute_id);
                                    if (is_array($value['value'])) {
                                        $query->whereIn('attribute_values.' . $value['type'] . '_value', $value['value']);
                                    } else {
                                        $query->where('attribute_values.' . $value['type'] . '_value', $value['value']);
                                    }
                                });
                            } else {
                                $query->orWhere(function ($query) use ($attribute_id, $value) {
                                    $query->where('attribute_values.attribute_id', $attribute_id);
                                    if (is_array($value['value'])) {
                                        $query->whereIn('attribute_values.' . $value['type'] . '_value', $value['value']);
                                    } else {
                                        $query->where('attribute_values.' . $value['type'] . '_value', $value['value']);
                                    }
                                });
                            }

                            $i++;
                        }
                    });

                    $this->havingRaw('count(DISTINCT product_attributes.attribute_value_id) = ' . count($reqAttrs));
                    if (!$this->isGroupByProductId) {

                        $this->groupBy('products.id');
                        $this->isGroupByProductId = true;
                    }

                    // addslashes
                }
            }
        }


        $args = $this->parsePaginateParam($request, $args);

        // lấy kết qua

        $this->with(['category', 'metadatas', 'attrs']);

        $results = $this->get($args);
        // nếu tham số có yêu cau paginate
        if ($this->hasPaginateParam) {
            if ($params = array_remove_key($request->all(), 'page')) {
                // them query string vào url
                $results->appends($params);
            }
        }
        return $this->parseCollection($results);
    }

    public function getSuggestionProducts($product_id, $args = [])
    {
        /**
         * @var StyleSetItemRepository
         */
        // $setItemRepository = app(StyleSetItemRepository::class);
        // $set = $setItemRepository->first([
        //     'product_id' => $product_id,
        //     '@orderByRaw' => 'RAND()'
        // ]);
        // $style_set_id = 0;
        // if ($set) {
        //     $style_set_id = $set->style_set_id;
        // }
        $rs = $this
            // ->join('style_set_items', 'style_set_items.product_id', '=', 'products.id')
            // ->where('products.id', '!=', $product_id)
            // ->where('style_set_items.style_set_id', $style_set_id)
            ->select('products.*', 'style_set_items.style_set_id')
            // ->groupBy('products.id')
            ->get($args);
        return $this->parseCollection($rs);
    }

    // /**
    //  * get Style Set's Products
    //  *
    //  * @param int|PersonalStyleSet|StyleSetMask $style
    //  * @return ProductCollection
    //  */
    // public function getSetComboByStyle($style)
    // {
    //     if (is_string($style) || is_numeric($style)) {
    //         $style = app(StyleSetRepository::class)->detail(['id' => $style, '@withFullData' => true]);
    //         // dd($style);
    //     }

    //     $products = [];
    //     if ($style) {
    //         $params = $style->product_parameters;
    //         if ($params) {
    //             $i = 0;

    //             $having = [];
    //             foreach ($params as $param) {
    //                 $select = ['products.*'];
    //                 $tag_count = count($param['tags']);
    //                 $cate_count = count($param['categories']);
    //                 $attribute_count = count($param['attributes']);

    //                 if ($tag_count + $cate_count + $attribute_count) {
    //                     $raw = "(
    //                         SELECT COUNT(PD.id) FROM products as PD " .
    //                         ($tag_count ? " LEFT JOIN tag_refs as TR on TR.ref_id = PD.id " : "") .
    //                         ($cate_count ? " LEFT JOIN category_refs as CR on CR.ref_id = PD.id " : "") .
    //                         ($param['attributes'] ? " LEFT JOIN product_attributes as PA on PA.product_id = PD.id " : "") .
    //                         " WHERE PD.id = products.id " .
    //                         ($tag_count ? " AND TR.ref = '" . Product::REF_KEY . "'" : "") .
    //                         ($cate_count ? " AND CR.ref = '" . Product::REF_KEY . "'" : "") .
    //                         ($tag_count ? " AND TR.tag_id in (" . implode(',', $param['tags']) . ")" : "") .
    //                         ($cate_count ? " AND CR.category_id in (" . implode(',', $param['categories']) . ")" : "") .
    //                         ($attribute_count ? " AND PA.attribute_value_id in (" . implode(',', $param['attributes']) . ")" : "") .
    //                         " GROUP BY PD.id" .
    //                         ($tag_count + $cate_count + $attribute_count > 0 ? " having " : "") .
    //                         ($tag_count > 0 ? " count(DISTINCT TR.tag_id) = " . $tag_count : "") .
    //                         ($cate_count > 0 ? ($tag_count > 0 ? ' AND' : '') . " count(DISTINCT CR.category_id) > 0 " : "") .
    //                         ($attribute_count > 0 ? ($tag_count + $cate_count > 0 ? ' AND' : '') . " count(DISTINCT PA.attribute_value_id) = " . $attribute_count : "") .

    //                         "
    //                     ) as attr_count";
    //                     $select[] = DB::raw($raw);
    //                     $product = $this->where('products.available_in_store', '>', 0)->where('products.status', '>', 0)->select(...$select)->havingRaw("attr_count > 0")->first([
    //                         '@with' => ['promoAvailable'],
    //                         '@withOption' => true,
    //                         '@withGallery' => true,
    //                         '@withCategory' => true,
    //                         '@sorttype' => 1
    //                     ]);
    //                     if ($product) {
    //                         $product->style_attrs = $param['attributes'];
    //                         $products[] = $product;
    //                     }

    //                     $i++;
    //                 }
    //             }
    //         }
    //     }
    //     return new ProductCollection($products);
    // }
    // public function buildStyleConditionQuery($param, $i = 0)
    // {
    //     if ($param) {
    //         // $select = ['products.*'];
    //         // $having = [];
    //         $tag_count = count($param['tags']);
    //         $cate_count = count($param['categories']);
    //         $attribute_count = count($param['attributes']);

    //         if ($tag_count + $cate_count + $attribute_count) {
    //             // $raw = " as attr_count";
    //             // $this->selectable[] = DB::raw($raw);
    //             $this->havingRaw("(
    //                 SELECT COUNT(PD.id) FROM products as PD " .
    //                 ($tag_count ? " LEFT JOIN tag_refs as TR on TR.ref_id = PD.id " : "") .
    //                 ($cate_count ? " LEFT JOIN category_refs as CR on CR.ref_id = PD.id " : "") .
    //                 ($param['attributes'] ? " LEFT JOIN product_attributes as PA on PA.product_id = PD.id " : "") .
    //                 " WHERE PD.id = products.id " .
    //                 ($tag_count ? " AND TR.ref = '" . Product::REF_KEY . "'" : "") .
    //                 ($cate_count ? " AND CR.ref = '" . Product::REF_KEY . "'" : "") .
    //                 ($tag_count ? " AND TR.tag_id in (" . implode(',', $param['tags']) . ")" : "") .
    //                 ($cate_count ? " AND CR.category_id in (" . implode(',', $param['categories']) . ")" : "") .
    //                 ($attribute_count ? " AND PA.attribute_value_id in (" . implode(',', $param['attributes']) . ")" : "") .
    //                 " GROUP BY PD.id" .
    //                 ($tag_count + $cate_count + $attribute_count > 0 ? " having " : "") .
    //                 ($tag_count > 0 ? " count(DISTINCT TR.tag_id) = " . $tag_count : "") .
    //                 ($cate_count > 0 ? ($tag_count > 0 ? ' AND' : '') . " count(DISTINCT CR.category_id) > 0 " : "") .
    //                 ($attribute_count > 0 ? ($tag_count + $cate_count > 0 ? ' AND' : '') . " count(DISTINCT PA.attribute_value_id) = " . $attribute_count : "") .

    //                 "
    //             ) > 0");
    //         }
    //     }
    //     return $this;
    // }

    public function beforeFilter($request)
    {
        $this->isFilter = true;
        if ($request->category) {
            $cat = $request->category;
            $cats = explode(',', str_replace(' ', '', $cat));
            $this->leftJoin('category_refs as CR', 'CR.ref_id', '=', 'products.id')
                ->where('CR.ref', Product::REF_KEY)->groupBy('products.id')
                ->whereIn('CR.category_id', $cats)
                ->havingRaw('count(DISTINCT CR.category_id) > 0');
        }
    }

    /**
     * lấy giá của sản phẩm theo thuộc tính
     * @param int $product_id
     * @param array $attributes
     * @return array
     */
    public function checkPrice($product_id, array $attributes = [], $quantity = 1)
    {
        /**
         * @var Product
         */
        if ($product = $this->findBy('id', $product_id)) {
            if (!is_numeric($quantity) || $quantity < 1) $quantity = 1;
            if ($quantity > $product->available_in_store) {
                $this->actionMessage = "Sản phẩm tạm hết hàng";
                // return false;
                $this->actionStatus = false;
                $quantity = $product->available_in_store;
            }
            $use_thumbnail = false;
            $thumbnail = null;

            $price = $product->on_sale ? $product->sale_price : $product->list_price;

            if ($attributes) {
                $attrs = (new ProductAttributeRepository())->getProductAttributeValues($product_id, $attributes, 1, 1);
                $change = 0;
                if (count($attrs)) {
                    foreach ($attrs as $key => $attr) {
                        if ($attr->use_thumbnail && $attr->thumbnail) {
                            $use_thumbnail = true;
                            $thumbnail = asset(content_path('products/attributes/thumbnails/' . $attr->thumbnail));
                        }
                        if ($attr->price_type) {
                            if (!$change) {
                                $price = $attr->price;
                                $change = 1;
                            }
                        } else {
                            $price += $attr->price;
                        }
                    }
                }
            }
            $price = $product->getFinalPrice($price);
            $product = $this->parseDetail($product);
            $price = $price * $quantity;
            if ($product->price_status < 0) $price = -1;
            $status = $this->actionStatus;
            $message = $this->actionMessage;

            return compact('product', 'price', 'quantity', 'status', 'message', 'use_thumbnail', 'thumbnail');
        }
        return null;
    }


    public function groupByProductID()
    {
        if (!$this->isGroupByProductId) {
            $this->groupBy('products.id');
            $this->isGroupByProductId = true;
        }
        return $this;
    }


    public function buildStyleQuery($style)
    {
        // if (is_string($style) || is_numeric($style)) {
        //     $style = app(StyleSetRepository::class)->detail(['id' => $style, '@withFullData' => true]);
        //     // dd($style);
        // }
        // if ($style) {
        //     $params = $style->product_parameters;
        //     if ($params) {
        //         $i = 0;
        //         $select = ['products.*'];
        //         $having = [];
        //         foreach ($params as $param) {
        //             $tag_count = count($param['tags']);
        //             $cate_count = count($param['categories']);
        //             $attribute_count = count($param['attributes']);

        //             if ($tag_count + $cate_count + $attribute_count) {
        //                 $raw = "(
        //                     SELECT COUNT(PD_$i.id) FROM products as PD_$i " .
        //                     ($tag_count ? " LEFT JOIN tag_refs as TR_$i on TR_$i.ref_id = PD_$i.id " : "") .
        //                     ($cate_count ? " LEFT JOIN category_refs as CR_$i on CR_$i.ref_id = PD_$i.id " : "") .
        //                     ($param['attributes'] ? " LEFT JOIN product_attributes as PA_$i on PA_$i.product_id = PD_$i.id " : "") .
        //                     " WHERE PD_$i.id = products.id " .
        //                     ($tag_count ? " AND TR_$i.ref = '" . Product::REF_KEY . "'" : "") .
        //                     ($cate_count ? " AND CR_$i.ref = '" . Product::REF_KEY . "'" : "") .
        //                     ($tag_count ? " AND TR_$i.tag_id in (" . implode(',', $param['tags']) . ")" : "") .
        //                     ($cate_count ? " AND CR_$i.category_id in (" . implode(',', $param['categories']) . ")" : "") .
        //                     ($attribute_count ? " AND PA_$i.attribute_value_id in (" . implode(',', $param['attributes']) . ")" : "") .
        //                     " GROUP BY PD_$i.id" .
        //                     ($tag_count + $cate_count + $attribute_count > 0 ? " having " : "") .
        //                     ($tag_count > 0 ? " count(DISTINCT TR_$i.tag_id) = " . $tag_count : "") .
        //                     ($cate_count > 0 ? ($tag_count > 0 ? ' AND' : '') . " count(DISTINCT CR_$i.category_id) > 0 " : "") .
        //                     ($attribute_count > 0 ? ($tag_count + $cate_count > 0 ? ' AND' : '') . " count(DISTINCT PA_$i.attribute_value_id) = " . $attribute_count : "") .

        //                     "
        //                 ) as condition_$i";


        //                 if ($this->isFilter) {
        //                     $this->selectable[] = DB::raw($raw);
        //                 } else {
        //                     $select[] = DB::raw($raw);
        //                 }
        //                 $having[] = "condition_$i > 0";
        //                 $i++;
        //             }
        //         }

        //         if ($i > 0) {
        //             if (!$this->isFilter) {
        //                 $this->select(...$select);
        //             }
        //             $this->havingRaw("(" . implode(" or ", $having) . ")");
        //         }
        //     }
        // }
    }

    public function matchCategory($categoryIDs, $type = 'any')
    {
        if (!$categoryIDs) return $this;
        $this->leftJoin('category_refs', function ($join) {
            $join->on('category_refs.ref_id', '=', 'products.id')->on('category_refs.ref', '=', DB::raw("'" . Product::REF_KEY . "'"));
        });

        if (is_array($categoryIDs))
            $this->whereIn('category_refs.category_id', $categoryIDs);
        else
            $this->where('category_refs.category_id', $categoryIDs);

        $this->selectable[] = DB::raw("count(category_refs.category_id) as category_count");

        $this->havingRaw('count(category_refs.category_id) ' . ($type == 'all' && is_array($categoryIDs) ? '= ' . count($categoryIDs) : '> 0'));
        $this->needBuildJoin = true;
        $this->needBuildSelect = true;
        $this->groupByProductID();
        return $this;
    }

    public function matchLabel($labelIDs, $type = 'any')
    {
        if (!$labelIDs) return $this;
        $this->leftJoin('product_label_refs', function ($join) {
            $join->on('product_label_refs.ref_id', '=', 'products.id');
            $join->on('product_label_refs.ref', '=', DB::raw("'" . Product::REF_KEY . "'"));
        });
        if (is_array($labelIDs))
            $this->whereIn('product_label_refs.label_id', $labelIDs);
        else
            $this->where('product_label_refs.label_id', $labelIDs);
        $this->havingRaw('count(product_label_refs.label_id) ' . ($type == 'all' && is_array($labelIDs) ? '= ' . count($labelIDs) : '> 0'));
        $this->selectable[] = DB::raw("count(product_label_refs.label_id) as label_count");
        $this->needBuildJoin = true;
        $this->needBuildSelect = true;
        $this->groupByProductID();
        return $this;
    }

    public function matchTag($tagIDs, $type = 'any')
    {
        if (!$tagIDs) return $this;
        $this->leftJoin('tag_refs', function ($join) {
            $join->on('tag_refs.ref_id', '=', 'products.id');
            $join->on('tag_refs.ref', '=', DB::raw("'" . Product::REF_KEY . "'"));
        });
        if (is_array($tagIDs))
            $this->whereIn('tag_refs.tag_id', $tagIDs);
        else
            $this->where('tag_refs.tag_id', $tagIDs);
        $this->havingRaw('count(tag_refs.tag_id) ' . ($type == 'all' && is_array($tagIDs) ? '= ' . count($tagIDs) : '> 0'));
        $this->selectable[] = DB::raw("count(tag_refs.tag_id) as tag_count");
        $this->needBuildJoin = true;
        $this->needBuildSelect = true;
        $this->groupByProductID();

        return $this;
    }

    /**
     * xữ lý trước khi lấy dữ liệu
     *
     * @param array $args
     * @return void
     */
    public function beforeGetData($args = [])
    {

        $a = false;
        $anyCateKeys = ['category', 'incategories', 'cate', 'anycate', 'any:category', 'any:categories', 'any:cate', 'matchanycategory', 'matchanycategories', 'hasanycategory', 'hasanycategories', 'anycategory', 'anycategories', 'incategory', 'inanycategory', 'incategories', 'in:category', 'in:categories'];
        $allCateKeys = ['allcategory', 'hascategories', 'havecategories', 'allcate', 'all:category', 'all:categories', 'all:cate', 'matchallcategory', 'matchallcategories', 'hasallcategory', 'hasallcategories', 'allcategory', 'allcategories', 'matchcategory', 'matchallcategory', 'matchcategories', 'match:category', 'match:categories'];
        $anyLabelKeys = ['matchanylabel', 'matchanylabels', 'hasanylabel', 'anylabel', 'anylabels', 'inlabel', 'inlabels'];
        $allLabelKeys = ['all:label', 'matchalllabel', 'matchlabels', 'hasalllabel', 'alllabel'];
        $anyTagKeys = ['any:tag', 'matchanytag', 'matchanytags', 'hasanytag', 'hasanytags', 'anytag', 'anytags', 'intag', 'intags', 'in:tags'];
        $allTagKeys = ['all:tag', 'matchalltag', 'matchtags', 'hasalltag', 'alltag'];

        foreach ($args as $key => $value) {
            $k = strtolower($key);
            $f = substr($k, 1);
            $s = substr($k, 0, 1);
            if ($s != '@') {
                continue;
            } elseif (in_array($f, $anyCateKeys)) {
                $this->matchCategory($value, 'any');
            } elseif (in_array($f, $allCateKeys)) {
                $this->matchCategory($value, 'all');
            } elseif (in_array($f, $anyLabelKeys)) {
                $this->matchLabel($value, 'any');
            } elseif (in_array($f, $allLabelKeys)) {
                $this->matchLabel($value, 'all');
            } elseif (in_array($f, $anyTagKeys)) {
                $this->matchTag($value, 'any');
            } elseif (in_array($f, $allTagKeys)) {
                $this->matchTag($value, 'all');
            } elseif (in_array($f, ['withvariant', 'loadvariant'])) {
                $this->with('variants');
            } elseif (in_array($f, ['withoption', 'loadoption'])) {
                $this->with('orderOptions');
            } elseif (in_array($f, ['withreview', 'loadreview'])) {
                $this->buildWithReviewQuery();
            } elseif (in_array($f, ['withreviewanalytics', 'reviewanalytics'])) {
                $this->buildReviewAnalyticsQuery();
            } elseif (in_array($f, ['style', 'styleset'])) {
                $this->buildStyleQuery($value);
            } elseif (in_array($f, ['styleparameters', 'styleconditions', 'filterdata'])) {
                $this->buildStyleConditionQuery($value);
            } elseif (in_array($f, ['promo']) && $value) {
                $this->withCount(['promoable' => function ($q) use ($value) {
                    $q->where('promos.id', $value)->where('promos.started_at', '<=', CURRENT_TIME_STRING)->where('promos.finished_at', '>=', CURRENT_TIME_STRING);
                }])->havingRaw('promoable_count > 0');
            } elseif (in_array($f, ['sort', 'sorttype', 'sort_type']) && !$a) {
                $this->parseSortBy($value);
                $a = true;
            } else {
                continue;
            }
            unset($args[$key]);
        }
        if (!$this->hasSortby && !isset($args['@orderBy']) && !isset($args['@order_by']) && $this->defaultSortBy && !$a) {
            $this->parseSortBy(1);
        }
        return $args;
    }

    public function buildWithReviewQuery()
    {
        $review_approve_request = $this->settings ? $this->settings->review_approve_request : false;
        if ($review_approve_request) {
            $this->with(['reviews' => function ($query) {
                $query->where('product_reviews.approved', 1);
            }]);
        } else {
            $this->with('reviews');
        }
    }

    public function buildReviewAnalyticsQuery()
    {
        $review_approve_request = $this->settings ? $this->settings->review_approve_request : false;
        for ($i = 1; $i < 6; $i++) {
            // $this->leftJoin('product_reviews as pr' . $i, function ($join) use ($i, $review_approve_request) {
            //     $join->on('pr' . $i . '.product_id', '=', DB::raw('products.id'))->on('pr' . $i . '.rating', DB::raw($i));
            //     if($review_approve_request){
            //         $join->on('pr' . $i . '.approved', '=', DB::raw(1));
            //     }
            // });
            $raw = "(
                SELECT COUNT(1)
                FROM product_reviews as pr{$i}
                WHERE pr{$i}.product_id = products.id
                    AND pr{$i}.rating = $i
                ".($review_approve_request ? "AND pr{$i}.approved = 1": '')."

            ) as rating_{$i}_count";
            // $raw = "count(pr{$i}.id) as rating_{$i}_count";
            $this->selectable[] = DB::raw($raw);
        }
        $this->selectable[] = DB::raw("1 as has_review_analytics");
        // $this->selectable[] = DB::raw("a.a");

        $this->needBuildJoin = true;
        $this->needBuildSelect = true;
        $this->groupByProductID();


        return $this;
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

    /**
     * Undocumented function
     *
     * @param string $type
     * @return void
     */
    protected function orderBySeller($type = 'DESC')
    {
        if (strtoupper($type) != 'ASC') $type = 'DESC';
        $this->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::COMPLETED)
            ->groupBy('products.id');
        $this->select('products.*')->selectRaw('COUNT(order_items.product_id) AS sell_total');
        $this->orderByRaw('COUNT(order_items.product_id) ' . $type);
    }
    /**
     * tạo cây danh mục
     * @param int $category_id
     * @return string ví dụ: " 1, 4, 6,"
     */
    public function makeCategoryMap($category_id = 0)
    {
        if (!$category_id) return null;
        $rep = new CategoryRepository();
        $cate = $rep->find($category_id);
        $str = '';
        if ($cate) {
            $str = ' ' . implode(', ', $cate->getMap()) . ',';
        }

        return $str;
    }


    public function getSelectOptions(array $args = [])
    {
        $data = [' -- Chọn một sản phẩm -- '];

        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        $this->join('categories', 'categories.id', '=', 'products.category_id');
        $this->select('products.name', 'products.sku', 'products.id', 'categories.name as category_name');
        if ($list = $this->notTrashed()->get(array_merge(['@limit' => 10, '@order_by' => 'RAND()'], $params))) {
            foreach ($list as $item) {
                $data[$item->id] = htmlentities("$item->sku - $item->name");
            }
        }
        return $data;
    }

    /**
     * get Product option
     * @param Request $request
     * @param array $args
     * @return array
     */
    public function getProductSelectOptions($request, array $args = [])
    {
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('products.id', $request->ignore);
        }
        $data = [];
        if ($request->map_id) {
            $this->like('products.category_id', " $request->map_id,");
        }
        if ($list = $this->getFilter($request, $args)) {
            foreach ($list as $item) {
                $data[$item->id] = htmlentities("$item->sku - $item->name");
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
    public function getProductTagData($request, array $args = [])
    {
        if ($request->ignore && is_array($request->ignore)) {
            $this->whereNotIn('products.id', $request->ignore);
        }
        $data = [];
        if ($request->map_id) {
            $this->like('products.category_id', " $request->map_id,");
        }
        if ($list = $this->getFilter($request, $args)) {
            foreach ($list as $item) {
                $data[] = [
                    'id' => $item->id,
                    'name' => htmlentities("$item->sku - $item->name")
                ];
            }
        }
        return $data;
    }




    /**
     * ref Query
     */
    public function refQuery()
    {
        return $this->setWhereable([
            'ref'          => 'product_refs.ref',
            'ref_id'       => 'product_refs.ref_id',
            'id'           => 'products.id',
        ])
            ->join('product_refs', 'products.id', '=', 'product_refs.product_id')
            ->groupBy('products.id')
            ->select('products.id', 'products.sku', 'products.name', 'products.slug');
    }


    /**
     * lấy product có ref hoặc ko
     */
    public function getRefProducts(string $ref = 'link', $ref_id = 0, array $args = [])
    {
        $args = array_merge(compact('ref', 'ref_id'), $args);
        return $this->refQuery()->get($args);
    }

    /**
     * get Order Input Data
     * @param int $product_id
     * @param array $attribute_value_id
     */
    public function getOrderInputData($product_id, array $attribute_value_id = [])
    {
        $data = [];
        if ($product = $this->find($product_id)) {
            $data['product'] = $product;
            $attrs = [];
            if (count($attr_values = $this->attributeRepository->getOrderAttributeValues($product->category_id, $product_id))) {
                foreach ($attr_values as $attr) {
                    if (!array_key_exists($attr->attribute_id, $attrs)) {
                        $attrs[$attr->attribute_id] = [
                            'data-attribute-id' => $attr->attribute_id,
                            'type' => $attr->show_type == 'dropdown' ? 'select' : $attr->show_type,
                            'template' => $attr->show_type,
                            'name' => $attr->name,
                            'label' => $attr->label,
                            'data' => []
                        ];
                    }
                    if (in_array($attr->attribute_value_id, $attribute_value_id)) {
                        $attrs[$attr->attribute_id]['value'] = $attr->attribute_value_id;
                    }
                    if (!$attrs[$attr->attribute_id]['data']) {
                        $attrs[$attr->attribute_id]['default'] = $attr->attribute_value_id;
                    }
                    $attrs[$attr->attribute_id]['data'][$attr->attribute_value_id] = $attr->text_value ?? $attr->{$attr->value_type . '_value'};
                }
            }
            $data['attr_values'] = $attrs;
        }
        return $data;
    }


}
