<?php

use App\Engines\RequestAttribute;
use App\Masks\CustomerCentricData\BodyShapeCollection;
use App\Masks\CustomerCentricData\BodyShapeMask;
use App\Masks\StyleSets\Personal\StyleSetCollection;
use App\Masks\StyleSets\Personal\StyleSetMask;
// use App\Models\BodyShape;
use App\Models\Order;
// use App\Models\PersonalStyleSet;
use App\Models\ProductLabel;
use App\Models\Promo;
use App\Repositories\Affiliates\AffiliateRepository;
// use App\Repositories\Banners\BannerRepository;
use App\Repositories\CustomerCentricData\BodyShapeRepository;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Products\CategoryRepository;
use App\Repositories\Products\AttributeRepository;
use App\Repositories\Products\CollectionRepository;
use App\Repositories\Products\LabelRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Products\ReviewRepository;
use App\Repositories\Promos\PromoRepository;
// use App\Repositories\StyleSets\Personal\StyleSampleRepository;
// use App\Repositories\StyleSets\Personal\StyleSetRepository as PersonalStyleSetRepository;
// use App\Repositories\StyleSets\Personal\TemplateRepository;
// use App\Repositories\StyleSets\StyleSetRepository;
use App\Repositories\Tags\TagRepository;
// use App\Services\Styles\StyleConfigService;
use Crazy\Helpers\Arr as CrazyArr;
use Gomee\Files\Filemanager;
use Gomee\Helpers\Arr;

if (!function_exists('get_product_parent_category_options')) {
    /**
     * lấy danh sách danh mục cha
     * @param mixed $max_level
     * @return array
     */
    function get_product_parent_category_options($max_level = 2)
    {
        return CategoryRepository::getParentSelectOptions($max_level);
    }
}

if (!function_exists('get_product_category_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_product_category_options($args = [], $firstDefault = null)
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        $options = CategoryRepository::getCategorySelectOptions(array_merge(['trashed_sratus' => 0], $params));
        if ($firstDefault) {
            $options[0] = $firstDefault;
        }
        return $options;
    }
}
if (!function_exists('get_product_category_tree_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_product_category_tree_options($args = [], $firstDefault = null)
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        $options = CategoryRepository::getCategoryCheckTreeOptions(array_merge(['trashed_sratus' => 0], $params));
        if ($firstDefault) {
            $options[0] = $firstDefault;
        }
        return $options;
    }
}


if (!function_exists('get_product_category_list_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_product_category_list_options($args = [], $defaultFirst = null, $value_key = 'id', $text_key = 'name')
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        $a = array_merge(['trashed_status' => 0], $params);
        /**
         * repository
         * @var CategoryRepository
         */
        $repository = app(CategoryRepository::class);
        return $repository->getDataOptions($a, $defaultFirst, $value_key, $text_key);
    }
}

if (!function_exists('get_product_attribute_category_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_product_attribute_category_options($args = [])
    {
        $options = CategoryRepository::getCategorySelectOptions(array_merge(['trashed_sratus' => 0], $args));
        $options[0] = "-- Tất cả --";
        return $options;
    }
}

if (!function_exists('get_product_category_map')) {
    /**
     * lấy map danh mục thuộc tinh
     * @param mixed $args
     * @return array
     */
    function get_product_category_map($args = [])
    {
        $options = CategoryRepository::getCategoryMap($args);
        return $options;
    }
}

if (!function_exists('get_product_attribute_input_data')) {
    /**
     * lấy danh sách thuộc tính để chuyển về input
     * @param int $category_id mã danh mục
     * @param bool $all lấy tất cả bao gồm thuộc tính chung
     * @return array mảng gồm required và optional
     */
    function get_product_attribute_input_data($category_id = 0, $all = false): array
    {
        return (new AttributeRepository())->getAttributeInput($category_id, $all);
    }
}


if (!function_exists('get_product_variant_attribute_input_data')) {
    /**
     * lấy danh sách thuộc tính để chuyển về input
     * @param int $product_id mã danh mục
     * @param bool $all lấy tất cả bao gồm thuộc tính chung
     * @return array mảng gồm required và optional
     */
    function get_product_variant_attribute_input_data($product_id = 0, $all = false): array
    {
        return (new AttributeRepository())->getVariantAttributeInput($product_id, $all);
    }
}


if (!function_exists('get_product_refs')) {
    /**
     * lấy danh sách tag
     * @param string $ref
     * @param int $ref_id
     * @param array $args
     */
    function get_product_refs(string $ref = 'link', $ref_id = 0, array $args = [])
    {
        return (new ProductRepository())->getRefProducts($ref, $ref_id, array_merge(['trashed_sratus' => 0], $args));
    }
}


if (!function_exists('get_product_tag_data')) {
    /**
     * lấy danh sách tag
     * @param string $ref
     * @param int $ref_id
     * @param array $args
     */
    function get_product_tag_data(string $ref = 'link', $ref_id = 0, array $args = [])
    {
        $data = [];
        if (count($tags = get_product_refs($ref, $ref_id, $args))) {
            foreach ($tags as $tag) {
                $data[] = [
                    'id' => $tag->id,
                    'name' => htmlentities("$tag->sku - $tag->name")
                ];
            }
        }
        return $data;
    }
}

if (!function_exists('get_product_select_options')) {
    /**
     * lấy danh sách sản phẩm
     * @param array $args
     */
    function get_product_select_options(array $args = [])
    {
        $rep = new ProductRepository();


        return $rep->getSelectOptions($args);
    }
}



if (!function_exists('get_product_label_options')) {
    /**
     * lấy danh sách sản phẩm
     * @param array $args
     */
    function get_product_label_options(array $args = [])
    {
        $rep = app(LabelRepository::class);


        return $rep->getSelectOptions($args);
    }
}


if (!function_exists('get_order_product_item_input')) {
    /**
     * lấy thông tin product để render input
     * @param int $product_id
     *
     * @return array
     */
    function get_order_product_item_input($product_id, $default_list = [])
    {
        static $productRepository = null;
        if (!$productRepository) {
            $productRepository = new ProductRepository();
        }
        return $productRepository->getOrderInputData($product_id, $default_list);
    }
}

if (!function_exists('get_product_image')) {
    /**
     * lấy hình ảnh sản phẩm nếu có tồn tại
     */
    function get_product_image($product_image, $size = null)
    {
        $fd = get_content_path('products');
        if ($product_image) {
            $featured_image = $product_image;
            if ($size) {
                if (file_exists($f = $fd . '/' . $size . '/' . $featured_image)) return asset($f);
            }
            if (file_exists($f = $fd . '/' . $featured_image)) return asset($f);
        }
        return asset('static/images/product.png');
    }
}



if (!function_exists('get_product_category')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return \App\Models\Category
     */
    function get_product_category(array $params = [])
    {
        return app(CategoryRepository::class)->mode('mask')->detail(get_parse_query_args($params));
    }
}


if (!function_exists('get_product_categories')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return \App\Models\Category[]|\App\Masks\Categories\CategoryCollection
     */
    function get_product_categories(array $params = [])
    {
        return app(CategoryRepository::class)->mode('mask')->getCategories(get_parse_query_args($params));
    }
}



if (!function_exists('set_attribute_category_map')) {
    /**
     * thiet lap map cho thuoc tinh san pham
     * @param array $map
     */
    function set_attribute_category_map(array $map = [])
    {
        if (!$map) $map = [0];
        set_web_data('attribute_category_map', $map);
    }
}

if (!function_exists('get_attribute_category_map')) {
    /**
     * lấy map cho truy van thuoc tinh san pham
     * @return array $map
     */
    function get_attribute_category_map()
    {
        $map = get_web_data('attribute_category_map');
        if (!$map) $map = [];
        return $map;
    }
}


if (!function_exists('get_attribute_request_name_prefix')) {
    /**
     * lấy map cho truy van thuoc tinh san pham
     * @return array $map
     */
    function get_attribute_request_name_prefix()
    {
        return get_ecommerce_config('products.list.filter.attribute_prefix');
    }
}



if (!function_exists('set_attribute_request_data')) {
    /**
     * thiet lap du liệu lay tu request
     * @param array $data
     */
    function set_attribute_request_data(array $data = [])
    {
        set_web_data('attribute_request_data', $data);
    }
}


if (!function_exists('get_attribute_request_data')) {
    /**
     * lay du liệu thuộc tính từ request
     * @return array $data
     */
    function get_attribute_request_data()
    {
        if (!is_array($data = get_web_data('attribute_request_data'))) {

            // lấy thuộc tính từ request
            $data = Arr::prefix(request()->all(), get_attribute_request_name_prefix(), true, function ($val) {
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
            set_web_data('attribute_request_data', $data);
        }
        return $data;
    }
}


if (!function_exists('get_attribute_from_request')) {
    /**
     * lay du liệu thuộc tính từ request
     * @return array $data
     */
    function get_attribute_from_request()
    {
        return new RequestAttribute();
    }
}




if (!function_exists('get_product_attribute')) {
    /**
     * lấy chi tiet thuoc tinh
     * @param array $params
     * @return \App\Models\Arreibute
     */
    function get_product_attribute(array $params = [])
    {
        return app(AttributeRepository::class)->mode('mask')->detail(get_parse_query_args($params));
    }
}



if (!function_exists('get_product_category_attributes')) {
    /**
     * lấy danh sach thuoc tinh
     * @param array $params
     * @return \App\Models\Attribute[]
     */
    function get_product_category_attributes(array $params = [])
    {
        $a = get_parse_query_args($params);
        $map = get_attribute_category_map();
        if (!$map) $map = [0];
        $a['category_id'] = $map;
        $a['is_query'] = 1;
        return app(AttributeRepository::class)->mode('mask')->getAttributeData($a);
    }
}


if (!function_exists('get_products')) {
    /**
     * lấy thông tin sản phẩm
     *
     * @param array $args
     */
    function get_products($args = [])
    {
        $a = get_parse_query_args($args);
        $a['@withVariant'] = true;
        return app(ProductRepository::class)->mode('mask')->getProducts($a);
    }
}


if (!function_exists('get_product')) {
    /**
     * lấy chi tiết sản phẩm
     *
     * @param array $args
     */
    function get_product($args = [])
    {
        $a = get_parse_query_args($args);

        return app(ProductRepository::class)->mode('mask')->getProductDetail($a);
    }
}

if (!function_exists('get_product_count')) {
    /**
     * lấy chi tiết sản phẩm
     *
     * @param array $args
     */
    function get_product_count($args = [])
    {
        $a = get_parse_query_args($args);

        return app(ProductRepository::class)->count($a);
    }
}

if (!function_exists('get_active_product')) {
    /**
     * lấy chi tiết sản phẩm được active
     * @return App\Masks\Products\ProductMask|null
     */
    function get_active_product($args = [])
    {
        return get_active_model('product');
    }
}


if (!function_exists('get_product_sortby_data')) {
    /**
     * thông tin sap xep san phẩm
     *
     * @return array
     */
    function get_product_sortby_data()
    {
        return get_ecommerce_config('products.list.sortby', []);
    }
}
if (!function_exists('get_product_page_tabs')) {
    /**
     * thông tin sap xep san phẩm
     *
     * @return array
     */
    function get_product_page_tabs()
    {
        return get_ecommerce_config('products.list.tabs', []);
    }
}

if (!function_exists('get_product_sortby_options')) {
    /**
     * thông tin sap xep san phẩm
     *
     * @return array
     */
    function get_product_sortby_options($firstDefault = null)
    {
        $a = $firstDefault?[$firstDefault]:[];
        $options = get_ecommerce_config('products.list.sortby', []);
        foreach ($options as $key => $value) {
            $a[$key] = $value;
        }
        $a['seller'] = "Sản phẩm bán chạy";

        return $a;
    }
}

if (!function_exists('get_product_category_sortby_options')) {
    /**
     * thông tin sap xep danh mục san phẩm
     *
     * @return array
     */
    function get_product_category_sortby_options()
    {
        $options = get_ecommerce_config('categories.list.sortby', []);
        // $options['seller'] = "Sản phẩm bán chạy";
        return $options;
    }
}




if (!function_exists('get_product_price_range')) {
    /**
     * thông tin khoang gia san phẩm
     *
     * @return Arr
     */
    function get_product_price_range()
    {
        $repo = app(ProductRepository::class);
        $lower = $repo->first(['@order_by' => ['list_price' => 'ASC']]);
        $heighter = $repo->first(['@order_by' => ['list_price' => 'DESC']]);

        return new Arr([
            'low' => $lower ? $lower->list_price : 0,
            'height' => $heighter ? $heighter->list_price : 0
        ]);
    }
}



if (!function_exists('get_product_tags')) {
    /**
     * lấy danh sách tag
     * @param string $ref
     * @param int $ref_id
     * @param array $args
     */
    function get_product_tags(array $args = [])
    {
        return (new TagRepository())->getRefTags('product', 0, $args);
    }
}

if (!function_exists('get_set_combo_products')) {
    /**
     * lấy thông tin sản phẩm
     *
     * @param int|PersonalStyleSet|StyleSetMask $style
     */
    function get_set_combo_products($style)
    {
        /**
         * @var ProductRepository
         */
        $repository = app(ProductRepository::class);
        return $repository->getSetComboByStyle($style);
    }
}



if (!function_exists('get_promo_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_promo_options($args = [])
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        return (new PromoRepository())->getPromoAvailableOptions(array_merge(['trashed_sratus' => 0], $params));
    }
}
if (!function_exists('get_promos')) {
    /**
     * lấy danh sách khuyến mãi
     * @param mixed $args
     * @return array
     */
    function get_promos($args = [])
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        return (new PromoRepository())->mode('mask')->get(array_merge(['trashed_sratus' => 0], $params));
    }
}

if (!function_exists('get_promo_detail')) {
    /**
     * lấy danh sách chi tiết
     * @param mixed $args
     * @return array
     */
    function get_promo_detail($args = [])
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        return (new PromoRepository())->mode('mask')->detail(array_merge(['trashed_sratus' => 0], $params));
    }
}



if (!function_exists('get_promo_type_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_promo_type_options()
    {
        return Promo::TYPE_LABELS;
    }
}


if (!function_exists('get_user_discount_tag_data')) {
    /**
     * lấy danh sách tag
     * @param int $ref_id
     * @param array $args
     */
    function get_user_discount_tag_data($promo_id = 0, array $args = [])
    {
        $data = [];
        if ($promo_id && $promo = app(PromoRepository::class)->with('users')->first(array_merge($args, ['id' => $promo_id]))) {
            if ($promo->users && count($promo->users)) {
                foreach ($promo->users as $tag) {
                    $data[] = [
                        'id' => $tag->id,
                        'name' => htmlentities($tag->name . ' (' . $tag->email . ')')
                    ];
                }
            }
        }
        return $data;
    }
}

if (!function_exists('can_review_product')) {
    /**
     * kiểm tra user hiện tại có được review hay không
     * @param int $product_id
     */
    function can_review_product($product_id)
    {
        /**
         * @var App\Repositories\Customers\CustomerRepository
         */
        static $customerRepository = null;
        /**
         * @var App\Repositories\Orders\OrderRepository
         */
        static $orderRepository = null;

        if (!$customerRepository) $customerRepository = app(CustomerRepository::class);
        if (!$orderRepository) $orderRepository = app(OrderRepository::class);
        return $orderRepository->checkProductBoughtCustomer($product_id, $customerRepository->getCustomerParams());
    }
}

if (!function_exists('get_currency_format')) {
    /**
     * lấy thông tin tiền tệ
     * @param int|float|double $total
     * @return string
     */
    function get_currency_format($total)
    {

        // sau này thiết lập sau
        static $ecommerce = null;
        if (!$ecommerce) $ecommerce = get_ecommerce_setting();
        if ($total < 0) return 'Liên Hệ';
        $decimals = $ecommerce->decimals ?? 0;
        $dsc_point = $ecommerce->decimal_pointer ?? ',';
        $thousands_sep = $ecommerce->thousands_sep ?? ',';
        $fm = number_format($total, $decimals, $dsc_point, $thousands_sep);
        $currency_type = $ecommerce->currency_type;
        return $currency_type ? ($ecommerce->currency_position == 'right' ? $fm . '' . $currency_type : $currency_type . '' . $fm) : $fm;
    }
}

if (!function_exists('get_currency_unit')) {
    /**
     * lấy thông tin đơn vị tiền tệ
     * @param int|float|double $total
     * @return string
     */
    function get_currency_unit($default = null)
    {
        // sau này thiết lập sau
        static $unit = null;
        if (!$unit) {
            $unit = get_ecommerce_setting()->currency_type;
            if (!$unit) $unit = $default;
        }
        return $unit;
    }
}

if (!function_exists('get_currency_unit_position')) {
    /**
     * lấy thông tin vị trí đơn vị tiền tệ
     * @return string
     */
    function get_currency_unit_position($default = null)
    {
        static $position = null;
        if (!$position) {
            $position = get_ecommerce_setting()->currency_position;
            if (!$position) $position = $default;
        }
        return $position;
    }
}


if (!function_exists('get_order_payment_methods')) {
    /**
     * lấy danh sách phương thức thanh toán đơn hàng
     * @return Arr
     */
    function get_order_payment_methods()
    {
        return new Arr(
            array_map(function ($item) {
                return new Arr($item);
            }, get_ecommerce_config('orders.payment_methods', []))
        );
    }
}


if (!function_exists('get_order_transaction_options')) {
    /**
     * lấy danh sách đơn hàng theo code
     * @return array
     */
    function get_order_transaction_options($args = [])
    {
        return (new OrderRepository())->getOptionForTransaction($args);
    }
}


if (!function_exists('get_order_status_list')) {
    /**
     * lấy danh sách trạng thái đơn hàng
     * @return CrazyArr
     */
    function get_order_status_list()
    {
        return new Arr(array_map(function ($item) {
            return new Arr($item);
        }, get_ecommerce_config('orders.status_list', [])));
    }
}

if (!function_exists('get_order_status_keys')) {
    /**
     * lấy danh sách key trạng thái đơn hàng
     * @return array
     */
    function get_order_status_keys()
    {
        return get_ecommerce_config('orders.status_keys', []);
    }
}
if (!function_exists('get_order_status_options')) {
    /**
     * lấy danh sách key trạng thái đơn hàng
     * @return array
     */
    function get_order_status_options($defaultFirst = null, $defaultFirstValue = null)
    {
        $data = $defaultFirst ? ($defaultFirstValue === null ? ['all' => $defaultFirst] : [$defaultFirstValue => $defaultFirstValue]):[];
        return array_merge($data, Order::getStatusLabels());
    }
}

if (!function_exists('get_customer_access_alow_status_list')) {
    /**
     * lấy danh sách trạng thái đơn hàng người mua hàng dc phép xem
     * @return array
     */
    function get_customer_access_alow_status_list()
    {
        return get_ecommerce_config('orders.customer_access_alow_status_list', []);
    }
}




if (!function_exists('get_product_reviews')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return ProductReviewCollection
     */
    function get_product_reviews(array $params = [], $advance = [])
    {
        // $args = array_merge(['approved' => 1], $params);
        $args = $params;
        return app(ReviewRepository::class)->mode('mask')->getReviews(get_parse_query_args($args), $advance);
    }
}

// if(!function_exists('get_banners')){

//     function get_banners(array $params = [])
//     {
//         // $args = array_merge(['approved' => 1], $params);
//         $args = $params;
//         return app(BannerRepository::class)->mode('mask')->getData(get_parse_query_args($args));
//     }
// }



if (!function_exists('get_product_tagss')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return \App\Models\ProductCollection[]|\App\Masks\Products\CollectionCollection
     */
    function get_product_tagss(array $params = [])
    {
        return app(TagRepository::class)->mode('mask')->getHaveProducts(get_parse_query_args($params));
    }
}





if (!function_exists('get_product_collections')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return \App\Models\ProductCollection[]|\App\Masks\Products\CollectionCollection
     */
    function get_product_collections(array $params = [])
    {
        return app(CollectionRepository::class)->mode('mask')->getCollections(get_parse_query_args($params));
    }
}



if (!function_exists('get_product_collection')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return \App\Models\ProductCollection|\App\Masks\Products\CollectionMask
     */
    function get_product_collection(array $params = [])
    {
        return app(CollectionRepository::class)->mode('mask')->detail(get_parse_query_args($params));
    }
}



if (!function_exists('get_collection_options')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return array<int, string>
     */
    function get_collection_options(array $params = [], $defaultFirst = null)
    {
        return app(CollectionRepository::class)->getDataOptions(get_parse_query_args($params), $defaultFirst);
    }
}




if (!function_exists('get_collection_tag_data')) {
    /**
     * lấy danh mục san pham
     * @param array $params
     * @return array<int, string>
     */
    function get_collection_tag_data(array $params = [])
    {
        return app(CollectionRepository::class)->getTagData(get_parse_query_args($params));
    }
}




if (!function_exists('get_collection_sortby_options')) {
    /**
     * thông tin sap xep danh mục san phẩm
     *
     * @return array
     */
    function get_collection_sortby_options()
    {
        return [
            "0" => "Auto",
            "1" => "Mới nhất",
            "2" => "Cũ nhất",
            "3" => "Tên (A-Z)",
            "4" => "Tên (Z-A)",
            "5" => "Ngẫu nhiên",

        ];
    }
}







// if (!function_exists('get_style_sets')) {
//     /**
//      * lấy danh sach style set
//      * @param array $params
//      * @return \App\Models\StyleSet[]|\App\Masks\StyleSets\StyleSetCollection
//      */
//     function get_style_sets(array $params = [])
//     {
//         return app(StyleSetRepository::class)->mode('mask')->getData(get_parse_query_args($params));
//     }
// }



// if (!function_exists('get_style_set')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return \App\Models\ProductCollection|\App\Masks\Products\CollectionMask
//      */
//     function get_style_set(array $params = [])
//     {
//         return app(StyleSetRepository::class)->mode('mask')->detail(get_parse_query_args($params));
//     }
// }



// if (!function_exists('get_body_shapes')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return BodyShape[]|BodyShapeMask[]|BodyShapeCollection|null
//      */
//     function get_body_shapes(array $params = [])
//     {
//         return app(BodyShapeRepository::class)->mode('mask')->getData($params);
//     }
// }

// if (!function_exists('get_sample_styles')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return PersonalStyleSet[]|StyleSetCollection
//      */
//     function get_sample_styles(array $params = [])
//     {
//         return app(StyleSampleRepository::class)->mode('mask')->with('items')->getData($params);
//     }
// }



// if (!function_exists('get_style_set_options')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return array<int, string>
//      */
//     function get_style_set_options(array $params = [], $defaultFirst = null)
//     {
//         return app(StyleSetRepository::class)->getDataOptions(get_parse_query_args($params), $defaultFirst);
//     }
// }


// if (!function_exists('get_style_template_detail')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return array<int, string>
//      */
//     function get_style_template_detail(array $params = [])
//     {
//         $rs = app(TemplateRepository::class)->getListWithAttributes(array_merge(['@limit' => 1], $params));
//         return $rs && count($rs) ? $rs[0] : null;
//     }
// }




// if (!function_exists('get_style_set_tag_data')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return array<int, string>
//      */
//     function get_style_set_tag_data(array $params = [])
//     {
//         return app(StyleSetRepository::class)->getTagData(get_parse_query_args($params));
//     }
// }




// if (!function_exists('get_style_set_sortby_options')) {
//     /**
//      * thông tin sap xep danh mục san phẩm
//      *
//      * @return array
//      */
//     function get_style_set_sortby_options()
//     {
//         return [
//             "0" => "Auto",
//             "1" => "Mới nhất",
//             "2" => "Cũ nhất",
//             "3" => "Tên (A-Z)",
//             "4" => "Tên (Z-A)",
//             "5" => "Ngẫu nhiên",

//         ];
//     }
// }




// if (!function_exists('get_personal_style_sets')) {
//     /**
//      * lấy danh sach style set
//      * @param array $params
//      * @return \App\Models\PersonalStyleSet[]|\App\Masks\StyleSets\Personal\StyleSetCollection
//      */
//     function get_personal_style_sets(array $params = [])
//     {
//         if(!($user = auth()->user())) return [];
//         return app(PersonalStyleSetRepository::class)->mode('mask')->getData(array_merge($params, ['user_id' => $user->id]));
//     }
// }



// if (!function_exists('get_personal_style_set')) {
//     /**
//      * lấy danh mục san pham
//      * @param array $params
//      * @return \App\Models\ProductCollection|\App\Masks\Products\CollectionMask
//      */
//     function get_personal_style_set(array $params = [])
//     {
//         if(!($user = auth()->user())) return [];
//         return app(PersonalStyleSetRepository::class)->mode('mask')->detail(array_merge($params, ['user_id' => $user->id]));
//     }
// }


// if (!function_exists('get_personal_style_set_avatar_config')) {
//     /**
//      * lấy dữ liệu config
//      * @return array<string, string>
//      */
//     function get_personal_style_set_avatar_config()
//     {
//         return app(StyleConfigService::class)->getData();
//     }
// }




if (!function_exists('get_product_field_options')) {
    /**
     * lấy danh sách danh mục
     * @return array
     */
    function get_product_field_options($defaultFirst = null, $defaultFirstValue = null)
    {
        /**
         * @var Filemanager
         */
        $filemanager = app(Filemanager::class);
        $data =$filemanager->getJson(json_path('admin/modules/products/form.json'));
        $options = $defaultFirst?($defaultFirstValue ? [$defaultFirstValue=>$defaultFirst]:[$defaultFirst]):[];
        foreach ($data['inputs'] as $name => $config) {
            $options[$name] = $config['label']??$name;
        }
        return $options;

    }
}
