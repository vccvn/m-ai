@php
    $color = '#e22454';
    if($general = $options->theme->general){
        $color = $general->theme_primary_color($color);
    }
@endphp

    <!-- tap to top Section Start -->
    <div class="tap-to-top">
        <a href="#home">
            <i class="fas fa-chevron-up"></i>
        </a>
    </div>
    <!-- tap to top Section End -->

    <div class="bg-overlay"></div>

    <!-- latest jquery-->
    <script src="{{theme_asset('js/jquery-3.5.1.min.js')}}"></script>

    <!-- Bootstrap js -->
    <script src="{{theme_asset('js/bootstrap/bootstrap.bundle.min.js')}}"></script>

    <!-- feather icon js -->
    <script src="{{theme_asset('js/feather/feather.min.js')}}"></script>

    <!-- lazyload js -->
    <script src="{{theme_asset('js/lazysizes.min.js')}}"></script>

    <!-- Add To Home js -->
    {{-- <script src="{{theme_asset('js/pwa.js')}}"></script> --}}

    <!-- Slick js -->
    <script src="{{theme_asset('js/slick/slick.js')}}"></script>
    <script src="{{theme_asset('js/slick/slick-animation.min.js')}}"></script>
    <script src="{{theme_asset('js/slick/custom_slick.js')}}"></script>

    <!-- notify js -->
    <script src="{{theme_asset('js/bootstrap/bootstrap-notify.min.js')}}"></script>

    <!-- script js -->
    <script src="{{theme_asset('js/script.js')}}"></script>
    <script src="{{theme_asset('js/home-script.js')}}"></script>


    <script src="{{theme_asset('js/custom.js')}}"></script>
    <script src="{{asset('static/plugins/rangeslider/rangeslider.min.js')}}"></script>
    <script src="{{asset('static/features/style-sets/create-form-modal.js')}}"></script>


    <script>

        // @if(session('disableBack'))

        window.navInit = function(){
            App.nav.init({
                disableBack: true,
                urls: {
                    next: "{{session('next')}}"
                }
            })
        }

        // @endif
        //





        window.customCartInit = function () {
            App.cart.init({
                decimal: 0,
                templates: {
                    item: '<div class="cart-item" data-item-id="{$id}">'+
                        '<div class="cart-inner">'+
                            '<div class="cart-top">'+
                                '<div class="thumb">'+
                                    '<a href="{$link}"><img src="{$image}" alt="{$name}"></a>'+
                                '</div>'+
                                '<div class="content">'+
                                    '<a href="{$link}">{$name}</a>'+
                                '</div>'+
                                '<div class="remove-btn">'+
                                    '<a href="#" class="{{parse_classname('remove-cart-item')}}" data-item-id="{$id}"><i class="icofont-close"></i></a>'+
                                '</div>'+
                            '</div>'+
                            '{$attributes}'+
                            '<div class="cart-bottom">'+
                                '<div class="sing-price">{$price}</div>'+
                                '<div class="cart-plus-minus">{$quantity}'+
                                    // '<div class="dec qtybutton">-</div>'+
                                    // '<div class="dec qtybutton">-</div>'+
                                    // '<input type="text" name="quantity[{$id}]" id="qty-{$id}" value="{$quantity}" data-item-id="{$id}" min="1" placeholder="1" class="cart-plus-minus-box {{parse_classname('product-order-quantity', 'quantity', 'item-quantity')}}">'+
                                    // '<div class="inc qtybutton">+</div>'+
                                    // '<div class="inc qtybutton">+</div>'+
                                '</div>'+
                                '<div class="total-price">{$total_price}</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>',

                    attribute: '<div class="sing-price"><strong class="{{parse_classname('attribute-label')}}">{$label}</strong>: <span class="{{parse_classname('attribute-value')}}">{$value}</span></div>'
                },
                tasks: {
                    changeItemAttribute: function(item){
                        wisestyle_update_cart_attribute(item)
                    }
                }
            });
        };
        window.authInit = function(){


            App.auth.init({
                urls: {
                    check: "{{route('web.account.check')}}"
                },
                templates: {
                    account_section: "<i class=\"fa fa-user\"></i> {$name}",
                    link: "<a class=\"dropdown-item\" href=\"{$link}\">{$text}</a>"
                }
            });
            App.auth.check(function(res){
                if(res.status){
                    // gs-account-links

                    function maplinks(links, func) {
                        var linkArr = [];
                        for(var key in links){
                            if(Object.hasOwnProperty.call(links, key)){
                                linkArr.push(func(links[key]));
                            }
                        }
                        return linkArr;
                    }
                    var a = maplinks(res.data.links, function(link){return '<li><a href="'+link.link+'">'+link.text+'</a></li>';}).join('')

                    $('#account-menu-links').html(a);

                    var $btn = $('#account-menu-block .btn-account');
                    $btn.prepend('<span class="avatar-span"><img class="avatar" src="'+res.data.avatar+'" /></span>');
                    $btn.find('span.name-span').html(res.data.name);
                    $btn.removeClass('btn-colored-default');
                    $btn.addClass('btn-outline-default');




                }else{

                    // $('.gs-account-links').html(
                    //     '<a href="{{route('web.account.login')}}">Đăng nhập</a>'+
                    //     '<a href="{{route('web.account.register')}}">Đăng ký</a>'
                    // );
                }
            });



        };
    </script>
