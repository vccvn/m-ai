@if($slider = get_slider(['status' => 1]))
    @php
    $exam = get_exam(['type' => 'public']);
    @endphp
    <div class="slider-section section bg-color-2">
        <div class="container">
            @foreach ($slider->items as $key => $item)
                @if($key == 0)
                <div class="row justify-content-center align-items-center">
                    <div class="col-md-6">
                        <!-- Slider Start -->
                        <div class="slider-content">
                            <h5 class="sub-title">{{$item['title']}}</h5>
                            <h2 class="title">{{$item['sub_title']}}</h2>
                            <p>{{$item['description']}}</p>
                            <a href="{{$exam?route('web.exams.detail', ['slug' => $exam->slug]):'#'}}" class="btn btn-primary ">Vào thi ngay</a>
                        </div>
                        <!-- Slider End -->
                    </div>

                    <div class="col-md-6 col-sm-8">
                        <!-- Slider Images Start -->
                        <div class="slider-images-02">

                            <div class="image-shape-01 parallaxed">
                                <img src="{{frontend_asset('images/shape/shape-11.svg')}}" alt="{{$item['title']}}">
                            </div>
                            <div class="image-shape-02 parallaxed"></div>
                            <div class="image-shape-03 parallaxed"></div>

                            <div class="image">
                                <img src="{{$item->getImage()}}" alt="{{$item['title']}}">
                            </div>
                        </div>
                        <!-- Slider Images End -->
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    <!-- Slider End -->
@endif
