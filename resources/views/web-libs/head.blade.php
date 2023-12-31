@php
    $subTemplateFolder = $__env->yieldContent('sub-template', $__env->yieldContent('subTemplate', $__env->yieldContent('template.sub')));
    if($subTemplateFolder) $subTemplateFolder.='.';
@endphp
@include($_lib.'meta')
<link rel="stylesheet" href="{{asset('static/app/css/app.min.css')}}">
@include($_template.$subTemplateFolder.'links')
@yield('css')
@if ($css = get_custom_css())

    <style>
    {!! $css !!}
    </style>

@endif
@if ($links = get_css_link())

@foreach ($links as $link)

    <link rel="stylesheet" href="{{$link}}">

@endforeach
@endif
{!! $html->getAndCleanCss() !!}
{!! $html->head->embeds !!}
