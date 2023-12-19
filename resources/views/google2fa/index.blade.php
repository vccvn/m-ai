<?php
	$args = [

	];
	$request = request();

	if($request->reauthenticating && !$request->next){
		$args = ['next' => route('home')];
	}
	elseif($next = $request->next){
		$args = ['next' => $next];
	}
?>
@extends('admin._layouts.auth', [
    '_base' => 'admin.',
    '_template' => 'admin._templates.'

    ])

{{-- khai báo title --}}
@section('title', $page_title = isset($title)?$title:"Đăng nhập hệ thống")

@section('content')


			<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2" id="m_login" style="background-image: url({{asset('static/manager/assets/app/media/img//bg/bg-3.jpg')}});">
				<div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
					<div class="m-login__container">
						<div class="m-login__logo">
							<a href="#">
								<img src="{{siteinfo('admin_login_logo', asset('static/images/logos/login_logo.png'))}}">
							</a>
						</div>
						<div class="m-login__signin">
							<div class="m-login__head">
								<h3 class="m-login__title">Xác thực tài khoản</h3>
							</div>
                            <form class="m-login__form m-form" action="{{ route('2fa', $args) }}" method="POST">
								{{ csrf_field() }}
								<div class="form-group m-form__group">
									<input class="form-control m-input m-login__form-input--last" id="one_time_password" type="text" class="form-control" name="one_time_password" placeholder="Nhập mã xác thực" required autofocus>
								</div>
								<div class="m-login__form-action">
									<button id="m_login_signin_submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--success">Xác thực</button>
								</div>
							</form>
						</div>
						<div class="m-login__account">
							<span class="m-login__account-msg">
								Không phải {{auth()->user()->email}}
							</span>&nbsp;&nbsp;
							<a href="{{route('logout')}}" id="m_login_signup" class="m-link m-link--light m-login__account-link">Đăng xuất</a>
						</div>
					</div>
				</div>
            </div>

@endsection

{{--
@section('js')
	<script src="{{asset('static/manager/js/login.js')}}" type="text/javascript"></script>

@endsection --}}
