@extends($_layout . 'clean')
@section('body.class', 'account-page')


@section('content')
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Page Content -->
        <div class="content align-items-center">

            <div class="w-100 ">
                <div class="login-left">

                    <!-- Login Tab Content -->
                    <div class="account-content">
                        <div class="login-header">
                            <a href="/">
                                <img src="{{ $siteinfo->logo ?? theme_asset('img/logo-full.png') }}" alt="">
                            </a>
                        </div>
                        <div class="form-col">
                            <div class="login-text-details">
                                <h3>Đăng nhập</h3>
                                <p>Đăng nhập để tiếp tục</p>
                            </div>
                            <form class="{{ parse_classname('login-form') }}" action="{{ route('web.account.post-login') }}" method="POST">
                                @csrf
                                @if ($next = old('next', $request->next))
                                    <input type="hidden" name="next" value="{{ $next }}">
                                @endif
                                <div class="form-group">
                                    <label for="username">Tài khoản</label>
                                    <input class="form-control form-control-lg group_formcontrol" name="username" value="{{ old('username') }}" id="username" required="" placeholder="Tên đăng nhập hoặc email">
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu</label>
                                    <input class="form-control form-control-lg group_formcontrol" name="password" type="password" placeholder="Nhập mật khẩu">
                                </div>

                                @if ($error = session('error'))
                                    <span class="error">{{ $error }}</span>
                                @endif
                                <div class="form-group">
                                    <label class="custom-check d-flex">
                                        <input type="checkbox" name="remember_me">Duy trì đăng nhập
                                        <span class="checkmark"></span>

                                    </label>
                                </div>
                                <div class="pt-1">
                                    <div class="text-center">
                                        <button class="btn newgroup_create btn-block d-block w-100" type="submit">Đăng nhập</button>
                                    </div>
                                </div>

                                {{-- <div class="text-center dont-have">Don’t have an account? <a href="signup-email.html">Signup</a></div>
                                <div class="text-center mt-3">
                                    <span class="forgot-link">
                                        <a href="forgotpassword-email.html" class="text-end">Forgot Password ?</a>
                                    </span>
                                </div> --}}
                            </form>
                        </div>
                        <div class="back-btn-col text-center">
                            <a href="/"><span><i class="fas fa-caret-left"></i></span> Back</a>
                        </div>
                    </div>
                    <!-- /Login Tab Content -->
                </div>
                <div class="login-right">
                </div>
            </div>

        </div>
        <!-- /Page Content -->
    </div>
    <!-- /Main Wrapper -->
@endsection
