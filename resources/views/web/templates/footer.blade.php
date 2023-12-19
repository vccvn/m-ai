{{-- <div class="stop-sticky"></div> --}}
<!-- Footer Start -->
<div class="footer-section-02 section bca-footer" id="footer">
    <div class="container">

        <!-- Footer Widget Wrapper Start -->
        <div class="footer-widget-wrapper">
            <div class="row">
                {{--
                <div class="col-12 mb-15">
                    <img src="{{$siteinfo->footer_logo(frontend_asset('images/footer-logo.png'))}}" alt="{{$siteinfo->site_name}}">
                </div> --}}


                <div class="col-lg-5 col-sm-6">
                    <!-- Footer Widget Start -->
                    <div class="footer-widget">
                        <h4 class="footer-widget-title text-white">{{ $general->footer_column_1_title('Tổng đài tuyển sinh Công An Nhân Dân') }}</h4>

                        <div class="widget-link widget-link-white">
                            {!! $helper->getCustomMenu(['id' => $general->footer_column_1_menu_id], 1, ['class' => 'link'])->addAction(function ($item, $link) {
                                $item->removeClass();
                                $link->removeClass();
                            }) !!}
                        </div>
                    </div>
                    <!-- Footer Widget End -->
                </div>

                <div class="col-lg-3 col-sm-6">
                    <!-- Footer Widget Start -->
                    <div class="footer-widget">
                        <h4 class="footer-widget-title text-white">{{ $general->footer_column_2_title('Về Chúng Tôi') }}</h4>

                        <div class="widget-link widget-link-white">
                            {!! $helper->getCustomMenu(['id' => $general->footer_column_2_menu_id], 1, ['class' => 'link'])->addAction(function ($item, $link) {
                                $item->removeClass();
                                $link->removeClass();
                            }) !!}
                        </div>
                    </div>
                    <!-- Footer Widget End -->
                </div>

                <div class="col-lg-4 col-sm-6">
                    <!-- Footer Widget Start -->
                    <div class="footer-box">
                        <div class="box-header">
                            <h4 class="box-title text-white">{{ $general->footer_column_3_title('THỐNG KẾ TRUY CẬP') }}</h4>
                        </div>

                        <div class="box-body widget-link widget-link-white">
                            <ul class="link">
                                <li class="visiting-item"><a href="#visiting"><i class="fa fa-user"></i> Đang truy cập: <span></span> </a></li>
                                <li class="visiting-today"><a href="#"><i class="fa fa-clock"></i> Hôm nay: <span></span> </a></li>
                                <li class="month-view"><a href="#month"><i class="fa fa-calendar-alt"></i> Tháng hiện tại: <span></span> </a></li>
                                <li class="visit-total"><a href="#visit-total"><i class="fa fa-signal"></i> Tổng lượt truy cập: <span></span> </a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Footer Widget End -->
                </div>


            </div>
        </div>
        <!-- Footer Widget Wrapper End -->

        <!-- Footer Copyright End -->
        <div class="footer-copyright footer-copyright-white text-center">
            {!! $general->footer_copyright('<p>BẢN QUYỀN 2023 thuộc Cục Đào tạo, Tổng công ty Gtel</p>') !!}
        </div>
        <!-- Footer Copyright End -->

    </div>
</div>
<!-- Footer End -->
@php
    $hotline = $home->admission_hotline('19000146');
@endphp
<div class="hotline-phone-ring-wrap">
    <div class="hotline-phone-ring">
        <div class="hotline-phone-ring-circle"></div>
        <div class="hotline-phone-ring-circle-fill"></div>
        <div class="hotline-phone-ring-img-circle">
            <a href="tel:{{ $sp = str_replace(['.', ' '], '', $hotline) }}" class="pps-btn-img">
                <img src="{{ frontend_asset('img/call.png') }}" alt="{{ $home->call_text('Gọi điện thoại') }}" width="80">
            </a>
        </div>
    </div>
    <div class="hotline-bar"> <a href="tel:{{ $sp }}"> <span class="text-hotline">{{ $hotline }}</span> </a></div>
</div>
