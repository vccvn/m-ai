@if ($data->show && $data->promo_id && $promo = $helper->getPromoDetail(['id'=>$data->promo_id]))
    @php
        add_js_src(theme_asset('components/common/script.js'));
        $title = $data->title;
        $args = [
            '@limit' => $data->limit && $data->limit > 0 ? $data->limit : 4,
            '@sorttype' => $data->sorttype(1),
            '@with' => ['promoAvailable'],
            // '@withCountPromoAvailable' => [
            //     'promos.id' => $data->promo_id
            // ],
            // '@withReviews' => true,
            '@withOption' => true,
            '@withCategory' => true,
            '@promo' => $data->promo_id,

            '@withGallery' => true
        ];

        if($data->match_label && $data->match_label != 'none' && $data->labels){
            $args[$data->match_label == 'all' ? '@matchAllLabel' : '@hasAnyLabel'] = $data->labels;
        }
        if($data->match_tag && $data->match_tag != 'none' && $data->tags){
            $args[$data->match_tag == 'all' ? '@matchAllTag' : '@hasAnyTag'] = $data->tags;
        }

    @endphp

    @if (count($products = $helper->getProducts($args)))
        <div class="section-b-space section product-list-section section-default section-large">
            <div class="container container-max">
                <div class="filter-options section-header">
                    <div class="select-options">
                        <div class="image-icon">
                            <img src="{{theme_asset('images/flash-sale.png')}}" alt="">
                        </div>
                        <h2 class="section-title">{{$title}}</h2>
                        <div class="timer countdown" data-timestamp="{{$promo->finished_at}}" data-date-format="%d ngày" data-time-format="%h:%i:%s" data-delimiter=":">
                            <div class="days bordered"></div>
                            <div class="delimiter"> ngày - </div>
                            <div class="hours bordered"></div>
                            <div class="space">:</div>
                            <div class="minutes bordered"></div>
                            <div class="space">:</div>
                            <div class="seconds bordered"></div>
                        </div>
                    </div>
                    <div class="grid-options d-sm-inline-block d-none">
                        <a href="#" class="theme-color seemore">Xem thêm <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
                @include($_template.'products.slides', [
                    'products' => $products,
                    'item_class' => '',
                    'use_thubnail_slide' => true
                ])


                <div class="section-buttons text-center d-sm-none">
                    <a href="{{route('web.products')}}" class="btn btn-outline-default btn-def-size">Xem thêm</a>
                </div>
            </div>
        </div>

    @endif

@endif
