@php
    $productSettings = $options->theme->products;
    $pTitle = $productSettings->page_title;
    $mTitle = $productSettings->meta_title;
    $mDesc  = $productSettings->page_description;
    if (isset($category) && $category){
        if ($category->page_title){
            $pTitle = $category->page_title;
        }
        if ($category->meta_title){
            $mTitle = $category->meta_title;
        }
        if ($category->description){
            $mDesc = $category->description;
        }
    }
    if($pTitle){
        $page_title = $pTitle;
    }
@endphp
@extends($_layout.'shop')
@section('title', $page_title)
@include($_lib.'register-meta')

@if ($pTitle)
    @section('title', $pTitle)
@endif
@if ($mTitle)
    @section('meta_title', $mTitle)
@endif
@if ($mDesc)
    @section('meta_description', $mDesc)
@endif



@section('page.header.show', 'breadcrumb')
@section('breadcrumb.section_class', 'section-large')
@section('breadcrumb.container_class', 'container-max')


@section('content')
@include($_current.'templates.filter')


<!-- label and featured section -->
@if (count($products))
    
    <!-- Prodcut setion -->
    <div class="row row-cols-max-4  row-cols-lg-3 row-cols-md-3 row-cols-2 mt-3  product-style-2 ratio_asos product-list-section">
        @foreach ($products as $product)
            <div>
                @include($_template.'products.grid-item', [
                    'product' => $product,
                    'item_class' => 'mb-10',
                    'use_thubnail_slide' => true
                ])
            </div>
        @endforeach
    </div>
    {{$products->links($_template.'pagination')}}

    @if ((!$request->page || $request->page == 1) && isset($category) && $category && $category->second_content)
        <div class="cate-content-box {{strlen($category->second_content) > 300?'viewmoreable':''}} mt-4">
            <div class="content-box article-content">
                {!! $category->second_content !!}
            </div>
            
            <div class="see-more-content">
                <a href="javascript:;" class="btn-see-more">Xem thêm</a>
            </div>
        </div>
    @endif
@else
    <div class="alert alert-error text-center">
        Không có kết quả phù hợp
    </div>
@endif

@php
    add_css_link(theme_asset('css/vendors/ion.rangeSlider.min.css'));
    add_js_src(theme_asset('js/price-filter.js'));
    add_js_src(theme_asset('js/ion.rangeSlider.min.js'));
    add_js_src(theme_asset('js/filter.js'));
    $filter_url = str_replace($u = route('home'), $u . '/ajax', $current_url);
@endphp
@endsection
@section('jsinit')
    <script>
        var infinity_load_data = {
            data: {!! json_encode($products->toArray()) !!},
            url: "{{$filter_url}}",
            parameters : {!! json_encode($request->all()) !!}
        };
    </script>
@endsection