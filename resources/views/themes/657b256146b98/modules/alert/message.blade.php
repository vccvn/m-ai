@extends($_layout.'master')
@section('title', 'Thông báo')
@section('meta.robots', 'noindex')

@include($_lib.'register-meta')

@section('content')
    <?php
    $type = (isset($type) && $type)?$type:(session('type')?session('type'):'success');
    $message = (isset($message) && $message)?$message:(session('message')?session('message'):'Hello World');
    $link = isset($link)?$link:(session('link')?session('link'):route('home'));
    $text = isset($text)?$text:(session('text')?session('text'):'<i class="fa fa-home"></i> Về trang chủ');
    $title = (isset($title) && $title)?$title:(session('title')?session('title'):null);

    ?>
    <section class="section-b-space min-80vh">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pb-3 text-center mt-4">
                    <h4>{!! $message !!}</h4>
                </div>
                <div class="modal-footer d-block text-center mb-4">
                    <a href="{{$link}}" class="btn btn-def-size btn-theme-color rounded" data-bs-target="#doneModal" data-bs-dismiss="modal">{!! $text !!}</a>
                </div>
            </div>
        </div>
    </section>

@endsection