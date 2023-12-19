
$(function () {
    $('.wtf-slide').each(function (index, elem) {
        var $el = $(elem),
            $tabs = $el.find(".style-tabs"),
            activatedSlides = {},
            activeTab = function (tab) {
                var $currentTab = $tabs.find('.style-tab[data-tab-index="' + tab + '"]');
                $tabs.find(".style-tab").removeClass("active");
                $currentTab.addClass("active");
                if (!activatedSlides[tab]) {
                    var html = $currentTab.find(".style-product-slide").html(),
                    html2 = $currentTab.find(".product-list-section").html();
                    
                    $currentTab.find(".style-product-slide").html("<div class='alert text-center'>Đang tải</div>");
                    $currentTab.find(".product-list-section").html("<div class='alert text-center'>Đang tải</div>");
                    setTimeout(function () {
                        $currentTab.find(".style-product-slide").html(html);
                        $currentTab.find(".product-list-section").html(html2);
                        $currentTab.find(".bg-img").parent().addClass("bg-size");
                        $currentTab.find(".bg-img.blur-up").parent().addClass("blur-up lazyload");
                        $currentTab.find(".bg-img").each(function () {
                            var el = $(this),
                                src = el.attr("src"),
                                parent = el.parent();

                            parent.css({
                                "background-image": "url('" + src + "')",
                                "background-size": "cover",
                                "background-position": "center",
                                "background-repeat": "no-repeat",
                                display: "block",
                            });

                            el.hide();

                        });
                        setTimeout(function () {
                            $currentTab.find(".style-product-slide").each(function (i, e) {
                                $(e).slick({
                                    dots: false,
                                    infinite: true,
                                    speed: 500,
                                    arrows: true,
                                    slidesToShow: 4,
                                    slidesToScroll: 2,
                                    prevArrow: '<span class="btn-prev"><i class="fa fa-angle-left"></i></span>',
                                    nextArrow: '<span class="btn-next"><i class="fa fa-angle-right"></i></span>',
                                    responsive: [
                                        {
                                            breakpoint: 1366,
                                            settings: {
                                                slidesToShow: 4,
                                            },
                                        },
                                        {
                                            breakpoint: 1100,
                                            settings: {
                                                slidesToShow: 3,
                                            },
                                        },
                                        {
                                            breakpoint: 992,
                                            settings: {
                                                slidesToShow: 2,
                                            },
                                        },
                                        {
                                            breakpoint: 420,
                                            settings: {
                                                slidesToShow: 1,
                                            },
                                        },
                                    ],
                                })
                            });
    
                            $currentTab.find(".style-thumbnail-slide").each(function (i, e) {
                                $(e).slick({
                                    dots: false,
                                    infinite: true,
                                    speed: 500,
                                    arrows: true,
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    prevArrow: '<span class="btn-prev"><i class="fa fa-angle-left"></i></span>',
                                    nextArrow: '<span class="btn-next"><i class="fa fa-angle-right"></i></span>'
                                })
                            });
                            activatedSlides[tab] = true;
    
                        }, 10);
                    }, 500);
                    
                }
            };
        $el.on("click", ".btn-view-products", function (e) {
            e.preventDefault();
            activeTab($(this).data('tab'));
            return false;
        });
        if ($tabs.length) {
            activeTab(0);
        }

    });


    $(".personal-style-slide").each(function (i, e) {
        $(e).slick({
            dots: false,
            infinite: true,
            speed: 500,
            arrows: true,
            slidesToShow: 4,
            slidesToScroll: 2,
            prevArrow: '<span class="btn-prev"><i class="fa fa-angle-left"></i></span>',
            nextArrow: '<span class="btn-next"><i class="fa fa-angle-right"></i></span>',
            responsive: [
                {
                    breakpoint: 1366,
                    settings: {
                        slidesToShow: 4,
                    },
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 4,
                    },
                },
                {
                    breakpoint: 456,
                    settings: {
                        slidesToShow: 3,
                    },
                }
            ],
        })
    });

    $(".product-slide").each(function (i, e) {
        $(e).slick({
            dots: false,
            infinite: true,
            speed: 500,
            arrows: true,
            slidesToShow: 4,
            slidesToScroll: 2,
            prevArrow: '<span class="btn-prev"><i class="fa fa-angle-left"></i></span>',
            nextArrow: '<span class="btn-next"><i class="fa fa-angle-right"></i></span>',
            responsive: [
                {
                    breakpoint: 1366,
                    settings: {
                        slidesToShow: 4,
                    },
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 3,
                    },
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                    },
                },
                {
                    breakpoint: 420,
                    settings: {
                        slidesToShow: 1,
                    },
                },
            ],
        })
    });

    $(".product-features-slide").each(function (i, e) {
        $(e).slick({
            dots: false,
            infinite: true,
            speed: 500,
            arrows: false,
            slidesToShow: 2,
            slidesToScroll: 2,
            autoplay: true,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                    },
                },
                {
                    breakpoint: 420,
                    settings: {
                        slidesToShow: 1,
                    },
                },
            ],
        })
    });

    $(".product-thumbnail-slide").each(function (i, e) {
        $(e).slick({
            dots: false,
            infinite: true,
            speed: 500,
            arrows: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<span class="btn-prev"><i class="fa fa-angle-left"></i></span>',
            nextArrow: '<span class="btn-next"><i class="fa fa-angle-right"></i></span>'
        })
    });


    $(document).on("click", ".section-personal-styles .style-list .btn-view-products", function (e) {
        e.preventDefault();
        var wrapper = $(this).closest('.section-personal-styles');
        wrapper.find(".style-tab").removeClass("active");
        wrapper.find('.style-tab[data-tab-index="' + $(this).data('tab') + '"]').addClass("active");
        return false;
    });

    $(".slide-ovh").slick({
        dots: false,
        infinite: true,
        speed: 500,
        arrows: false,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 1630,
            settings: {
                slidesToShow: 4,
            },
        },
        {
            breakpoint: 1366,
            settings: {
                slidesToShow: 4,
            },
        },
        {
            breakpoint: 992,
            settings: {
                slidesToShow: 3,
            },
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
            },
        }
        ],
    });

    $(document).on("click", ".policy-drop-list .drop-list-item .item-header", function (e) {
        e.preventDefault();
        var wrapper = $(this).closest('.drop-list-item');
        if(wrapper.hasClass('expanded')){
            wrapper.removeClass('expanded');
        }else{
            wrapper.addClass('expanded');
        }
    });


})

$(function () {
    var second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;
    var $countdown = $('.timer.countdown');
    $countdown.each(function (i, e) {
        var $e = $(e),
            timestamp = $e.data("timestamp"),
            dateformat = $e.data('date-format'),
            timeformat = $e.data('time-format'),
            delimiter = $e.data('delimiter'),
            countDown = new Date(timestamp).getTime(),
            $d = $e.find('.date'),
            $t = $e.find('.time'),
            $l = $e.find(".delimiter"),
            $s = $e.find('.show'),
            $days = $e.find('.days'),
            $hours = $e.find('.hours'),
            $minutes = $e.find('.minutes'),
            $seconds = $e.find('.seconds'),
            dateTime = {},
            check = (function () {
                if (!$d.length && !$t.length && !$s.length && !$hours.length && !$minutes.length && !$seconds.length) {
                    $e.html("<div class='show'></div>");
                    $s = $e.find('.show');
                }
            }()),
            x = setInterval(function () {
                var now = new Date().getTime(),
                    distance = countDown - now;
                if (distance <= 0) {
                    clearInterval(x);
                    return false;
                }
                var _d = Math.floor(distance / day), _h = Math.floor((distance % day) / hour), _i = Math.floor((distance % hour) / minute), _s = Math.floor((distance % minute) / second);
                if (_h < 10) _h = "0" + String(_h);
                if (_i < 10) _i = "0" + String(_i);
                if (_s < 10) _s = "0" + String(_s);

                dateTime["%d"] = _d;
                dateTime["%h"] = _h;
                dateTime["%i"] = _i;
                dateTime["%s"] = _s;
                if ($d.length) {
                    if (!_d) {
                        $d.addClass('d-none');
                        $l.addClass('d-none');
                        $days.addClass('d-none')
                    } else {
                        $d.html(App.str.replace(dateformat, dateTime));
                    }
                }
                if ($t.length) {
                    $t.html(App.str.replace(timeformat, dateTime));
                }
                if ($days.length) {
                    $days.html(_d);
                }
                if ($hours.length) {
                    $hours.html(_h);
                }
                if ($minutes.length) {
                    $minutes.html(_i);
                }
                if ($seconds.length) {
                    $seconds.html(_s);
                }
                if ($s.length) {
                    var fm = (_d > 0 ? dateformat + delimiter : '') + timeformat;
                    $s.html(App.str.replace(fm, dateTime));
                }




            }, second);
    })
})