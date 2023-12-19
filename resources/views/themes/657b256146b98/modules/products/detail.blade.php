@extends($_layout.'master')
@section('title', $page_title)
@include($_lib.'register-meta')
@section('page.header.show', 'breadcrumb')
@section('breadcrumb.disable_last_url', '1')

@php
$hasPromo = $product->hasPromo();
// $reviews = $product->getReviewData();
$hasOption = $product->hasOption();
$u = $product->getViewUrl();
$user = $request->user();
add_product_schema($product);

$reviewAnalytics = $product->getReviewData();

@endphp

@section('content')

<!-- Shop Section start -->
<section class="product-detail-section section-default pt-12 pt-lg-30 pt-xl-30 pt-xxl-40 section-small">
    <div class="container-max">
        <div class="row mt-0">
            <div class="col-lg-12 col-12">
                <div class="details-items {{parse_classname('product-detail')}}">
                    @php
                        $thumbnails = $product->getThumbnailOrderOption();
                        $thumbnailImages = [];
                        if ($thumbnails){
                            foreach ($thumbnails as $thumbAttr){
                                if (is_array($attrValues = $thumbAttr->values) && count($attrValues)){
                                    foreach ($attrValues as $attrVal){
                                        if ($attrVal->thumbnail){
                                            $thumbnailImages[] = $attrVal;
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    <div class="row mt-0">
                        <div class="col-md-6">
                            <div class="product-thumbnails">
                                <div class="thumbnail-left">
                                    <div class="details-image-vertical black-slide rounded">
                                        <div>
                                            <img src="{{$product->getImage()}}" class="img-fluid blur-up lazyload" alt="{{$product->name}}">
                                        </div>
                                        @if ($thumbnailImages)
                                            @foreach ($thumbnailImages as $thumb)
                                                <div>
                                                    <img src="{{$thumb->thumbnail}}" id="pav-thumbnail-{{$thumb->value_id}}" class="img-fluid blur-up lazyload" alt="{{$thumb->text}}">
                                                </div>
                                            @endforeach
                                        @endif
                                        @if ($product->gallery && count($product->gallery))
                                            @foreach ($product->gallery as $item)
                                                <div>
                                                    <img src="{{$item->url}}" class="img-fluid blur-up lazyload" alt="{{$product->name}}">
                                                </div>

                                            @endforeach
                                        @endif

                                    </div>
                                </div>

                                <div class="details-image-1 ratio_asos">
                                    <div>
                                        <img src="{{$product->getImage()}}" id="zoom_01" data-zoom-image="{{$product->getImage()}}" class="img-fluid w-100 image_zoom_cls-0 blur-up lazyload" alt="{{$product->name}}">
                                    </div>
                                    @if ($thumbnailImages)
                                        @foreach ($thumbnailImages as $thumb)
                                            <div>
                                                <img src="{{$thumb->thumbnail}}" id="zoom_v_{{$thumb->value_id}}" data-zoom-image="{{$thumb->thumbnail}}" class="img-fluid w-100 image_zoom_cls-1 blur-up lazyload" alt="{{$thumb->text}}">
                                            </div>
                                        @endforeach
                                    @endif

                                    @if ($product->gallery && count($product->gallery))
                                        @foreach ($product->gallery as $item)
                                            <div>
                                                <img src="{{$item->url}}" id="zoom_g_{{$item->id}}" data-zoom-image="{{$item->url}}" class="img-fluid w-100 image_zoom_cls-1 blur-up lazyload" alt="{{$product->name}}">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="cloth-details-size product-info {{parse_classname('product-detail-info', 'product-detail-info-'.$product->id)}}" id="product-detail-{{$product->id}}" data-id="{{$product->id}}">
                                <div class="sku">
                                    {{$product->sku}}
                                </div>

                                <div class="details-image-concept product-name">
                                    <h1>{{$product->name}}</h1>
                                </div>

                                <div class="product-rating-overview">
                                    <ul class="rating d-inline-block">
                                        @if ($reviewAnalytics->total)
                                            @for ($i = 1; $i < 6; $i++)
                                                <li>
                                                    <i class="fas fa-star {{$i <= $reviewAnalytics->rating_int?'theme-color':''}}"></i>
                                                </li>
                                            @endfor
                                        @endif
                                        <li><span>{{$reviewAnalytics->total}} Đánh giá</span></li>
                                    </ul>
                                </div>
                                <div class="product-price-box">

                                    @if ($hasPromo && $product->price_status > -1)
                                    <span class="old-price">
                                        {{$product->priceFormat('list')}}
                                    </span>
                                    <span class="onsale-label">-{{$product->getDownPercent()}}%</span>
                                    @endif

                                    <div class="regular-price  {{parse_classname('product-price')}}">{{$product->priceFormat('final')}}</div>
                                </div>

                                <div class="border-product">
                                    @if ($product->features)
                                        <div class="seo-features">
                                            <h6 class="product-title product-title-2 d-block">Đặc điểm nổi bật</h6>
                                            <ul class="">
                                                @foreach ($product->features as $text)
                                                    <li class="feature-item">
                                                        <a href="javascript:;">{{$text}}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    @endif
                                    @if ($ecommerce->allow_place_order && $product->price_status > 0 && $product->status > 0 && $product->available_in_store)
                                        <form action="{{ route('web.orders.add-to-cart') }}" method="post" class="{{ $product->price_status < 0 ? '' : parse_classname('product-order-form') }}"data-check-required="{{($ecommerce->allow_place_order && $product->price_status > 0 && $product->status > 0 && $product->available_in_store)?'true': 'false'}}">

                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}" class="{{ parse_classname('product-order-id') }}">
                                            <input type="hidden" name="redirect" value="checkout">


                                                {!! $product->attributesToHtml([
                                                    'section_class' => '',
                                                    'attribute_class' => '',
                                                    'attribute_name_class' => '',
                                                    'value_list_class' => '',
                                                    'value_item_class' => '',
                                                    'select_class' => '',
                                                    'image_class' => '',
                                                    'value_text_class' => '',
                                                    'radio_class' => '',
                                                    'value_label_class' => '',
                                                ]) !!}





                                            <div class="addeffect-section product-description">


                                                <h6 class="quantity-label d-block">Số lượng</h6>

                                                <div class="qty-box">
                                                    <div class="input-group">
                                                        <span class="input-group-prepend">
                                                            <button type="button" class="btn quantity-left-minus" data-type="minus" data-field="">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </span>
                                                        <input type="text" name="quantity" class="form-control input-number {{ $product->price_status < 0 ? '' : parse_classname('product-order-quantity', 'quantity') }}" value="1" min="1" step="1">
                                                        <span class="input-group-prepend">
                                                            <button type="button" class="btn quantity-right-plus" data-type="plus" data-field="">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="product-buttons">
                                                <button type="submit" class="btn btn-outline-default btn-add-to-cart ">
                                                    Thêm giỏ hàng
                                                </button>
                                                <a href="javascript:void(0)"  class="btn btn-colored-default btn-buy-now crazy-btn-buy-now">
                                                    Mua Ngay
                                                </a>
                                            </div>
                                        </form>

                                    @elseif(!$product->available_in_store)
                                        <div class="alert alert-danger">
                                            Sản phẩm tạm hết hàng
                                        </div>
                                    @endif

                                </div>

                                <div class="product-extra-info">
                                    {!! $html->product_detail_extra->components !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="container-max pt-50 pt-lg-40 pl-lg-40 pl-xl-60 pr-lg-40 pr-xl-60 ">
        {!! $html->product_detail_after->components !!}
    </div>
    <div class="container-max pt-50 pt-lg-40">

        <!-- detail content -->
        <div class="detail-contents">
            <div class="row ">
                <div class="col-12 col-lg-8">
                    <h3 class="detail-title">Chi tiết sản phẩm</h3>
                    <div class="detail-box">
                        <div class="content-box article-content">
                            {!! $product->detail !!}
                        </div>
                        <div class="see-more-content">
                            <a href="javascript:;" class="btn-see-more">Xem thêm</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    {!! $html->sidebar_product->components !!}
                </div>
            </div>
        </div>

    </div>
    <div class="container-max">
        <div class="">
            <div class="cloth-review">

                @if ($reviewAnalytics)
                    @php
                        $ratingData = $reviewAnalytics->ratings;
                        $productSettings = $options->theme->products;
                        // $reviews = $product->getReviews(5, 'DESC');

                        // if($reviews){
                        //     $reviews->path(route('web.products.reviews', ['slug' => $product->slug]));
                        // }
                    @endphp
                    <div class="row g-4 review">
                        <div class="col-md-6 col-lg-5">
                            @if ($reviewAnalytics->total)

                                <div class="customer-rating">
                                    <div class="row g-4">
                                        <div class="col-6"><span class="count-review">{{$reviewAnalytics->total}} đánh giá</span>
                                        </div>
                                        <div class="col-6"><span class="count-review">{{$reviewAnalytics->rating_avg}}/5</span><i
                                                    class="fas fa-star theme-color count-review"></i></div>
                                    </div>
                                    <div class="row g-4 total-review">
                                        <div class="col-4">
                                            <span class="total-review-rating">5</span>
                                            <i class="fas fa-star theme-color total-review-rating"></i>
                                            <span class="total-review-rating">({{$product->rating_5_count}})</span>
                                        </div>
                                        <div class="col-4"><span class="total-review-rating">4</span><i
                                                    class="fas fa-star theme-color total-review-rating"></i><span
                                                    class="total-review-rating">({{$product->rating_4_count}})</span>
                                        </div>
                                        <div class="col-4"><span class="total-review-rating">3</span><i
                                                    class="fas fa-star theme-color total-review-rating"></i><span
                                                    class="total-review-rating">({{$product->rating_3_count}})</span>
                                        </div>
                                    </div>
                                    <div class="row g-4 total-review">
                                        <div class="col-4"><span class="total-review-rating">2</span><i
                                                    class="fas fa-star theme-color total-review-rating"></i><span
                                                    class="total-review-rating">({{$product->rating_2_count}})</span>
                                        </div>
                                        <div class="col-4"><span class="total-review-rating">1</span><i
                                                    class="fas fa-star theme-color total-review-rating"></i><span
                                                    class="total-review-rating">({{$product->rating_1_count}})</span>
                                        </div>
                                    </div>
                                </div>

                            @endif
                        </div>
                        <div class="col-md-6 col-lg-7">

                            <div class="customer-review-box review-ajax-render-container">

                            </div>

                            <div class="review-cta">
                                <div class="inner">
                                    <div class="icon">
                                        <img src="{{theme_asset('images/review-icon.svg')}}" alt="Viết đánh giá">
                                    </div>
                                    <div class="texts">
                                        <h4>{{$productSettings->review_cta_title('Đánh giá sản phẩm ngay')}}</h4>
                                        <p class="d-none d-md-block">{{$productSettings->review_cta_description('Lorem Ipsum is simply dummy text of the printing and typesetting industry.')}}</p>
                                        <div class="buttons d-none d-md-block">
                                            <a class="ws-btn colored-default {{parse_classname('show-review-form')}}" href="javascript:void(0);">
                                                <span>{{$productSettings->review_cta_button('Viết đánh giá')}}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <p class="d-md-none">{{$productSettings->review_cta_description('Lorem Ipsum is simply dummy text of the printing and typesetting industry.')}}</p>
                                <div class="buttons d-md-none text-xemter">
                                    <a class="ws-btn colored-default {{parse_classname('show-review-form')}}" href="javascript:void(0);">
                                        <span>{{$productSettings->review_cta_button('Viết đánh giá')}}</span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- Shop Section end -->
{!! $html->product_detail_append->components !!}
<!-- product section end -->

@php
    add_js_src(theme_asset('js/sticky-cart-bottom.js'));
@endphp

<!-- sticky cart bottom start -->
<div class="sticky-bottom-cart section-large {{parse_classname('product-detail')}}">
    <div class="container container-max {{parse_classname('product-detail-info', 'product-detail-info-'.$product->id)}}">
        <form action="{{ route('web.orders.add-to-cart') }}" method="post" class="{{ $product->price_status < 0 ? '' : parse_classname('product-order-form') }}" data-check-required="{{($ecommerce->allow_place_order && $product->price_status > 0 && $product->status > 0 && $product->available_in_store)?'true': 'false'}}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}" class="{{ parse_classname('product-order-id') }}">
            <input type="hidden" name="redirect" value="checkout">
            <div class="cart-content">
                <div class="product-image">
                    <img src="{{$product->getThumbnail()}}" class="img-fluid blur-up lazyload" alt="{{$product->name}}">
                    <div class="content">
                        <h2>{{$product->name}}</h2>

                        <div class="product-rating-overview">
                            <ul class="rating d-inline-block">
                                @if ($reviewAnalytics->total)
                                    @for ($i = 1; $i < 6; $i++)
                                        <li>
                                            <i class="fas fa-star {{$i <= $reviewAnalytics->rating_int?'theme-color':''}}"></i>
                                        </li>
                                    @endfor
                                @endif

                                <li class="df-ds-none"><span>120 Đánh giá</span></li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="selection-section product-price">
                    <div class="product-price-box">

                        @if ($hasPromo && $product->price_status > -1)
                        <div class="d-flex">
                            <span class="old-price">
                                {{$product->priceFormat('list')}}
                            </span>
                            <span class="onsale-label">-{{$product->getDownPercent()}}%</span>
                        </div>
                        @endif

                        <div class="regular-price  {{parse_classname('product-price')}}">{{$product->priceFormat('final')}}</div>
                    </div>
                </div>
                @if ($ecommerce->allow_place_order && $product->price_status > 0 && $product->status > 0 && $product->available_in_store)

                    {!! $product->attributesToHtml([
                        'section_class' => 'selection-section',
                        'attribute_class' => 'form-group mb-0 product-attr-group',
                        'attribute_name_class' => '',
                        'value_list_class' => '',
                        'value_item_class' => '',
                        'select_class' => '',
                        'image_class' => '',
                        'value_text_class' => '',
                        'radio_class' => '',
                        'value_label_class' => '',
                        'input_id_prefix' => 'sticky-select-attr'
                    ]) !!}

                    <div class="selection-section qty-section">
                        <div class="addeffect-section">


                            <div class="qty-box">
                                <div class="input-group" style="flex-wrap: nowrap">
                                    <span class="input-group-prepend">
                                        <button type="button" class="btn quantity-left-minus" data-type="minus" data-field="">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </span>
                                    <input type="text" name="quantity" class="form-control input-number {{ $product->price_status < 0 ? '' : parse_classname('product-order-quantity', 'quantity') }}" value="1" min="1" step="1">
                                    <span class="input-group-prepend">
                                        <button type="button" class="btn quantity-right-plus" data-type="plus" data-field="">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="add-btn d-flex">
                        <button type="submit" class="btn btn-theme square btn-outline-default btn-add-to-cart mr-10 ">
                            <img src="{{theme_asset('images/header/cart.png')}}" alt="">
                        </button>
                        <a href="javascript:void(0)"  class="btn btn-theme btn-colored-default btn-buy-now crazy-btn-buy-now">
                            Mua Ngay
                        </a>

                    </div>
                @else
                    <div class="text-center">
                        Sản phẩm tạm hết hàng
                    </div>
                @endif

            </div>

        </form>
    </div>
</div>
<!-- sticky cart bottom end -->

<div class="modal fade quick-view-modal show" id="review-form-modal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                @if (isset($is_rated) && $is_rated)
                    <h2 class="mb-20 review-form-title text-center">Đánh giá sản phẩm</h2>
                    <div class="review-box">
                        @if ($boughtProductOptions)

                        <form action="{{ route('web.products.review') }}" method="post" class="{{parse_classname('product-review-form')}}">
                            <div class="row g-4">
                                <input name="product_id" value="{{$product->id}}" hidden>
                                @csrf

                                <div class="col-12 col-md-6">
                                    <label class="mb-1 required" for="name">Họ và tên</label>
                                    <input type="text" class="form-control" id="name" placeholder="Nhập họ và tên" required="" value="{{$user->name}}" name="name">
                                </div>


                                <div class="col-12 col-md-6">
                                    <label class="mb-1 required" for="id">Email</label>
                                    <input type="email" class="form-control" id="name" placeholder="Nhập email" required="" value="{{$user->email}}" name="email">
                                </div>


                                <div class="col-12 col-md-6">
                                    <label class="mb-1 required" for="attr_values">Sản phẩm đã mua</label>
                                    @php
                                        $input = html_input([
                                            'type' => 'crazyselect',
                                            'name' => 'attr_values',
                                            'id' => 'attr_values',
                                            'data' => $boughtProductOptions,
                                            'class' => 'd-block'
                                        ]);
                                        $input->setTemplatePath('client-libs.form');

                                    @endphp
                                    @include($_lib.'form.crazyselect', ['input' => $input])
                                </div>

                                <div class="col-12 col-md-6 mt-20">
                                    <label class="d-none d-md-block mb-1">Đánh giá sản phẩm:</label>
                                    <div class="row g-4">
                                        <div class="col-4 col-md-4 stars-title"><label class="required">Điểm:</label></div>
                                        <div class="col-8 col-md-8 stars">
                                            <input class="star star-5" id="star-5" type="radio" name="rating" value="5" />
                                            <label class="star star-5" for="star-5"></label>
                                            <input class="star star-4" id="star-4" type="radio" name="rating" value="4" />
                                            <label class="star star-4" for="star-4"></label>
                                            <input class="star star-3" id="star-3" type="radio" name="rating" value="3" />
                                            <label class="star star-3" for="star-3"></label>
                                            <input class="star star-2" id="star-2" type="radio" name="rating" value="2" />
                                            <label class="star star-2" for="star-2"></label>
                                            <input class="star star-1" id="star-1" type="radio" name="rating" value="1" />
                                            <label class="star star-1" for="star-1"></label>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-12">
                                    <label class="mb-1 required" for="comments">Nội dung</label>
                                    <textarea class="form-control" placeholder="Nhập nội dung" id="comment" style="height: 100px" required="" name="comment"></textarea>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn default-light-theme default-theme default-theme-2">
                                        Gửi đánh giá
                                    </button>
                                </div>
                            </div>

                        </form>

                        @else
                            <div class="alert alert-warning text-center">
                                Bạn đã gửi đánh giá trước rồi
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        Bạn chưa mua sản phẩm nên không thể thực hiện đánh giá
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
