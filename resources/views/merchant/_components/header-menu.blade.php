@php
    $webType = get_web_type();

    $user = auth()->user();
    $agent = get_agent_account($user->id);
    $wallet =get_user_wallet($user->id);
@endphp

<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
    <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
        <li class="m-menu__item  m-menu__item--rel">
            <a href="#" class="m-menu__link">
                {{-- <i class="m-menu__link-icon fa fa-home"></i> --}}
                <span class="m-menu__link-text">{{$user->name}} {{$agent && $agent->policy?' [ ' . $agent->policy->name . ']' :''}}</span>
            </a>
        </li>
        <li class="m-menu__item  m-menu__item--rel">
            <a href="#" class="m-menu__link">
                {{-- <i class="m-menu__link-icon fa fa-home"></i> --}}
                <span class="m-menu__link-text">Hoa hồng: {{get_price_format($wallet->balance, 'VND')}}</span>
            </a>
        </li>
        {{-- <li class="m-menu__item  m-menu__item--rel">
            <a href="#" class="m-menu__link">
                <span class="m-menu__link-text">Mục tiêu: {{$agent?(get_price_format($agent->revenue, 'VND') . ($agent->policy?' / ' . get_price_format($agent->policy->revenue_target, 'VND') :'')):''}}</span>
            </a>
        </li> --}}

            <li class="m-menu__item">
                <a href="/ai" target="_blank" class="m-menu__link">
                    <i class="m-menu__link-icon fa fa-home"></i>
                    <span class="m-menu__link-text">Chat</span>
                </a>
            </li>
    </ul>
</div>
