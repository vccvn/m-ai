<?php

namespace App\Models;
use Gomee\Helpers\Arr;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Product class
 *
 * @property integer $category_id Category Id
 * @property integer $subscription_package_id Subscription Package Id
 * @property string $name Name
 * @property string $slug Slug
 * @property string $type Type
 * @property string $description Description
 * @property string $detail Detail
 * @property string $keywords Keywords
 * @property string $featured_image Featured Image
 * @property string $sku Sku
 * @property integer $price_status Price Status
 * @property float $list_price List Price
 * @property float $sale_price Sale Price
 * @property integer $on_sale On Sale
 * @property float $wholesale_price Wholesale Price
 * @property integer $package_total Package Total
 * @property integer $available_in_store Available In Store
 * @property integer $views Views
 * @property string $privacy Privacy
 * @property string $category_map Category Map
 * @property integer $status Status
 * @property integer $trashed_status Trashed Status
 * @property integer $shop_id Shop Id
 * @property integer $created_by Created By
 */
class Product extends Model
{
    public $table = 'products';
    public $fillable = ['category_id', 'subscription_package_id', 'name', 'slug', 'type', 'description', 'detail', 'keywords', 'featured_image', 'sku', 'price_status', 'list_price', 'sale_price', 'on_sale', 'wholesale_price', 'package_total', 'available_in_store', 'views', 'privacy', 'category_map', 'status', 'trashed_status', 'shop_id', 'created_by'];


    const REF_KEY = 'product';

    const NOT_AVAILABLE = 0;
    const AVAILABLE = 1;
    const ALL_STATUS = [
        self::NOT_AVAILABLE, self::AVAILABLE
    ];
    const STATUS_LABELS = [
        self::NOT_AVAILABLE => 'Không khả dụng',
        self::AVAILABLE => 'Còn hàng'
    ];

    protected $totalProductOnWarehouse = -1;

    /**
     * @var array $jsonFields các cột dùng kiểu json
     */
    protected $jsonFields = ['resources', 'specification'];


    protected $attributeDataGroups = [];


    protected $_finalPrice = null;

    protected $variantPrices = [];

    // public $resources = [];
    /**
     * ket noi voi bang user_meta
     * @return queryBuilder
     */
    public function metadatas()
    {
        return $this->hasMany(Metadata::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }
    /**
     * danh mục
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Get all of the categoryRefs for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryRefs(): HasMany
    {
        return $this->hasMany(CategoryRef::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }

    public function categories()
    {
        return $this->categoryRefs()
            ->join('categories', 'categories.id', '=', 'category_refs.category_id')
            ->select('category_refs.category_id', 'category_refs.ref_id', 'categories.*');
    }

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_id', 'id')->select('id', 'name', 'avatar', 'email');
    }

    public function productTags()
    {
        return $this->hasMany(TagRef::class, 'ref_id', 'id')->where('tag_refs.ref', self::REF_KEY);
    }

    public function tags()
    {
        return $this->productTags()->join('tags', 'tag_refs.tag_id', '=', 'tags.id')->select('tags.name', 'tags.keyword', 'tags.slug');
    }

    public function productLabels(): HasMany
    {
        return $this->hasMany(ProductLabelRef::class, 'ref_id')->where('ref', self::REF_KEY);
    }

    public function labels()
    {
        return $this->productLabels()
            ->join('product_labels', 'product_labels.id', '=', 'product_label_refs.label_id')
            ->select('product_labels.*', 'product_label_refs.ref_id', 'product_label_refs.label_id');
    }

    public function fileRefs()
    {
        return $this->hasMany(FileRef::class, 'ref_id', 'id')->where('file_refs.ref', self::REF_KEY);
    }

    public function files()
    {
        return $this->fileRefs()->leftJoin('files', 'files.id', '=', 'file_refs.file_id')->select('file_refs.*', 'files.folder_id', 'files.date_path', 'files.filename', 'files.original_filename', 'files.filetype', 'files.mime', 'files.size', 'files.extension', 'files.title', 'files.description');
    }

    /**
     * The files that belong to the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    // public function files(): BelongsToMany
    // {
    //     return $this->belongsToMany(File::class, 'file_refs', 'ref_id', 'file_id')->wherePivot('ref', '=', self::REF_KEY)
    //         ->select('files.folder_id', 'files.date_path', 'files.filename', 'files.original_filename', 'files.filetype', 'files.mime', 'files.size', 'files.extension', 'files.title', 'files.description');
    // }



    public function gallery()
    {
        return $this->files();
        // return $this->hasMany('App\Models\File', 'ref_id', 'id')->where('ref', $this->ref);
    }

    public function refs()
    {
        return $this->hasMany(ProductRef::class, 'product_id', 'id');
    }

    /**
     * Get the productCustomerCentricData associated with the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function productCustomerCentricData(): HasOne
    {
        return $this->hasOne(ProductCustomerCentricData::class, 'product_id', 'id');
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class, 'product_id', 'id');
    }


    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->productReviews()
            ->leftJoin('customers', 'customers.id', '=', 'product_reviews.customer_id')
            ->leftJoin('users', 'users.id', '=', 'customers.user_id')
            ->select(
                'product_reviews.*',
                'customers.name as customer_name',
                'customers.email as customer_email',
                'users.avatar',
                'users.name as user_name',
                'users.email as user_email'
            );
    }

    public function oneStarReviews()
    {
        return $this->reviews()
            ->where('product_reviews.rating',1);
    }
    public function twoStarsReviews()
    {
        return $this->reviews()
            ->where('product_reviews.rating',2);
    }
    public function threeStarsReviews()
    {
        return $this->reviews()
            ->where('product_reviews.rating',3);
    }
    public function fourStarsReviews()
    {
        return $this->reviews()
            ->where('product_reviews.rating',4);
    }
    public function fiveStarsReviews()
    {
        return $this->reviews()
            ->where('product_reviews.rating',5);
    }



    public function comments()
    {
        return $this->hasMany(Comment::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }

    public function publishComments()
    {
        return $this->comments()->where('privacy', 'public')->where('parent_id', 0)->where('approved', 1);
    }

    /**
     * The promoable that belong to the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promoable(): BelongsToMany
    {
        return $this->belongsToMany(Promo::class, 'product_refs', 'product_id', 'ref_id')->wherePivot('ref', 'promo')->where('promos.scope', 'product');
    }

    public function promoRefs()
    {
        return $this->hasMany(ProductRef::class, 'product_id', 'id')->join('promos', 'promos.id', '=', 'product_refs.ref_id')
            ->where('product_refs.ref', 'promo')
            ->where('promos.scope', 'product');
    }

    public function promos()
    {
        return $this->refs()
            ->join('promos', 'promos.id', '=', 'product_refs.ref_id')
            ->where('product_refs.ref', 'promo')
            ->where('promos.scope', 'product')
            ->select(
                'product_refs.*',
                'promos.name',
                'promos.description',
                'promos.scope',
                'promos.type',
                'promos.down_price',
                'promos.code',
                'promos.quantity_per_user',
                'promos.limited_total',
                'promos.usage_total',
                'promos.started_at',
                'promos.finished_at',
                'promos.is_activated'
            );
    }


    /**
     * Các chuong trinh khuyen mai con hieu luc
     * @return QueryBuilder
     */
    public function promoAvailable()
    {
        return $this->promos()->where('promos.started_at', '<=', CURRENT_TIME_STRING)->where('promos.finished_at', '>=', CURRENT_TIME_STRING);
    }




    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }





    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }




    public function attrs()
    {
        return $this->productAttributes()
            ->join('attribute_values', 'attribute_values.id', 'product_attributes.attribute_value_id')
            ->join('attributes', 'attributes.id', '=', 'attribute_values.attribute_id')
            ->orderBy('attributes.is_order_option', 'DESC')
            ->orderBy('attributes.is_variant', 'DESC')
            ->orderBy('attributes.price_type', 'DESC')
            ->orderBy('attributes.id', 'ASC')
            ->orderBy('attribute_values.id', 'ASC')
            ->select(
                'product_attributes.id as product_attribute_id',
                'attribute_values.attribute_id',
                'attributes.name',
                'attributes.label',
                'attributes.input_type',
                'attributes.value_type',
                'attributes.show_type',
                'attributes.price_type',
                'attributes.value_unit',
                'attributes.is_variant',
                'attributes.advance_value_type',
                'attributes.use_list',
                'attributes.use_thumbnail',
                'product_attributes.attribute_value_id',
                'attributes.is_order_option',
                'attribute_values.varchar_value',
                'attribute_values.int_value',
                'attribute_values.decimal_value',
                'attribute_values.text_value',
                'attribute_values.advance_value as attribute_advance_value',
                'product_attributes.advance_value as variant_advance_value',
                'product_attributes.price',
                'product_attributes.is_default',
                'product_attributes.thumbnail',
                'product_attributes.product_id'
            );
        // lùi 1 tab
        // lùi 1 tab
        // lùi 1 tab
    }

    public function orderOptions()
    {
        return $this->attrs()->where('attributes.is_order_option', 1);
    }

    public function notOrderOptions()
    {
        return $this->attrs()->where('attributes.is_order_option', 0);
    }

    public function variantAttributes()
    {
        return $this->orderOptions()->where('attributes.is_variant', 1);
    }

    public function variants()
    {
        return $this->variantAttributes();
    }


    public function notVariantAttributes()
    {
        return $this->orderOptions()->where('attributes.is_variant', 0);
    }

    /**
     * lấy ra các thuộc tính biến thể
     *
     * @return array
     */
    public function getVariantAttributes()
    {
        return $this->parseAttributeData($this->variants, 1, 1);
    }

    /**
     * lấy ra các thuộc tính trong đơn hàng nhưng không phải biến thể
     *
     * @return array
     */
    public function getNotVariantAttributes()
    {
        return $this->parseAttributeData($this->notVariantAttributes, 1, 0);
    }


    /**
     * lấy ra các thuộc tính trong đơn hàng nhưng không phải biến thể
     *
     * @return array
     */
    public function getOrderAttributes()
    {
        return $this->parseAttributeData($this->notVariantAttributes, 1, 0);
    }

    /**
     * lấy ra các thuộc tính không nằm trong đơn hàng
     * @return array
     */
    public function getProductAttributes()
    {
        return $this->parseAttributeData($this->notOrderOptions);
    }


    /**
     * chuần hóa data trả về theo đúng logic
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param integer $is_order_option
     * @param integer $is_variant
     * @return array<int, array<string|int, array>>
     */
    public function getOrderOptions()
    {
        if($oo = $this->getRelation('orderOptions')){
            return $this->parsOrderOptionData($oo);
        }
        $data = $this->parsOrderOptionData($this->notVariantAttributes);
        if($d = $this->parsOrderOptionData($this->variantAttributes)){
            foreach ($d as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;

    }


    /**
     * chuần hóa data trả về theo đúng logic
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param integer $is_order_option
     * @param integer $is_variant
     * @return array<int, array<string|int, array>>
     */
    protected function parsOrderOptionData($attributes, $attr_values = [])
    {
        $data = [];
        if (count($attributes)) {
            foreach ($attributes as $attr) {
                $avt = $attr->advance_value_type;
                if (!isset($data[$attr->attribute_id])) {
                    $data[$attr->attribute_id] = [
                        'attribute_id' => $attr->attribute_id,
                        'name' => $attr->name,
                        'label' => $attr->label,
                        'values' => [],
                        'value_ids' => []

                    ];
                }

                if(!$attr_values || !count($attr_values) || in_array($attr->attribute_value_id, $attr_values)){
                    $values = [
                        'id' => $attr->product_attribute_id,
                        'value_id' => $attr->attribute_value_id,
                        'value' => $attr->{$attr->value_type . '_value'},
                        'text' => $attr->text_value ? $attr->text_value : ($attr->{$attr->value_type . '_value'} . (
                            ($attr->value_type == 'int' || $attr->value_type == 'decimal') ? ' ' . $attr->value_unit : ''
                        )
                        ),
                        'advance_type' => $attr->advance_value_type,
                        'advance_value' => ($av = $attr->variant_advance_value ? $attr->variant_advance_value : $attr->attribute_advance_value) ? ($attr->advance_value_type == 'image' ? asset(content_path('products/' . ($av ? (($attr->variant_advance_value ? 'variants/' : 'attributes/') . $av) : 'variants/default.png'))) : $av
                        ) : null
                    ];

                    $values['is_default'] = $attr->is_default;
                    if ($attr->is_default) {
                        $data[$attr->attribute_id]['default'] = $attr->attribute_value_id;
                    }
                    $values['price'] = $attr->price;

                    // if ($is_variant) {

                    // }

                    $data[$attr->attribute_id]['values'][$attr->attribute_value_id] = $values;
                    $a = $data[$attr->attribute_id]['value_ids'];
                    $a[] = $attr->attribute_value_id;
                    sort($a);

                    $data[$attr->attribute_id]['value_ids'] = $a;
                }

            }
        }
        return $data;
    }


    /**
     * chuần hóa data trả về theo đúng logic
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param integer $is_order_option
     * @param integer $is_variant
     * @return array<int, Arr>
     */
    protected function parseAttributeData($attributes, $is_order_option = 0, $is_variant = 0, $attr_values = [])
    {
        $data = [];
        if (count($attributes)) {
            $group = $is_variant ? 'variant' : 'attribute';
            $pg = 'product-' . $group;
            $prefix = get_prefix_classname();
            foreach ($attributes as $attr) {
                $avt = $attr->advance_value_type;
                if (!isset($data[$attr->attribute_id])) {
                    $d = [
                        'attribute_id' => $attr->attribute_id,
                        'name' => $attr->name,
                        'label' => $attr->label,
                        'type' => $attr->show_type,
                        'use_thumbnail' => $attr->use_thumbnail,
                        'group_class' => parse_classname($pg . '-group', $pg . '-group-' . $attr->name, $pg . '-type-' . $avt),
                        'list_class' => parse_classname($pg . '-type-' . $avt . '-list', $pg . '-' . $attr->name . '-list', 'product-attribute-' . $avt . '-values'),
                        'select_class' => parse_classname($pg . '-select', $pg . '-select-' . $avt, $pg . '-type-' . $avt . '-select', $pg . '-' . $attr->name . '-select'),
                        'prefix_class' => $prefix,
                        'price_type' => $attr->price_type
                    ];

                    if ($is_order_option) {
                        $f = [
                            'advance_value_type' => $attr->advance_value_type,
                            'value_unit' => $attr->value_unit,
                            'values' => []
                        ];
                        if ($is_variant) {
                            $f['change_type'] = $attr->price_type ? 'replace' : 'plus';
                        }
                        $d += $f;
                    }
                    $data[$attr->attribute_id] = $d;
                }

                if(!$attr_values || !count($attr_values) || in_array($attr->attribute_value_id, $attr_values)){
                    $values = [
                        'id' => $attr->product_attribute_id,
                        'value_id' => $attr->attribute_value_id,
                        'value' => $attr->{$attr->value_type . '_value'},
                        'text' => $attr->text_value ? $attr->text_value : ($attr->{$attr->value_type . '_value'} . (
                            ($attr->value_type == 'int' || $attr->value_type == 'decimal') ? ' ' . $attr->value_unit : ''
                        )
                        ),
                        'advance_type' => $attr->advance_value_type,
                        'advance_value' => ($av = $attr->variant_advance_value ? $attr->variant_advance_value : $attr->attribute_advance_value) ? ($attr->advance_value_type == 'image' ? asset(content_path('products/' . ($av ? (($attr->variant_advance_value ? 'variants/' : 'attributes/') . $av) : 'variants/default.png'))) : $av
                        ) : null,
                        'use_thumbnail' => $attr->use_thumbnail,
                        'thumbnail' => $attr->use_thumbnail ? asset(content_path('products/attributes/thumbnails/' . $attr->thumbnail)) : '',
                        'item_class' => parse_classname($pg . '-value', $pg . '-value-item', $pg . '-value-id-' . $attr->product_attribute_id, $pg . '-value-option')
                    ];

                    $values['is_default'] = $attr->is_default;
                    if ($attr->is_default) {
                        $data[$attr->attribute_id]['default'] = $attr->attribute_value_id;
                    }

                    if ($is_variant) {
                        $values['price'] = $attr->price;
                    }

                    $data[$attr->attribute_id]['values'][$attr->attribute_value_id] = new Arr($values);
                }

            }
        }
        if ($data) {
            return array_map(function ($value) {
                return new Arr($value);
            }, $data);
        }
        return [];
    }

    /**
     * chuần hóa data trả về theo đúng logic
     * @return array<int, Arr>
     */
    public function getOrderOptionData($attr_values = [])
    {
        return $this->parseAttributeData($this->orderOptions, 1, 0, $attr_values);
    }

    public function priceFormat($price_type = 'list')
    {
        $price = $price_type == 'final' ? $this->getFinalPrice() : $this->{$price_type . '_price'};
        if ($this->price_status < 0 || $this->list_price < 0) {
            if ($price_type == 'final') {
                return 'Liên Hệ';
            } else {
                if ($this->hasPromo() && $this->price_status >= 0) {
                    return '';
                }
                return 'Giá liên hệ';
            }
        }

        return get_currency_format($price);
    }

    /**
     * tính giá cuối cùng (sau các loại khuyến mãi)
     * @return float
     */
    public function getFinalPrice($externalPricxe = null)
    {
        $price = ($externalPricxe !== null && is_numeric($externalPricxe)) ? $externalPricxe : ($this->on_sale ? $this->sale_price : $this->list_price);
        if ($price < 0) return $price;
        if (count($this->promoAvailable)) {
            foreach ($this->promoAvailable as $promo) {
                $down_price = $promo->down_price;
                if ($promo->type == Promo::TYPE_SAME_PRICE) {
                    $price = $down_price;
                } elseif ($promo->type == Promo::TYPE_DOWN_PERCENT) {
                    $down = $down_price * $price / 100;
                    $price -= $down;
                } elseif ($promo->type == Promo::TYPE_DOWN_PRICE) {
                    $price -= $down_price;
                }
            }
        }

        return $price < 0 ? 0 : $price;
    }

    /**
     * tính giá cuối cùng (sau các loại khuyến mãi)
     * @return double
     */
    public function getDownPercent()
    {
        $price = $this->final_price;
        // $lp = to_number($this->list_price);
        $list_price = $this->list_price > 0 ? $this->list_price : 1;
        if ($list_price - $price <= 0) return 100;
        return round(($list_price - $price) / $list_price * 10000) / 100;
    }

    public function getFinalPriceAttribute()
    {
        if ($this->_finalPrice === null) {
            $this->_finalPrice = $this->getFinalPrice();
        }
        return $this->_finalPrice;
    }

    /**
     * lấy giá của sản phẩm theo thuộc tính
     * @param int $product_id
     * @param array $attributes
     * @return array
     */
    public function checkPrice(array $attr_values = [], $quantity = 1)
    {
        /**
         * @var Product
         */
        $product = $this;
        if (!is_numeric($quantity) || $quantity < 1) $quantity = 1;
        $attr_values = array_values($attr_values);
        sort($attr_values);
        $attr_key = implode('-', $attr_values);
        if(!$attr_key) $attr_key = "no-attr";
        if ($quantity > $product->available_in_store) {
            $quantity = $product->available_in_store;
        }
        if(array_key_exists($attr_key, $this->variantPrices)){
            return ($this->variantPrices[$attr_key] > -1?$this->variantPrices[$attr_key]:-1/$quantity) * $quantity;
        }
        if ($product->price_status < 0) {
            $price = -1;
            $this->variantPrices[$attr_key] = $price;
            return $price;
        }
        $change = false;
        $price = $product->on_sale ? $product->sale_price : $product->list_price;
        $attrs = $this->getOrderOptionData($attr_values);

        if (count($attrs)) {
            foreach ($attrs as $key => $attr) {
                $values = $attr->values;
                $hasDefault = false;
                if(!$attr_values){
                    foreach ($values as $value_id => $av) {
                        if($av->is_default){
                            $hasDefault = true;
                            if ($attr->price_type) {
                                if (!$change) {
                                    $price = $av->price;
                                    $change = 1;
                                }
                            } else {
                                $price += $av->price;
                            }
                        }
                    }
                    if(!$hasDefault){
                        $k = array_key_first($values);
                        $av = $values[$k];
                        if ($attr->price_type) {
                            if (!$change) {
                                $price = $av->price;
                                $change = 1;
                            }
                        } else {
                            $price += $av->price;
                        }
                    }

                }
                else{
                    $k = array_key_first($values);
                    $av = $values[$k];
                    if ($attr->price_type) {
                        if (!$change) {
                            $price = $av->price;
                            $change = 1;
                        }
                    } else {
                        $price += $av->price;
                    }
                }
            }
        }

        $price = $product->getFinalPrice($price);
        $this->variantPrices[$attr_key] = $price;
        return $price * $quantity;


    }


    /**
     * lấy điểm dánh giá trung bình
     *
     * @return int
     */
    public function getReviewPoints()
    {
        $t = count($this->reviews);
        if (!$t) return 0;
        $total = 0;
        $perfect = 5;
        foreach ($this->reviews as $review) {
            $total += $review->rating;
        }
        $avg = $total / ($t * $perfect) * $perfect;

        return round($avg, 1);
    }


    /**
     * lấy điểm dánh giá trung bình
     *
     * @return int
     */
    public function getReviewData()
    {
        $t = count($this->reviews);
        if (!$t) return 0;
        $total = 0;
        $perfect = 5;
        foreach ($this->reviews as $review) {
            $total += $review->rating;
        }
        $avg = $total / ($t * $perfect) * $perfect;

        return round($avg, 1);
    }
    public function countReview()
    {
        return count($this->reviews);
    }

    /**
     * có khuyến mãi
     * @return bool
     */
    public function hasPromo()
    {
        return (count($this->promoAvailable) > 0 || $this->on_sale);
    }

    public function countTotal()
    {
        return $this->warehouse->sum('total');
    }

    public function hasVariant()
    {
        return (($this->variants_count !== null) ? $this->variants_count : count($this->variants)) > 0;
    }

    public function hasOption()
    {
        return count($this->orderOptions) > 0;
    }

    public function canReview()
    {
        return true;
        return can_review_product($this->id);
    }
    /**
     * ẩn sản phẩm
     */
    public function hidden()
    {
        $this->trashed_status++;
        $this->save();
    }

    /**
     * hiện sản phẩm
     */
    public function visible()
    {
        $this->trashed_status--;
        $this->save();
    }


    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $this->applyMeta();
        if ($this->features) $this->features = implode(', ', explode(',', $this->features));
        $data = $this->toArray();

        if ($this->featured_image) {
            $data['featured_image'] = $this->getFeaturedImage();
        }
        $tags = [];
        if (count($this->productTags)) {
            foreach ($this->productTags as $tagged) {
                $tags[] = $tagged->tag_id;
            }
        }
        $data['tags'] = $tags;
        $data['total'] = $this->countTotal() ?? '';
        $data['labels'] = [];
        if ($this->productLabels && count($this->productLabels)) {
            foreach ($this->productLabels as $key => $value) {
                $data['labels'][] = $value->label_id;
            }
        }

        return $data;
    }


    /**
     * lấy tên thư mục chứa ảnh thumbnail / feature image
     * @return string tên tư mục
     */
    public function getImageFolder(): string
    {
        return 'products';
    }


    /**
     * get image url
     * @param string $size
     * @return string
     */
    public function getFeaturedImage($size = false)
    {
        $fd = $this->getSecretPath() . '/' . $this->getImageFolder();
        if ($this->featured_image) {
            $featured_image = $this->featured_image;
            if ($size) {
                if (file_exists($f = $fd . '/' . $size . '/' . $featured_image)) return asset($f);
            }
            if (file_exists($f = $fd . '/' . $featured_image)) return asset($f);
        }
        return asset('static/images/product.png');
    }

    public function getThumbnail()
    {
        if ($this->featured_image) {
            $featured_image = $this->featured_image;
        } else {
            $featured_image = 'default.png';
        }
        $fd = $this->getSecretPath() . '/' . $this->getImageFolder();
        if (file_exists(public_path($fd . '/thumbs/' . $featured_image))) {
            return asset($fd . '/thumbs/' . $featured_image);
        }
        return $this->getImage();
    }

    // lấy hình ảnh theo kích thước

    public function getImage($size = null)
    {
        if (!$size) {
            return $this->getFeaturedImage();
        } elseif (in_array($size, ['thumb',  'thumbnail'])) {
            return $this->getThumbnail();
        } elseif (in_array($size, ['social',  '90x90'])) {
            return $this->getFeaturedImage($size);
        } else {
            if ($this->featured_image) {
                $featured_image = $this->featured_image;
            } else {
                $featured_image = 'default.png';
            }

            $fd = $this->getSecretPath() . '/' . $this->getImageFolder();
            if (file_exists($p = $fd . '/' . $size . '/' . $featured_image)) {
                return asset($p);
            }
            return asset('static/images/default.png');
        }
    }


    public function getViewUrl()
    {
        return get_product_url($this);
    }


    public function getFullTitle()
    {
        $title = '';
        if ($this->category_id) {
            if ($category = get_model_data('product_category', $this->category_id)) {
                $tree = $category->getTree();

                foreach ($tree as $cate) {
                    $title = $cate->name . ' | ' . $title;
                }
            }
        }
        $title = $this->name . ' | ' . $title;
        $title .= ' | Sản phẩm';
        return $title;
    }

    public function getTypeLabel()
    {
        if ($this->type == 'digital') return 'Sản phẩm số';
        return 'Sản phẩm vật lý';
    }

    /**
     * lay61 key words
     *
     * @return void
     */
    public function getSeoKeywords()
    {
        $data = [];
        if ($this->keywords) $data[] = $this->keywords;
        if (count($tags = $this->tags)) {
            foreach ($tags as $key => $tag) {
                $data[] = $tag->keyword;
            }
        }
        return implode(', ', $data);
    }


    public function canMoveToTrash()
    {
        if ($this->orderItems && count($this->orderItems)) {
            return 'Bạn không thể xóa sản phẩm đã được thêm trong đơn hàng!';
        }
        return true;
    }


    /**
     * xoa image
     */
    public function deleteFeatureImage()
    {
        $sp = $this->getSecretPath() . '/' . $this->getImageFolder();
        if ($this->featured_image && file_exists($path = public_path($sp . '/' . $this->featured_image))) {
            unlink($path);
            if (file_exists($p = public_path($sp . '/90x90/' . $this->featured_image)) && is_file($p)) {
                unlink($p);
            }
            if (file_exists($p = public_path($sp . '/thumbs/' . $this->featured_image)) && is_file($p)) {
                unlink($p);
            }
            if (file_exists($p = public_path($sp . '/social/' . $this->featured_image)) && is_file($p)) {
                unlink($p);
            }
        }
    }
    /**
     * ham xóa file cũ
     * @param int $id
     *
     * @return boolean
     */
    public function deleteAttachFile()
    {
        return $this->deleteFeatureImage();
    }

    /**
     * lấy tên file đính kèm cũ
     */
    public function getAttachFilename()
    {
        return $this->featured_image;
    }

    /**
     * xóa tài nguyên
     */
    public function deleteResources()
    {
        $this->applyMeta();
        if ($this->resources && is_array($this->resources)) {
            $sp = $this->getSecretPath() . '/' . $this->getImageFolder();
            foreach ($this->resources as $file) {
                if (file_exists($p = public_path($sp . '/' . $file)) && is_file($p)) {
                    unlink($p);
                }
                if (file_exists($p = public_path($sp . '/thumbs/' . $file)) && is_file($p)) {
                    unlink($p);
                }
            }
        }
    }

    /**
     * xóa dữ liệu
     */
    public function beforeDelete()
    {
        // delete meta
        if (count($this->metadatas)) {
            foreach ($this->metadatas as $metadata) {
                $metadata->delete();
            }
        }
        // deletegallery
        if (count($this->fileRefs)) {
            foreach ($this->fileRefs as $gallery) {
                $gallery->delete();
            }
        }
        $this->productLabels()->delete();

        if (count($this->productAttributes)) {
            foreach ($this->productAttributes as $productAttribute) {
                $productAttribute->delete();
            }
        }
        // xóa tài nguyên
        $this->deleteResources();

        // delete image
        $this->deleteFeatureImage();

        // xóa liên kết sản phẩm
        $this->refs()->delete();

        // xóa kho hàng
        $this->warehouse()->delete();
        $this->productReviews()->delete();

        // deletegallery
        if (count($this->categoryRefs)) {
            foreach ($this->categoryRefs as $gallery) {
                $gallery->delete();
            }
        }
    }
}
