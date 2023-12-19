@extends($_layout.'shop')
@section('title', $page_title)
@include($_lib.'register-meta')
@section('page.header.show', 'breadcrumb')

@section('content')
    @include($_current.'templates.filter')
        <div class="alert alert-error text-center">
            Không có kết quả phù hợp
        </div>
        @php
            add_css_link(theme_asset('css/vendors/ion.rangeSlider.min.css'));
            add_js_src(theme_asset('js/price-filter.js'));
            add_js_src(theme_asset('js/ion.rangeSlider.min.js'));
            add_js_src(theme_asset('js/filter.js'));
        @endphp


@endsection