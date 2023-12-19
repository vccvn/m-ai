<div class="menu-right">
    <ul>
        <li>
            <div class="search-box d-xl-none primary-color mobile-btn">
                <img src="{{theme_asset('images/header/search.png')}}" alt="">
            </div>
            <div class="search-group d-none d-xl-block">
                <form action="{{route('web.products')}}" method="get">
                    <div class="input-group">
                        <i class="icon fa fa-search  primary-color"></i>
                        <input type="text" name="keyword" value="{{$request->keyword}}" class="form-control search-input" placeholder="Tìm kiếm">

                    </div>
                </form>
            </div>
        </li>
        <li class="onhover-dropdown cart-dropdown">

            <a href="{{route('web.orders.cart')}}" class="btn btn-outline-default btn-spacing btn-cart d-none d-xl-flex header-btn-cart">

                <img src="{{theme_asset('images/header/cart.png')}}" alt="">
                <span class="{{parse_classname('cart-quantity')}}">0</span>
            </a>
            <a href="{{route('web.orders.cart')}}" class="btn-cart cart-icon d-xl-none primary-color mobile-btn header-btn-cart">

                <img src="{{theme_asset('images/header/cart.png')}}" alt="">
                <span class="{{parse_classname('cart-quantity')}}">0</span>

            </a>
        </li>
        <li class="onhover-dropdown df-lg-none" id="account-menu-block">
            @php
                $user = $request->user();
            @endphp
            <a class="btn btn-colored-default d-none d-lg-flex btn-spacing btn-account" href="{{route('web.account')}}">
                <span class="name-span">@if ($user)
                    {{$user->first_name??($user->name??$user->username)}}
                    @else
                    Tài khoản
                @endif</span>
            </a>
            <div class="onhover-div profile-dropdown">
                <ul id="account-menu-links">
                    @if ($user)
                    <li>
                        <a href="{{route('web.account')}}" class="d-block">Trang cá nhân</a>
                    </li>
                    <li>
                        <a href="{{route('web.account.logout')}}" class="d-block">Đăng xuất</a>
                    </li>
                    @else
                    <li>
                        <a href="{{route('web.account.login')}}" class="d-block">Đăng nhập</a>
                    </li>
                    <li>
                        <a href="{{route('web.account.register')}}" class="d-block">Đăng ký</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        <li class="d-xl-none">
            <div class="toggle-nav mobile-btn">
                <img src="{{theme_asset('images/header/menu.png')}}" alt="">
            </div>
        </li>

    </ul>
</div>
