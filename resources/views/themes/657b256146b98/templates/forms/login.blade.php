
<form class="{{parse_classname('login-form')}}" action="{{route('web.account.post-login')}}" method="POST">
    @if ($next = old('next', $request->next))
        <input type="hidden" name="next" value="{{$next}}">
    @endif
    @csrf
    <div class="box">
        <div class="login-title">
            <h2>Đăng nhập</h2>
        </div>
        <div class="input">
            <input type="text" name="username" value="{{old('username')}}" id="name" required="" placeholder="Tên đăng nhập hoặc email">
            <span class="spin"></span>
            <div class="valid-feedback">
                Please fill the name
            </div>
        </div>

        <div class="input">
            <input type="password" name="password" id="pass" placeholder="Mật khẩu">
            <span class="spin"></span>
        </div>

        @if ($error = session('error'))
            <span class="error">{{$error}}</span>
        @endif
        <a href="{{route('web.account.forgot')}}" class="pass-forgot">Quên mật khẩu?</a>

        <div class="button login">
            <button type="submit">
                <span>Đăng nhập</span>
                <i class="fa fa-check"></i>
            </button>
        </div>

        <p>Chưa có tài khoản? <a href="{{route('web.account.register')}}" class="theme-color">Đăng ký ngay</a></p>

    </div>
</form>
