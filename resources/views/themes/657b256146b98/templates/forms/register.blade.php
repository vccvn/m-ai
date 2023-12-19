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
