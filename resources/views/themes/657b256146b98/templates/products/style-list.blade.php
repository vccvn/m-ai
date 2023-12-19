@php
    $container = isset($use_container) && $use_container ? 'container container-max' : '';
    if(isset($list_class) && $list_class) $container .= ' '.$list_class;
    $header = isset($use_header) && $use_header;
    $seemore = isset($seemore) && $seemore?$seemore:'';
    $seemore_text = isset($seemore_text) && $seemore_text?$seemore_text:'Xem thêm';

    $useSlide = isset($use_slide)?$use_slide:false;
    $slideOnMobile = $slide_on_mobile??($slideOnMobile??false);
    $item_class = $item_class??'';
    $use_thubnail_slide = $use_thubnail_slide??false;
    $slide_thumbnail_class = $slide_thumbnail_class??null;
    $total_price = 0;
    foreach ($products as $product){
        $total_price += $product->checkPrice($product->style_attrs);
    }
            
@endphp
<div class="{{$container}}">
    @if ($header)
        
    <div class="filter-options section-header">
        <div class="select-options">
            <h2 class="section-title">{{$title??'Danh sách sản phẩm'}}</h2> 

        </div>
        @if (isset($edit_url))
            
        <div class="btns">
            <a href="{{$edit_url}}" class="btn btn-outline-default btn-show-edit-form">Sửa style</a>
        </div>
        
        @endif
        
        
    </div>
    
    @endif
    @if ($useSlide)
        
        <div class="ratio_asos product-list-section product-style-list {{isset($slide_class) && $slide_class ? $slide_class : 'product-slide'}} {{$slideOnMobile?'':'d-none d-md-block'}}" id="style-product-list">
            @foreach ($products as $product)   
                <div class="item-wrapper">
                    @include($_template.'products.grid-item', [
                        'product' => $product,
                        'item_class' => $item_class,
                        'use_thubnail_slide' => $use_thubnail_slide,
                        'thumbnail_class' => $slide_thumbnail_class
                    ])
                </div>
            @endforeach
        </div>

    @endif
    @if (!$slideOnMobile || !$useSlide)
        
    <div class="row g-sm-4 g-3 row-cols-lg-4 row-cols-md-2 row-cols-2 mt-1  product-style-2 ratio_asos product-list-section product-style-list {{$useSlide && !$slideOnMobile?'d-md-none':''}}" id="style-product-list">
        @foreach ($products as $product)
            <div class="item-wrapper mt-0">
                @include($_template.'products.grid-item', [
                    'product' => $product,
                    'item_class' => $item_class,
                    'use_thubnail_slide' => $use_thubnail_slide,
                    'thumbnail_class' => $slide_thumbnail_class
                ])
            </div>
        @endforeach
    </div>


    @endif
    
    <div class="actions">
        <div class="btns">
            <a href="javascript:;" class="btn btn-outline-default btn-add-cart btn-theme square {{parse_classname('add-many-to-cart')}}" data-list-id="style-product-list">
                <img src="{{theme_asset('images/header/cart.png')}}" alt="">
            </a>
            <a href="javascript:;" class="btn btn-colored-default btn-theme btn-buy-now {{parse_classname('add-many-to-cart')}}" data-list-id="style-product-list" data-redirect="checkout">
                Mua Combo {{get_currency_format($total_price)}}
            </a>
        </div>

        @if ($seemore)
        <a href="{{$seemore}}" class="btn btn-outline-default btn-def-size">{{$seemore_text}} <i class="fa fa-angle-right"></i></a>
        @endif
        
        
    </div>
</div>