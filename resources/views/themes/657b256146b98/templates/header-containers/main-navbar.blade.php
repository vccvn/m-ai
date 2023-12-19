<nav>
    <div class="main-navbar">
        <div id="mainnav">

            {!!
                // các tham số
                // 1: Vị trí menu hoặc tham số lấy menu
                // ví dụ string: 'primary' // lấy ra menu đứng đầu có vị trí là primary
                // hoặc mảng tham số : ['id' => $menuID , ...]
                // 2: số cấp
                // 3: thuộc tính html của menu
                $helper->getCustomMenu('primary', 3, [
                    'class' => 'nav-menu'
                ])
                // thêm một action khi lặp qua từng menu item
                ->addAction(function($item, $link, $sub){
                    // $item->removeClass();
                    $link->removeClass();
                    $level = $item->getSonLevel();
                    $SubItems = ($hasSub = $item->hasSubMenu()) ? $item->sub->count() : 0;
                    if(!$item->level){
                        $link->addClass('nav-link menu-title');
                        if($item->isActive()){
                            $item->addClass('current-menu-item');
                            $item->removeClass('active');
                        }
                        if($hasSub){
                            $item->addClass('dropdown');
                            if($level>=2 && $SubItems > 0){
                                $item->addClass('mega-menu');
                                // $link->before('<div class="menu-title">')->after('</div>');
                                // $link->href = 'javascript:void(0)';

                                // $link->prepend('<div class="gradient-title">')->append('</div>');
                                $item->sub->before('
                                    <div class="mega-menu-container menu-content '.(($item->isLast() && $item->index >= 4 && $SubItems >= 5? 'mega-right' : ($SubItems >= 4 || ($item->index <2 && $SubItems >= 2)? 'mega-left' : ''))).'">
                                        <div class="container">
                                ')->after('</div></div>')->addClass('row')->tagName = 'div';

                            }else{
                                $item->sub->addClass('nav-submenu menu-content');
                            }
                            if($item->hasClass('personal-style-item')){
                                if(count($personalStyles = get_personal_style_sets(['@limit' => 10]))){
                                    $a = '';
                                    foreach ($personalStyles as $style) {
                                        $a .= '<li><a href="'.route('web.style-sets.detail', ['id' => $style->id]).'"> <img src="'.theme_asset('images/gif/new.gif').'" />'.$style->name.'</a></li>';
                                    }
                                    $item->sub->prepend($a);
                                }
                            }
                        }
                    }
                    else{
                        if ($item->level == 1 && $item->parent->sub->count() > 0 && $item->parent->getSonLevel() > 1) {

                            $item->prepend('<div class="link-section">')->append('</div>')->addClass('col mega-box')->tagName = 'div';

                            $link->before('<div class="submenu-title"><h5>')->after('</h5></div>');

                            if($hasSub) $item->sub->before('<div class="submenu-content opensubmegamenu">')->after('</div>')->addClass('list');



                        }

                    }
                })->prepend('
                <li class="back-btn d-xl-none">
                    <div class="close-btn">
                        Menu
                        <span class="mobile-back"><i class="fa fa-times"></i>
                        </span>
                    </div>
                </li>
                ')->append('<li class="mobile-login">
                    <div class="text-center my-auto" style="max-width:40%; margin: 0 auto">
                        <a class="btn btn-theme btn-colored-default" ' .( ($user = Auth::user())?'href="'.route('web.account').'">Tài khoản' :'href="'.route('web.account.login').'">Đăng nhập' ). '</a>
                    </div>
                </li>')
            !!}
        </div>
    </div>
</nav>
