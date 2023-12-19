@extends($_layout.'master')
@section('meta.robots', 'noindex')
@php
    add_js_src('static/frontend/js/filter.js');
 $user = Auth::user();
@endphp
@section('content')
    <div class="section section-padding">
        <section class="section-b-space min-80vh">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-xxl-3">
                        <div style="padding:0 0 20px 0; text-align:center">

                            <img src="{{frontend_asset('images/avatar.png')}}" alt="{{$user->name}}">

                            <br>
                            <br>
                            Chào mừng <b>{{$user->name}}</b>

                            <div class="profile-xeng-info">
                                <ul>
                                    <li><span><i class="fa fa-money" aria-hidden="true"></i> Số dư tài khoản:</span>
                                        <b>{{number_format($account->balance)}}</b> vnđ
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <ul class="nav nav-tabs custome-nav-tabs flex-column category-option" id="myTab">
                            <li class="nav-item mb-2">
                                <a href="/thanh-toan"
                                   class="nav-link font-light"><i class="fa fa-angle-right p-1"></i>Nạp tiền</a>
                            </li>
                            @foreach ($account->settings as $sk => $item)
                                <li class="nav-item mb-2">
                                    <a href="{{route('web.account.settings', ['tab' => $item->slug])}}"
                                       class="nav-link font-light {{$account->tab == $sk ? "active":""}}" id="tab"
                                       data-bs-target="#dash"><i class="fa fa-angle-right p-1"></i>{{$item->title}}</a>
                                </li>
                            @endforeach

                            <li class="nav-item">
                                <a href="{{route('web.exams.my-exams')}}" class="nav-link font-light" id="6-tab"
                                   data-bs-target="#dash" type="button"><i class="fa fa-file-word p-1"></i>Đề thi của rôi</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('web.account.logout')}}" class="nav-link font-light" id="7-tab"
                                   data-bs-target="#dash" type="button"><i class="fa fa-angle-right p-1"></i>Thoát</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-8 col-xxl-9">


                        @yield('account-content')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
