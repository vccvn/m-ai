@php
add_js_src(theme_asset('components/common/script.js'));
@endphp

<div class="product-features desktop d-none d-md-block extra-block">
    <div class="row g-sm-4 g-3 row-cols-lg-4 row-cols-sm-2 row-cols-2">
        {!!$children!!}
    </div>
</div>

<div class="product-features mobile d-md-none extra-block">
    <div class="product-features-slide">
        {!!$children!!}
    </div>
</div>