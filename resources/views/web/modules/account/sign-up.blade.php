@extends($_layout.'colorlib-regform-7')
@section('title', 'Đăng ký tài khoản')
@section('content')

        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Đăng ký tài khoản</h2>
                        <form method="POST" action="{{route('account.signup')}}" class="register-form" id="register-form">
                            @csrf
                            <div class="form-group {{$errors->has('full_name')?'has-error':''}}">
                                <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="full_name" id="name" placeholder="Họ và tên" value="{{old('full_name')}}"/>
                            </div>
                            @if ($em = $errors->first('full_name'))
                                <div class="error-message">
                                    {{$em}}
                                </div>
                            @endif

                            <div class="form-group {{$errors->has('email')?'has-error':''}}">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Địa chỉ E-mail" value="{{old('email')}}"/>
                            </div>

                            @if ($em = $errors->first('email'))
                                <div class="error-message">
                                    {{$em}}
                                </div>
                            @endif
                            <div class="form-group {{$errors->has('password')?'has-error':''}}">
                                <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="pass" placeholder="Mật khẩu"/>
                            </div>

                            @if ($em = $errors->first('password'))
                                <div class="error-message">
                                    {{$em}}
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="password_confirmation" id="re_pass" placeholder="Nhập lại mật khẩu"/>
                            </div>


                            <div class="form-group {{$errors->has('agree')?'has-error':''}}">
                                <input type="checkbox" name="agree" id="agree-term" class="agree-term" {{old('agree')?'checked':''}} />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span>Tôi đã đọc và đồng ý với các <a href="#" class="term-service">Điều khoản</a></label>
                            </div>
                            @if ($em = $errors->first('agree'))
                                <div class="error-message">
                                    {{$em}}
                                </div>
                            @endif
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="btn-signup" class="form-submit" value="Đăng ký"/>
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="{{asset('static/system/forms/colorlib-regform-7/images/signup-image.jpg')}}" alt="sing up image"></figure>
                        <a href="{{route('account.sign-in')}}" class="signup-image-link">Đã có tài khoản</a>
                    </div>
                </div>
            </div>
        </section>
@endsection

@section('js')

<script src="{{asset('static/system/forms/colorlib-regform-7/js/signup.js')}}"></script>
@endsection
