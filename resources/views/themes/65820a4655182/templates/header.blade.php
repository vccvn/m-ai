@php
    $header = $options->theme->header;
    $desktop_logo = ai_startup_asset('img/logo/logo2.png');
    $mobile_logo = ai_startup_asset('img/logo/logo1.png');
    if ($header) {
        $desktop_logo = $header->desktop_logo ?? ($siteinfo->logo ?? $desktop_logo);
        $mobile_logo = $header->mobile_logo ?? ($siteinfo->mobile_logo ?? $mobile_logo);
    } else {
        $desktop_logo = $siteinfo->logo ?? $desktop_logo;
        $mobile_logo = $siteinfo->mobile_logo ?? $mobile_logo;
    }

    $type = $header && $header->style ? $header->style : $__env->yieldContent('header.style', 1);
@endphp

<div class="navbar-area">

    <div class="mobile-nav">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ $mobile_logo }}" class="logo-one" alt="Logo">
            <img src="{{ $desktop_logo }}" class="logo-two" alt="Logo">
        </a>
    </div>

    <div class="main-nav nav-bar-two">
        <div class="container-fluid">
            <nav class="container-max-2 navbar navbar-expand-md navbar-light ">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ $desktop_logo }}" alt="{{ $siteinfo->site_name }}">
                </a>
                <div class="acc-buttons">
                    <a href="{{route('merchant.dashboard')}}" class="acc-btn acc-agent">Đại lý</a>
                    <a href="{{route('web.account')}}" class="acc-btn acc-user">Thành viên</a>

                </div>

                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    {!! // các tham số
                        // 1: Vị trí menu hoặc tham số lấy menu
                        // ví dụ string: 'primary' // lấy ra menu đứng đầu có vị trí là primary
                        // hoặc mảng tham số : ['id' => $menuID , ...]
                        // 2: số cấp
                        // 3: thuộc tính html của menu
                        $helper->getCustomMenu('primary', 3, [
                                'class' => 'navbar-nav m-auto',
                            ])->addAction(function ($item, $link, $sub) {
                                $item->removeClass()->addClass('nav-item');
                                $link->removeClass()->addClass('nav-link');

                                $level = $item->getSonLevel();
                                $subItems = ($hasSub = $item->hasSubMenu()) ? $item->sub->count() : 0;
                                // has-dropdown
                                if ($hasSub) {
                                    $item->addClass('has-sub');
                                    $item->sub->addClass('dropdown-menu');
                                    $link->append('<i class="bx bx-plus"></i>');
                                }
                            })
                            // hế comment
                            //
                            !!}
                    <div class="side-nav d-in-line d-lg-flex align-items-center">
                        @if ($header->show_search_button)
                            <div class="side-item">
                                <div class="search-box">
                                    <i class="flaticon-loupe"></i>
                                </div>
                            </div>
                        @endif
                        <div class="side-item">
                            <div class="user-btn">
                                <a href="{{ route('web.account.info') }}">
                                    <i class="flaticon-contact"></i>
                                </a>
                            </div>
                        </div>

                        @if ($header->show_addon_button)
                            <div class="side-item">
                                <div class="nav-add-btn">
                                    <a href="{{ $header->addon_button_link('/ai') }}" class="nav-menu-btn">
                                        {{-- <i class="bx bx-money"></i> --}}
                                        @php
                                            $text = $header->addon_button_text('100.000đ');
                                            if ($user = auth()->user()) {
                                                $wallet = get_user_wallet($user->id);
                                                if ($wallet) {
                                                    $text = get_price_format($wallet->balance, 'VND');
                                                }
                                            }
                                        @endphp
                                        {{ $text }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <div class="side-nav-responsive">
        <div class="container">
            <div class="acc-buttons">
                <a href="{{route('merchant.dashboard')}}" class="acc-btn acc-agent">Đại lý</a>
                <a href="{{route('web.account')}}" class="acc-btn acc-user">Thành viên</a>

            </div>

            <div class="dot-menu">
                <div class="circle-inner">
                    <div class="circle circle-one"></div>
                    <div class="circle circle-two"></div>
                    <div class="circle circle-three"></div>
                </div>
            </div>
            <div class="container">
                <div class="side-nav-inner">
                    <div class="side-nav justify-content-center align-items-center">
                        <div class="side-item">
                            <div class="search-box">
                                <i class="flaticon-loupe"></i>
                            </div>
                        </div>
                        <div class="side-item">
                            <div class="user-btn">
                                <a href="#">
                                    <i class="flaticon-contact"></i>
                                </a>
                            </div>
                        </div>

                        @if ($header->show_addon_button)
                            <div class="side-item">
                                <div class="nav-add-btn">
                                    <a href="{{ $header->addon_button_link('/ai') }}" class="nav-menu-btn border-radius">
                                        @php
                                            $text = $header->addon_button_text('100.000đ');
                                            if ($user = auth()->user()) {
                                                $wallet = get_user_wallet($user->id);
                                                if ($wallet) {
                                                    $text = get_price_format($wallet->balance, 'VND');
                                                }
                                            }
                                        @endphp
                                        {{ $text }}
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="search-overlay">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="search-layer"></div>
            <div class="search-layer"></div>
            <div class="search-layer"></div>
            <div class="search-close">
                <span class="search-close-line"></span>
                <span class="search-close-line"></span>
            </div>
            <div class="search-form">
                <form>
                    <input type="text" class="input-search" placeholder="Search here...">
                    <button type="submit"><i class="flaticon-loupe"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
