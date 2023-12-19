@php
    $container = isset($use_container) && $use_container ? 'container container-max' : '';
    $header = isset($use_header) && $use_header;
    if(isset($block_class)) $container .= ' ' . $block_class;
    $seemore = isset($seemore) && $seemore?$seemore:'';
    $seemore_text = isset($seemore_text) && $seemore_text?$seemore_text:'Xem thêm';
    
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
    <div class="ratio_asos product-list-section {{isset($slide_class) && $slide_class ? $slide_class : 'product-slide'}}">
        @foreach ($products as $product)   
            <div class="item-wrapper mt-0">
                @include($_template.'products.grid-item', [
                    'product' => $product,
                    'item_class' => $item_class??'',
                    'use_thubnail_slide' => $use_thubnail_slide??false,
                    'thumbnail_class' => $slide_thumbnail_class??null
                ])
            </div>
        @endforeach
    </div>



    @if ($seemore)
        
    <div class="section-buttons text-center d-sm-none">
        <a href="{{$seemore}}" class="btn btn-outline-default btn-def-size">{{$seemore_text}}</a>
    </div>
    
    @endif
</div>