@extends($_layout.'master')
@section('title', $page_title)
@include($_lib.'register-meta')
@section('content')
<div class="section section-personal-style-form ovxh section-default section-small">

@if (count($products))
<div class="section product-list-section section-default ovxh pb-0 pb-sm-10 pb-md-10 pb-xxl-20 pt-12 pt-md-20 pt-xl-25 section-small personal-style-products">
    <div class="container-max">
        <div class="style-header">
            <div class="left-side">
                <h3>Style {{$style->name}}</h3>
                <div class="tab-nav">
                    <ul>

                        @if (count($product_parameters))
                            @foreach ($product_parameters as $id => $item)
                                <li>
                                    <a href="{{route('web.style-sets.suggest-products', ['id' => $style->id, 'tab' => $id])}}" class="tab-link {{$id == $tab?'active':''}}">{{$item['item_name']}}</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="right-side">
                <a href="{{route('web.style-sets.update', ['id' => $style->id])}}" class="btn btn-outline-default">Sửa style</a>
            </div>
        </div>

    </div>
    @include($_template.'products.list', [
        'products' => $products,
        'use_header' => true,
        'use_thubnail_slide' => true,
        'title' => 'Sản phẩm đề xuất cho bạn',
        'list_class' => '',
        'use_thubnail_slide' => true,
        'use_container' => true,
    ])
    {{$products->links($_template.'pagination')}}
</div>
@endif


@endsection

{{-- thêm js mà layout chua co --}}
@php
    add_css_link(theme_asset('css/vendors/ion.rangeSlider.min.css'));
    add_js_src(theme_asset('js/ion.rangeSlider.min.js'));
    add_js_src(theme_asset('components/common/script.js'));

@endphp
@section('js')

    <!-- newsletter js -->
    <script src="{{theme_asset('js/newsletter.js')}}"></script>

    <!-- add to cart modal resize -->
    {{-- <script src="{{theme_asset('js/cart_modal_resize.js')}}"></script> --}}


    <script>
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        });
    </script>

@endsection

