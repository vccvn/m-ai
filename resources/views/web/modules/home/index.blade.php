@extends($_layout . 'master')
@section('title', __('web.modules.home.pageTitle'))
@section('content')
    @php
        add_js_src('static/plugins/region-data-vn.js');
    @endphp


@endsection
