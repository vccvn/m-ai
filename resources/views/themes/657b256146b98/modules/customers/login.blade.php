@extends($_layout.'master')
@section('page_type', 'my-account')
@section('title', 'Đăng nhập quản lý đơn hàng')
@include($_lib.'register-meta')

@section('content')


<div class="login-section">
    <div class="materialContainer">
        <form class="{{parse_classname('customer-login-form')}}" method="POST">
            @csrf
            <div class="box">
                <div class="login-title">
                    <h2>Đăng nhập khách hàng</h2>
                </div>
                <div class="input">
                    <input type="text" name="contact" value="{{old('contact')}}" id="contact" required="" placeholder="Nhập email hoặc SĐT">
                    <span class="spin"></span>
                    <div class="valid-feedback">
                        Vui lòng nhập email
                    </div>
                </div>
        
                @if ($errors->has('contact'))
                    <div class="error has-error">
                        {{$errors->first('contact')}}
                    </div>
                @endif
                
                @if ($error = session('error'))
                    <span class="error">{{$error}}</span>
                @endif


                <div class="button login">
                    <button type="submit">
                        <span>Tiếp tục</span>
                        <i class="fa fa-check"></i>
                    </button>
                </div>
                <p><a href="{{route('home')}}" class="theme-color"><i class="fa fa-arrow-single-left"></i> Về trang chủ</a></p>
        
            </div>
        </form>
        
    </div>
</div>

@endsection