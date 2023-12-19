@extends($_layout.'master')
@section('title', 'Đăng ký tài khoản')
@section('page_type', 'my-account')
@section('meta.robots', 'noindex')

@section('content')
    <div class="login-section">
        <div class="materialContainer">
            <div class="box">
                <div class="login-title">
                    <h2>Đăng ký</h2>
                </div>
                <form class="{{parse_classname('register-form')}}" action="{{route('web.account.post-register')}}" method="POST">
                    @if ($next = old('next', $request->next))
                        <input type="hidden" name="next" value="{{$next}}">
                    @endif
                    @php
                        $registerForm = $html->getRegisterForm([
                            'class' => 'form-control'
                        ]);
                    @endphp
                    @csrf
                    @if ($registerForm && $inputs = $registerForm->inputs())
                        @foreach ($inputs as $input)
                            <div class="input">
                                {!! $input !!}
                                @if ($input->error)
                                    <div class="error-register">{{$input->error}}</div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <div class="button login">
                        <button>
                            <span>Đăng ký</span>
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </form>


                <p>Đã có tài khoản? <a href="{{route('web.account.login')}}" class="theme-color">Đăng nhập</a></p>
                <p><a href="{{route('home')}}" class="theme-color"><i class="fa fa-arrow-single-left"></i> Về trang chủ</a></p>
            </div>
        </div>
    </div>

@endsection
