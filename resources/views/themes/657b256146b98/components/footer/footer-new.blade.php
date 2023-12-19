@if($data->show)
    <div class="col-xl-{{$data->col_xl(2)}} col-lg-{{$data->col_lg(3)}} col-md-{{$data->col_md(4)}} col-sm-{{$data->col_sm(6)}} col-sm-{{$data->col_xs(12)}}  {{$data->class}}">
        <div class="ft">
            <div class="footer-title">
                <h3>{{$data->title}}</h3>
            </div>
            <div class="fe-content">
                <div class="footer-newsletter">
                    <div class="ps-form--newsletter {{parse_classname('subcribe-form')}}" action="{{route('web.subscribe')}}">
                        <div class="form-newsletter">
                            <div class="input-group mb-4 t-form-email">
                                <input type="email" name="email" class="form-control" placeholder="Nhập email">
                                <button type="button" class="btn btn-theme-color {{parse_classname('btn-subscribe')}}">Đăng ký</button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
        <div class="footer-newsletter">
            <div class="form-newsletter">
                @if ($data->content_type == 'custom')
                    {!! $children !!}
                @else

                    <p>
                        <img src="{{theme_asset('images/custom/cart/icon-phone.png')}}" alt="">
                        <span>Liên hệ: <a href="tel:{{$data->phone}}">{{$data->phone}}</a></span>
                    </p>
                    <p>
                        <img src="{{theme_asset('images/custom/cart/icon-mail.png')}}" alt="">
                        <span>Email: <a href="mailto:{{$data->email}}">{{$data->email}}</a></span>
                    </p>
                    <p>
                        <img src="{{theme_asset('images/custom/cart/icon-location.png')}}" alt="">
                        <span>{{$data->address}}</span>
                    </p>
                @endif

                <ul class="social">
                    <li><a href="{{$data->tiktok}}" rel="nofollow" target="_blank"><img src="{{theme_asset('images/custom/cart/icon-tiktok.png')}}" alt=""></a></li>
                    <li><a href="{{$data->instagram}}" rel="nofollow" target="_blank"><img src="{{theme_asset('images/custom/cart/icon-instagram.png')}}" alt=""></a></li>
                    <li><a href="{{$data->facebook}}" rel="nofollow" target="_blank"><img src="{{theme_asset('images/custom/cart/icon-facebook.png')}}" alt=""></a></li>
                </ul>

                @if ($data->dcma)
                    <p class="dcma">
                        {!! $data->dcma !!}
                    </p>
                @endif
            </div>
        </div>
    </div>
@endif
