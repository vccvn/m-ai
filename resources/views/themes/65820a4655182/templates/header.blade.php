@php
    $header = $options->theme->header;
    $desktop_logo = theme_asset('img/logo/logo2.png');
    $mobile_logo = theme_asset('img/logo/logo1.png');

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
        <a href="index.html" class="logo">
            <img src="{{ $mobile_logo }}" class="logo-one" alt="Logo">
            <img src="{{ $desktop_logo }}" class="logo-two" alt="Logo">
        </a>
    </div>

    <div class="main-nav nav-bar-two">
        <div class="container-fluid">
            <nav class="container-max-2 navbar navbar-expand-md navbar-light ">
                <a class="navbar-brand" href="index.html">
                    <img src="{{ theme_asset('img/logo/logo2.png') }}" alt="Logo">
                </a>
                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    <ul class="navbar-nav m-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                Home
                                <i class="bx bx-plus"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="index.html" class="nav-link">
                                        SEO Agency
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index-2.html" class="nav-link active">
                                        AI Startup
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index-3.html" class="nav-link">
                                        Machine Learning
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="about.html" class="nav-link">
                                About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Pages
                                <i class="bx bx-plus"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="team.html" class="nav-link">
                                        Team
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pricing.html" class="nav-link">
                                        Pricing Table
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="faq.html" class="nav-link">
                                        FAQ
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        Courses
                                        <i class="bx bx-plus"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item">
                                            <a href="courses.html" class="nav-link">
                                                Courses
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="course-details.html" class="nav-link">
                                                Course Details
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="testimonials.html" class="nav-link">
                                        Testimonials
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        Case study
                                        <i class="bx bx-plus"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item">
                                            <a href="case-study.html" class="nav-link">
                                                Case study
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="case-details.html" class="nav-link">
                                                Case study Details
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="404.html" class="nav-link">
                                        404 page
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="sign-in.html" class="nav-link">
                                        Sign In
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="sign-up.html" class="nav-link">
                                        Sign Up
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="terms-condition.html" class="nav-link">
                                        Terms & Conditions
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="privacy-policy.html" class="nav-link">
                                        Privacy Policy
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="coming-soon.html" class="nav-link">
                                        Coming Soon
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Services
                                <i class="bx bx-plus"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="services.html" class="nav-link">
                                        Services
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="service-details.html" class="nav-link">
                                        Service Details
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Blog
                                <i class="bx bx-plus"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="blog.html" class="nav-link">
                                        Blog
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="blog-details.html" class="nav-link">
                                        Blog Details
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="contact.html" class="nav-link">
                                Contact
                            </a>
                        </li>
                    </ul>

                    <div class="side-nav d-in-line align-items-center">
                        @if ($header->show_search_button)
                            <div class="side-item">
                                <div class="search-box">
                                    <i class="flaticon-loupe"></i>
                                </div>
                            </div>
                        @endif
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
                                    <a href="{{ $header->addon_button_link }}" class="nav-menu-btn">
                                        {{ $header->addon_button_text }}
                                        <i class="bx bx-plus"></i>
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
            <div class="dot-menu">
                <div class="circle-inner">
                    <div class="circle circle-one"></div>
                    <div class="circle circle-two"></div>
                    <div class="circle circle-three"></div>
                </div>
            </div>
            <div class="container">
                <div class="side-nav-inner">
                    <div class="side-nav justify-content-center  align-items-center">
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
                        <div class="side-item">
                            <div class="nav-add-btn">
                                <a href="#" class="nav-menu-btn border-radius">
                                    Contact us
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                        </div>
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
