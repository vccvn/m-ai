
@extends($_layout.'colorlib-regform-7')
<?php
$type = isset($type) && $type ? $type : (session('type') ? session('type') : 'success');
$message = isset($message) && $message ? $message : (session('message') ? session('message') : 'Hello World');
$link = isset($link) ? $link : (session('link') ? session('link') : route('account.sign-in'));
$text = isset($text) ? $text : (session('text') ? session('text') : '<i class="fa fa-home"></i> Về trang chủ');
$title = isset($title) && $title ? $title : (session('title') ? session('title') : null);

?>
{{-- khai báo title --}}
@section('title', isset($title)?$title:"Thông báo")



@section('content')
    <!-- Sign up form -->
    <section class="signup">
        <div class="container">
            <div class="alert alert-{{$type}} text-center">{!! $message !!}</div>
        </div>
        <div class="buttons text-center" style="margin: 20px auto;">
            <a href="{{$link}}" class="theme-btn btn-style-two">{!! $text !!}</a>
        </div>
    </section>
@endsection