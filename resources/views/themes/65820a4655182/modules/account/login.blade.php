@extends($_layout . 'clean')
@section('body.class', 'account-page')


@section('content')

    <div class="sign-in-area pt-50 pb-70">
        <div class="container">
            <div class="section-title text-center">
                <span class="sp-before sp-after">Đăng nhập</span>
                <h2 class="h2-color">Đăng nhập hệ thống</h2>
            </div>
            <div class="row align-items-center pt-45">
                <div class="col-lg-5">
                    <div class="user-all-img">
                        <img src="{{ ai_startup_asset('/img/faq-img.png') }}" alt="Images">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="user-all-form">
                        <div class="contact-form">
                            <form class="{{ parse_classname('login-form') }}" action="{{ route('web.account.post-login') }}" method="POST">
                                @csrf
                                @if ($next = old('next', $request->next))
                                    <input type="hidden" name="next" value="{{ $next }}">
                                @endif

                                <div class="row">
                                    <div class="col-lg-12 ">
                                        <div class="form-group">
                                            <i class="bx bx-user"></i>
                                            <input type="text" name="username" value="{{ old('username') }}" id="username" required="" placeholder="Tên đăng nhập hoặc email" class="form-control" data-error="vui lòng nhập Username hoặc Email">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <i class="bx bx-lock-alt"></i>
                                            <input class="form-control" type="password" name="password" placeholder="Mật khẩu">
                                        </div>

                                        @if ($error = session('error'))
                                            <div class="error text-danger mb-3">{{ $error }}</div>
                                        @endif

                                    </div>
                                    <div class="col-lg-6 col-sm-6 form-condition">
                                        <div class="agree-label">
                                            <input type="checkbox" id="chb1" name="remember_me">
                                            <label for="chb1">
                                                Duy trì đăng nhập
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <a class="forget" href="{{ route('web.account.forgot') }}">Quên mật khẩu?</a>
                                    </div>
                                    <div class="col-lg-12 col-md-12 text-center mt-3">
                                        <button type="submit" class="default-btn">
                                            Đăng nhập
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <p class="account-desc">
                                            Chưa có tài khoản?
                                            <a href="{{ route('web.account.register') }}">Đăng ký</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
