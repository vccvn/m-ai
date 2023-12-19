@extends($_layout.'master')
{{-- @section('title', $account->formConfig->title) --}}
@include($_lib.'register-meta')

@section('content')

<!-- Log In Section Start -->
<div class="login-section">
    <div class="materialContainer">
        <div class="box">
            <div class="login-title">
                <h2>Thanh Toán</h2>
            </div>

            <form class="form" action="{{route('web.payments.check-order')}}" method="POST">
                @csrf
                @if ($error = session('error'))
                <div class="alert alert-danger text-center">
                    {{$error}}
                </div>
                @endif

                <div class="input">
                    <label class="form__label" for="contact">
                        Email hoặc Số điện thoại <span>*</span>
                    </label>
                    <input type="text" name="contact" id="contact" class="form-control" value="{{old('contact')}}" placeholder="">
                </div>
                @if ($error = $errors->first('contact'))
                <div class="alert alert-danger text-center">
                    {{$error}}
                </div>
                @endif
                <div class="input">
                    <label class="form__label" for="order_code">
                        Mã đơn hàng <span>*</span>
                    </label>
                    <input type="text" name="order_code" id="order_code" class="form-control" value="{{old('order_code')}}" placeholder="">
                </div>
                @if ($error = $errors->first('order_code'))
                <div class="alert alert-danger text-center">
                    {{$error}}
                </div>
                @endif
                <div class="submit">
                    <button type="submit" class="btn btn-colored-default">Tiếp tục</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- Log In Section End -->




@endsection
