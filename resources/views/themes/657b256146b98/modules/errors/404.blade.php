
@extends($_layout.'master')
{{-- @section('meta.robots', 'noindex') --}}
@section('title', '404 - Không tìm thấy')

@section('content')
        <!-- 404 Section Start -->
        <section class="page-not-found section-b-space section">
            <div class="container">
                <div class="row gx-md-2 gx-0 gy-md-0 gy-3">
                    <div class="col-md-5 col-xl-4 col-xxl-3">
                        <div class="page-image">
                            <img src="{{theme_asset('images/inner-page/404.png')}}" class="img-fluid blur-up lazyload" alt="">
                        </div>
                    </div>

                    <div class="col-md-7 mt-md-0 mt-3 col-xl-5 d-flex flex-center">
                        <div class="text-center text-md-left">
                            <div>
                                <h2 class="mb-2">Không tìm thấy</h2>
                                <p class="mb-3">Trang mà bạn truy cập có thể đang bị lỗi tạm thời hoặc đã bị xóa, bạn có thể truy cập sau hoặc xem thêm các sản phẩm hấp dẫn khác trên website <strong>Wisestyle.vn</strong></p>
                                <a href="{{ route('home') }}" class="btn btn-colored-default">Về Trang chủ</a>
                            </div>
                        </div>
                    </div>
                </div>


                @php
add_js_src(theme_asset('components/common/script.js'));
        $args = [];
        $routeParams = [];
        $url = route('web.products');
            $args['@limit'] = 4;
            $args['@sorttype'] = 'rand()';
            $url.="?sorttype=6";


        if($args){
            $args = array_merge($args, [
                '@with' => ['promoAvailable'],
                '@withOption' => true,
                '@withGallery' => true,
                '@withCategory' => true

            ]);
        }
    @endphp
    @if ($args && count($products = $helper->getProducts($args)))
                <div class="filter-options mb-10 mb-lg-12 mt-15 mt-md-50">
                    <div class="select-options section-header  mb-0">
                        <h2 class="section-title mb-0">Có thể bạn đang tìm</h2>
                    </div>
                    <div class="grid-options d-sm-inline-block d-none">
                        <a href="{{$url}}" class="theme-color seemore">Xem thêm <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
                <div class="row g-sm-4 g-3 row-cols-lg-4 row-cols-md-2 row-cols-2 mt-1 product-style-2 ratio_asos product-list-section">
                    @foreach ($products as $product)
                        <div class="mt-0">
                            @include($_template.'products.grid-item', [
                                'product' => $product,
                                'item_class' => 'mb-12 mb-lg-20',
                                'use_thubnail_slide' => true
                            ])
                        </div>
                    @endforeach
                </div>


                <div class="section-buttons mt-10 text-center d-sm-none">
                    <a href="{{$url}}" class="btn btn-outline-default btn-def-size">Xem thêm</a>
                </div>


    @endif


            </div>
        </section>
        <!-- 404 Section End -->
@endsection
