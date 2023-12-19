@php
    $color = '#e22454';
    $textInsideColor = '#ffffff';
    if($general = $options->theme->general){
        $color = $general->theme_primary_color($color);
        $textInsideColor = $general->text_inside_color($textInsideColor);
    }
    $html->addTagAttribute('html', 'lang', 'vi-VN');
    $html->addTagAttribute('body', [
        'class'=> "theme-color2 light ltr ". $__env->yieldContent('body.class'),
        'style' => "--theme-color:$color;--text-inside-color: $textInsideColor"
    ]);
    $general = $options->theme->general;
    $show_header = $__env->yieldContent('page.header.show');
@endphp
@extends($_lib.'layout')
@section('body')
@if ($general->show_preloader)
    @include($_template.'preloader')
@endif

    <!-- header start -->
    @include($_template.'header')
    <!-- header end -->

    <!-- mobile fix menu start -->
    @include($_template.'mobile-fix-menu')
    <!-- mobile fix menu end -->

    <!-- Breadcrumb section start -->
    @if(in_array($show_header, ["breadcrumb", "breadcrumbs"]))
        @include($_template.'breadcrumb')
    @elseif(in_array($show_header, ['show', 1, true, "true", "on"]))
        @include($_template.'page-header')
    @endif
    <!-- Breadcrumb section end -->

    @yield('content')



    <!-- footer start -->
    @include($_template.'footer')
    <!-- footer end -->
    @include($_template. 'style-sets.create-style-modal')
    @include($_template.'js')

    

@endsection
