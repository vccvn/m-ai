@extends($_layout.'base')
@section('title', 'Tạo mật khẩu mới')
@section('meta.robots', 'noindex')

@section('content')

<div class="ps-my-account">
    <div class="container">
        <div class="ps-form--account ps-tab-root">
            <ul class="ps-tab-list">
                <li class="active"><a href="#forgot">Tạo mật khẩu mới</a></li>

            </ul>
            <div class="ps-tabs">
                <div class="ps-tab active" id="forgot">
                    <form method="POST" action="{{route('web.account.password.reset')}}" class="{{parse_classname('reset-form')}}" >
                        @if ($next = old('next', $request->next))
                            <input type="hidden" name="next" value="{{$next}}">
                        @endif
                        @csrf
                        <input type="hidden" name="token" value="{{old('token', $token)}}">

                        <div class="ps-form__content">
                            <h5>Tạo mật khẩu mới</h5>
                            <div class="form-group">
                                <input class="form-control" type="password" name="password" placeholder="Mật khẩu mới">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu">
                            </div>

                            @if ($error = $errors->first('password')??session('error'))
                                <div class="alert alert-danger text-center">
                                    {{$error}}
                                </div>
                            @endif
                            <div class="form-group submtit">
                                <button class="ps-btn ps-btn--fullwidth">Tạo mật khẩu</button>
                            </div>
                        </div>

                        <div class="ps-form__footer">
                            <p>
                                <a href="{{route('web.account.login')}}">Đăng nhập</a>
                                |
                                <a href="{{route('web.account.register')}}">Đăng ký</a>
                            </p>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
