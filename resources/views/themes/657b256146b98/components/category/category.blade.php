@if($data->show)
{{--<section class="service-section service-style-2 section-b-space">--}}
{{--    <div class="container-category">--}}
{{--        <div class="row g-4 g-sm-3">--}}
{{--            {!! $children !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<div class="box-intro-cate section-large">
    <div class="container container-max">
        <div class="row">
            {!! $children !!}
        </div>
    </div>
</div>
@endif