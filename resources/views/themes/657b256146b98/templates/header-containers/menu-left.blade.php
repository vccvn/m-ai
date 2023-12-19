<div class="menu-left">
    <div class="brand-logo">
        <a href="{{route('home')}}">
            <img src="{{$logo}}" class="img-fluid blur-up lazyload" alt="{{$siteinfo->site_name}}">
        </a>
        </div>
    <div class="category-menu">
        <div class="category-dropdown">
            <div class="close-btn d-xl-none">
                Danh mục
                <span class="back-category"><i class="fa fa-angle-left"></i>
                </span>
            </div>
            {!! 
            
                $helper->getCustomMenu('category', 3, [
                    'class' => 'category-menu'
                ])
                // thêm một action khi lặp qua từng menu item
                ->addAction(function($item, $link, $sub){
                    // $item->removeClass();
                    $link->removeClass();
                    $level = $item->getSonLevel();
                    $SubItems = ($hasSub = $item->hasSubMenu()) ? $item->sub->count() : 0;
                    if(!$item->level){
                        // if(in_array($style, [3, 4])){

                            if($item->isActive()){
                                $item->addClass('current-menu-item');
                            }
                            if($hasSub){
                                $item->addClass('submenu');

                                if($level>1 && $SubItems > 0){
                                    $link->href = 'javascript:void(0)';
                                    $item->sub->removeClass()->addClass('category-mega-menu');
                                    $item->sub->prepend('
                                            <li>
                                                <div class="row">
                                    ')->apprnd('
                                                </div>
                                            </li>
                                            ');

                                }
                            }
                        // }
                            
                    }
                    else{
                        if ($item->level == 1 && $item->parent->sub->count() > 0 && $item->parent->getSonLevel() > 1) {                                                        
                            $item->prepend(
                                '<div class="col-xl-3">
                                    <div class="category-childmenu">'
                            )->append('
                                    </div>
                                </div>'
                            );
                            $link->before('<div class="title-category">')->after('</div>')->tagName = 'h4';
                        }
                                
                    }                                        
                })
            !!}
        </div>
    </div>
</div>