
<!-- product section start -->
<section class="ratio_asos section section-small overflow-hidden  pb-20 pb-lg-50">
    <div class="container-max">
        @include($_template.'products.list', [
            'use_header' => $data->title,
            'title' => 'Đề xuất mua cùng',
            'products' => get_active_model('product')->getRelated([
                '@limit' => 8,
                '@with' => ['gallery', 'promoAvailable'],
                '@withOption' => true
            ]),
            'use_slide' => true,
            'use_thubnail_slide' => true
        ])
    </div>
</section>