    <!-- Subscribe Section Start -->
    <section class="subscribe-section section-b-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-6">
                    <div class="subscribe-details">
                        <h3 class="mb-3">{{$data->title('Đăng ký theo dõi')}}</h3>
                        <h6 class="font-light">{{$data->description('Đăng ký để nhận những thông tin về các chương trình khuyến mãi sớm nhất từ chúng tôi!')}}</h6>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mt-md-0 mt-3">
                    <div class="subsribe-input">
                        <form  action="{{route('web.subscribe')}}" class="subcribe-form ">
                            <div class="input-group">
                                <input type="text" class="form-control subscribe-input" placeholder="{{$data->placeholder_text('Nhập email của bạn')}}">
                                <button class="btn btn-def-size btn-outline-default" type="button">{{$data->btn_text('Đăng ký')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Subscribe Section End -->
