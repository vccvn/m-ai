<!-- home slider start -->
@if(count($sliders = get_slider(['id' => $data->slider_id])) && $data->show)
    <section class="home-section home-style-2 pt-0">
        <div class="container-fluid p-0">
            <div class="slick-2 dot-dark">
                @foreach ($sliders->items as $item)
                    <?php //dd($item) ?>
                    <section class="timer-banner-style-2 pt-0" style="width: 100%; display: inline-block;">
                        <div>
                            <div class="row gy-3">
                                <div class="col-lg-12">
                                    <a href="{{$item->link}}" class="slide-link">
                                            <img src="{{$item->image_url}}" class="" alt="" style="width: 100%;">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>
@endif
<!-- home slider end -->