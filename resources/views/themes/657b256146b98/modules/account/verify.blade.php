@extends($_layout.'base')
@section('title', 'Xác minh tài khoản')
@section('page_type', 'my-account')

@section('meta.robots', 'noindex')

@section('content')

<div class="ps-my-account">
    <div class="container">
        <div class="ps-form--account ps-tab-root">
            <ul class="ps-tab-list">
                <li class="active"><a href="#forgot">Gửi Email xác minh tài khoản</a></li>

            </ul>
            <div class="ps-tabs">
                <div class="ps-tab active" id="forgot">
                    <form method="POST" action="{{route('web.account.verify.send-email')}}" class="{{parse_classname('verify-form')}}" >
                        @if ($next = old('next', $request->next))
                            <input type="hidden" name="next" value="{{$next}}">
                        @endif
                        @csrf

                        <div class="ps-form__content">
                            <h5>Tạo mật khẩu mới</h5>
                            <div class="form-group">
                                <input class="form-control" type="email" name="email" placeholder="Nhập Email của bạn">
                            </div>
                            @if ($error = session('error'))
                                <div class="alert alert-danger text-center">
                                    {{$error}}
                                </div>
                            @endif

                            @if ($error = session('error')??$errors->first('email'))
                                <div class="alert alert-danger text-center">
                                    {{$error}}
                                </div>
                            @endif
                            <div class="form-group submtit">
                                <button class="ps-btn ps-btn--fullwidth">Gửi</button>
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
