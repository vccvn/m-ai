@php
    $user = Auth::user();
    $exams = get_public_exams();
@endphp
<!-- Header Start  -->
<div id="header" class="header section">
    <div class="container">

        <!-- Header Wrapper Start  -->
        <div class="header-wrapper">


            <!-- Header Logo Start -->
            <div class="header-logo">
                <a href="/">
                    <img src="{{ $siteinfo->logo(frontend_asset('images/gomee-logo.png')) }}" alt="">
                    <span class="slogan">{{ $siteinfo->slogan('Bộ Công an - Cục Đào tạo') }}</span>
                </a>

            </div>
            <!-- Header Logo End -->

            <!-- Header Menu Start -->
            <div class="header-menu d-none d-lg-block mr-auto">
                {!!
                    $helper->getCustomMenu('primary', 2, ['class' => 'main-menu'])->addAction(function($item, $link, $sub = null) use($exams){
                        $hasExamItem = $item->hasClass('exam-menu-item');
                        $item->removeClass('menu-item');
                        $link->removeClass('menu-link');
                        if($item->hasSubMenu()){
                            $item->sub->addClass('sub-menu');
                        }
                        if($hasExamItem){
                            if($item->hasSubMenu() && $item->sub){
                                $item->sub->remove();
                            }

                            if(count($exams)){
                                $subMenu = '<ul class="sub-menu">';
                                    foreach ($exams as $exam){
                                        $subMenu .= '
                                        <li><a href="' . route('web.exams.detail', ['slug' => $exam->slug]) . '">'. $exam->name . '</a></li>
                                        ';
                                    }


                                $subMenu .= '</ul>';
                                $item->append($subMenu);
                            }
                        }
                    })

                !!}
            </div>
            <!-- Header Menu End -->

            <!-- Header Meta Start -->
            <div class="header-meta">

                {{-- <div class="header-search d-none d-lg-block">
                            <form action="#">
                                <input type="text" placeholder="Search Courses">
                                <button><i class="flaticon-loupe"></i></button>
                            </form>
                        </div>
                         --}}
                <div class="header-login d-none d-lg-flex">

                    @if ($user)
                        <a href="{{ route('web.account') }}" class="link"> {{$user->first_name??$user->name }}</a>
                        <a href="{{ route('web.account.logout') }}" class="link">Đăng xuất</a>
                    @else
                        <a class="header-btn" href="{{ route('web.account.register') }}"><span>Đăng ký</span></a>
                        <a class="header-btn" href="{{ route('web.account.login') }}"><span>Đăng nhập</span></a>
                    @endif

                </div>

            </div>
            <!-- Header Meta End -->

            <div class="header-toggle d-lg-none">
                <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

        </div>
        <!-- Header Wrapper End -->

    </div>
</div>
<!-- Header End -->



<!-- Offcanvas Start -->
<div class="offcanvas offcanvas-end" id="offcanvasMenu">

    <div class="offcanvas-header">
        <!-- Offcanvas Logo Start -->
        {{-- <div class="offcanvas-logo">
            <a href="/"><img src="{{ $siteinfo->logo(frontend_asset('images/gomee-logo.png')) }}" alt=""></a>
        </div> --}}
        <!-- Offcanvas Logo End -->
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <div class="offcanvas-menu">
            {!!
                $helper->getCustomMenu(['slug' => 'menu-mobile'], 2, ['class' => 'main-menu'])->addAction(function($item, $link, $sub = null) use($exams){
                    $hasExamItem = $item->hasClass('exam-menu-item');
                    $item->removeClass('menu-item');
                    $link->removeClass('menu-link');
                    if($item->hasSubMenu()){
                        $item->sub->addClass('sub-menu');
                    }
                    if($hasExamItem){
                        if($item->hasSubMenu() && $item->sub){
                            $item->sub->remove();
                        }

                        if(count($exams)){
                            $subMenu = '<ul class="sub-menu">';
                                foreach ($exams as $exam){
                                    $subMenu .= '
                                    <li><a href="' . route('web.exams.detail', ['slug' => $exam->slug]) . '">'. $exam->name . '</a></li>
                                    ';
                                }


                            $subMenu .= '</ul>';
                            $item->append($subMenu);
                        }
                    }
                })
                ->append(
                    $user ? '
                    <li>
                        <a href="'. route('web.account') .'" class="link">'.($user->first_name??$user->name).'</a>
                    </li>
                    <li>
                        <a href="'. route('web.account.logout') .'" class="link">Đăng xuất</a>
                    </li>
                    ' : '
                    <li>
                        <a class="link" href="'. route('web.account.login') .'"> Đăng nhập</a>
                    </li>
                    <li>
                        <a class="link" href="'. route('web.account.register') .'">Đăng ký</a>
                    </li>
                    '
                )

            !!}

        </div>
    </div>

</div>
<!-- Offcanvas End -->
