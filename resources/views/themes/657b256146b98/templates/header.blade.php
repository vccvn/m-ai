
@php
$header = $options->theme->header;
if($header && ($header->logo_light || $header->logo_dark)){
    $logo = $header->logo($siteinfo->logo(theme_asset('images/logo.png')));
    
}else{
    $logo = $siteinfo->logo(theme_asset('images/logo.png'));
}

$type = $header && $header->style ? $header->style : $__env->yieldContent('header.style', 1);
@endphp

<header class="header-style-2" id="home">
    <div class="main-header navbar-searchbar">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-menu">
                        
                        @include($_template.'header-containers.menu-left')
                    
                        @include($_template.'header-containers.main-navbar')
                    
                        @include($_template.'header-containers.menu-right')
                    
                    </div>
                </div>
            </div>
        </div>
        <div class="search-wrapper d-xl-none">
            @include($_template.'header-containers.search-full')
        </div>
        
    </div>
</header>