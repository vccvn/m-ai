@extends($_layout.'colorlib-regform-7')
@section('title', 'Đăng nhập tài khoản')
@section('content')

        <!-- Sing in  Form -->
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="{{asset('static/system/forms/colorlib-regform-7/images/signin-image.jpg')}}" alt="sing up image"></figure>
                        <a href="{{route('account.sign-up')}}" class="signup-image-link">Tạo tài khoản</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Đăng nhập</h2>
                        <form method="POST" action="{{route('account.signin')}}" class="register-form" id="login-form">
                            @csrf
                            @if ($m = ($a = $errors->first('username'))?$a:session('error'))
                                <div class="error-message">
                                    {{$m}}
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="your_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="username" id="your_name" placeholder="Tên tài khoản / Email / SĐT"/>
                            </div>
                            <div class="form-group">
                                <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="your_pass" placeholder="Mật khẩu"/>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="remember_me" id="remember-me" class="agree-term" />
                                <label for="remember-me" class="label-agree-term"><span><span></span></span>Duy trì đăng nhập</label>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Đăng nhập"/>
                            </div>
                        </form>
                        {{-- <div class="social-login">
                            <span class="social-label">Or login with</span>
                            <ul class="socials">
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
            </div>
        </section>

@endsection
