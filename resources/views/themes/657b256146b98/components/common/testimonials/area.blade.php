
@if ($data->show)
    
    <section class="testimonial-section section ovxh section-large">
        <div class="container container-max">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-last">
                    <div class="review-box">
                        <div class="review-list">
                            @if ($data->list_type == 'data')
                                @if ($data->item_number > 0 && count($reviews = $helper->getProductReviews(['@sort' => $data->sort_type, '@limit' => $data->item_number])))
                                    @foreach ($reviews as $review)
                                        <div class="swiper-slide">
                                            <div class="testimonial-item">
                                                <div class="testi-thumb">
                                                    <img src="{{$review->getAvatar()}}" alt="{{$review->review_name}}">
                                                    <div class="author">
                                                        <h3>{{$review->review_name}}</h3>
                                                        <h4>Khách hàng</h4>
                                                    </div>
                                                </div>
                                                <p> "{{$review->comment}}"</p>
                                                <ul class="ratting">
                                                    @for ($i = 0; $i < $review->rating; $i++)
                                                        <li><i class="las la-star"></i></li>
                                                    @endfor
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @else
                                {!! $children !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 order-md-first">
                    <div class="say">
                        <h3>{{$data->title}}</h3>
                        <p>{{$data->description}}</p>
                        <div class="btns">
                            <a href="{{$data->url('#')}}" class="btn btn-colored-default btn-def-size">{{$data->btn_text('Khám phá')}}</a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section><!--/.testimonial-section-->


@endif