
<!-- product section start -->
<section class="ratio_asos section section-small overflow-hidden  pb-20 pb-lg-50">
    <div class="container container-max">
        @include($_template.'products.list', [
            'use_header' => true,
            'title' => $data->title('Đề xuất mua cùng'),
            'products' => get_active_model('product')->getSuggestionProducts([
                '@limit' => $data->limit && $data->limit > 0 ? $data->limit : 4,
                '@sorttype' => $data->sorttype(1),
                '@with' => ['promoAvailable'], 
                '@withOption' => true,
                '@withCategory' => true,

                '@withGallery' => true,
                // 'a.a' => 0

            ]),
            'use_slide' => false,
            'use_thubnail_slide' => true
        ])
    </div>
</section>