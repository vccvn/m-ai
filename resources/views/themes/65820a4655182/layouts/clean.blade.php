@php
    $html->addTagAttribute('html', 'lang', 'vi-VN');
    $html->addTagAttribute('body', [
        'class' => $__env->yieldContent('body.class')
    ]);

@endphp

@extends($_lib.'layout')

@section('body')

    <div class="preloader">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="spinner"></div>
            </div>
        </div>
    </div>

    {{-- @include($_template.'header') --}}

    @yield('content')

    {{-- @include($_template.'footer') --}}

    @include($_template.'js')
@endsection
