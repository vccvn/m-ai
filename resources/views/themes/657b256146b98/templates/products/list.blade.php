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
@endphp
<div class="{{$container}}">
    @if ($header)
        
    <div class="filter-options section-header">
        <div class="select-options">
            <h2 class="section-title">{{$title??'Danh sách sản phẩm'}}</h2> 
        </div>
        @if ($seemore)
        <div class="grid-options d-sm-inline-block d-none">
            <a href="{{$seemore}}" class="theme-color seemore">{{$seemore_text}} <i class="fa fa-angle-right"></i></a>
        </div>    
        @endif
        
    </div>
    
    @endif
    @if ($useSlide)
        
        <div class="ratio_asos product-list-section {{isset($slide_class) && $slide_class ? $slide_class : 'product-slide'}} {{$slideOnMobile?'':'d-none d-md-block'}}">
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
        
    <div class="row g-sm-4 g-3 row-cols-lg-4 row-cols-md-2 row-cols-2 mt-1  product-style-2 ratio_asos product-list-section {{$useSlide && !$slideOnMobile?'d-md-none':''}}">
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
    @if ($seemore)
        
    <div class="section-buttons mt-10 text-center d-sm-none">
        <a href="{{$seemore}}" class="btn btn-outline-default btn-def-size">{{$seemore_text}}</a>
    </div>
    
    @endif
</div>