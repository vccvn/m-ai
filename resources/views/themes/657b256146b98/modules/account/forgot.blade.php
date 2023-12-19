@extends($_layout.'master')
@section('title', 'Quên mật khẩu')

@section('meta.robots', 'noindex')

@section('content')
    <div class="login-section">
        <div class="materialContainer">
            <div class="box">
                <div class="login-title">
                    <h2>Quên mật khẩu</h2>
                </div>
                <form method="POST" action="{{route('web.account.post-forgot')}}"
                      class="{{parse_classname('forgot-form')}}">
                    @if ($next = old('next', $request->next))
                        <input type="hidden" name="next" value="{{$next}}">
                    @endif
                    @csrf
                    <div class="input">
                        <label for="emailname">Nhập Email của bạn</label>
                        <input type="email" name="email" id="emailname">
                        <span class="spin"></span>
                    </div>
                    @if ($error = session('error'))
                        <span class="error">
                            {{$error}}
                        </span>
                    @endif
                    <div class="button login button-1">
                        <button>
                            <span>Gửi yêu cầu</span>
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
