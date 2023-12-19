<?php 
    $site_name = $siteinfo->site_name('Gomee Inc'); 
    $web_title = $siteinfo->title('Trang chủ'.' | '.$site_name)
    

?>
@extends($_layout.'master')

@section('content')
    <h1 class="d-none">{{$web_title}}</h1>
    {!! $html->home_area->components !!}
@endsection

{{-- thêm js mà layout chua co --}}

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

