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
    
@endphp
@extends($_lib.'layout')
@section('body')
    

@if ($general->show_preloader)
    @include($_template.'preloader')
@endif


    <!-- mobile fix menu start -->
    @include($_template.'mobile-fix-menu')
    <!-- mobile fix menu end -->


    @yield('content')


    
    @include($_template.'js')

    

@endsection
