@extends($_layout.'master')
@section('title', $page_title??($style?'Cập nhật':'Tạo') . ' style cá nhân')
@include($_lib.'register-meta')
@php

    $cond = $style && count($products);
@endphp
@section('content')
@if (!$cond)
    <div class="section section-personal-style-form ovxh section-default section-small">
        <div class="container-max">
            <div class="style-header">
                <div class="left-side">
                    <h3>Style {{$style->name}}</h3>

                </div>
                <div class="right-side">
                    <a href="{{route('web.style-sets.update', ['id' => $style->id])}}" class="btn btn-outline-default btn-show-edit-form">Sửa style</a>
                </div>
            </div>

        </div>
    </div>
@else


    <div class="section product-list-section section-default ovxh pb-40 pb-sm-10 pb-md-10 pb-xxl-20 pt-12 pt-md-20 pt-xl-25 section-small personal-style-products">
        @include($_template.'products.style-list', [
            'products' => $products,
            'use_header' => true,
            'use_thubnail_slide' => true,
            'title' => "Style $style->name",
            'list_class' => '',
            'use_thubnail_slide' => true,
            'use_container' => true,
            'seemore' => route('web.style-sets.suggest-products', ['id' => $style->id]),
            'seemore_text' => 'Xem thêm sản phẩm đề xuất',
            'edit_url' => route('web.style-sets.update', ['id' => $style->id])
        ])
    </div>
@endif


@endsection

{{-- thêm js mà layout chua co --}}
@php
    add_css_link(theme_asset('css/vendors/ion.rangeSlider.min.css'));
    add_js_src(theme_asset('js/ion.rangeSlider.min.js'));
    add_js_src(theme_asset('components/common/script.js'));

@endphp
@section('css')

@endsection
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
