@if ($data->show)
        
@php
add_js_src(theme_asset('components/common/script.js'));

        $hasList = false;
        $args = [];
        $sets = [];
        if($data->list_type=='tagged'){
            $args['id'] = $data->style_sets?$data->style_sets:[0];
        }elseif($data->list_type=='database'){
            $args['@sorttype'] = $data->sorttype;
            $args['@limit'] = $data->limit && $data->limit > 0? $data->limit:4;
        }
        if($args){
            $args['@withProducts'] = [
                '@withPromoAvailable' => true
            ];
            $sets = $helper->getStyleSets($args);
        }
        add_js_src(theme_asset('components/home/style-sets/script.js'));
    @endphp
    @if ($data->list_type=='custom' || $args)
            
        <div class="section style-set-section ovxh section-large">
            <div class="container container-max">
                <div class="filter-options section-header">
                    <div class="select-options">
                        <h2 class="section-title">{{$data->title}}</h2>
                    </div>
                    {{-- <div class="grid-options d-sm-inline-block d-none">
                        <a href="#" class="theme-color seemore">Xem thêm <i class="fa fa-angle-right"></i></a>
                    </div> --}}
                </div>
                <div class="product-wrapper style-set-slide product-style-2 ratio_asos d-none d-md-block">
                    @if ($data->list_type == 'custom')
                    {!! $children !!}
                    @elseif (count($sets))

                    @foreach ($sets as $item)
                    <div class="style-item-wrapper">
                        <div class="product-box style-item">
                            <div class="img-wrapper">
                                <div class="main-image">
                                    <a href="{{$item->url}}">
                                        <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="{{$item->name}}">
                                    </a>
                                </div>
                                <div class="right-images">
                                    <div class="item-thumbnails">
                                        @if ($item->products && count($item->products))
                                        @foreach ($item->products as $prod)
                                        <div class="item-image">
                                            <a href="{{$prod->getViewUrl()}}">
                                                <img src="{{$prod->getImage()}}" class="bg-img blur-up lazyload" alt="{{$prod->name}}">
                                            </a>
                                        </div>
                                        @if ($loop->index >= 2)
                                        @break
                                        @endif
                                        @endforeach
                                        @else
    
                                        <div class="item-image">
                                            <a href="javascript:;">
                                                <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                            </a>
                                        </div>
                                        <div class="item-image">
                                            <a href="javascript:;">
                                                <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                            </a>
                                        </div>
                                        <div class="item-image">
                                            <a href="javascript:;">
                                                <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="product-details">
                                <div class="product-name">
                                    <h2><a href="{{$item->url}}">{{$item->name}}</a></h2>
                                </div>
                                <div class="main-price">
                                    <div class="set-price">
                                        <div>
                                            @if ($item->has_discount)
                                        <div class="dscount">
                                            <span class="old-price">
                                                {{$item->priceFormat('total')}} <span class="onsale-label">-{{$item->down_percent}}%</span>
                                            </span>

                                        </div>
                                        <span class="regular-price">{{$item->priceFormat('final')}}</span>

                                        @else
                                        <span class="regular-price">{{$item->priceFormat('final')}}</span>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="btns">
                                        <a href="javascript:;" class="btn btn-outline-default btn-add-cart btn-theme square {{parse_classname('add-combo')}}" data-combo-id="{{$item->id}}">
                                            <img src="{{theme_asset('images/header/cart.png')}}" alt="">
                                        </a>
                                        <a href="javascript:;" class="btn btn-colored-default btn-theme btn-buy-now {{parse_classname('add-combo')}}" data-combo-id="{{$item->id}}" data-redirect="checkout">
                                            Mua Ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                <div class="row g-sm-3 g-3 row-cols-xl-4 row-cols-md-2 row-cols-1 mt-1  product-style-2 ratio_asos d-md-none style-grid">
                    @if ($data->list_type == 'custom')
                    {!! $children !!}
                    @elseif (count($sets))

                    @foreach ($sets as $item)
                    <div class="mt-0 style-item-wrapper">
                        <div class="product-box style-item">
                            <div class="img-wrapper">
                                <div class="main-image">
                                    <a href="</a>">
                                        <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="{{$item->name}}">
                                    </a>
                                </div>
                                <div class="right-images">
                                    @if ($item->products && count($item->products))
                                    @foreach ($item->products as $prod)
                                    <div class="item-image">
                                        <a href="{{$prod->getViewUrl()}}">
                                            <img src="{{$prod->getImage()}}" class="bg-img blur-up lazyload" alt="{{$prod->name}}">
                                        </a>
                                    </div>
                                    @if ($loop->index >= 2)
                                    @break
                                    @endif
                                    @endforeach
                                    @else

                                    <div class="item-image">
                                        <a href="javascript:;">
                                            <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                        </a>
                                    </div>
                                    <div class="item-image">
                                        <a href="javascript:;">
                                            <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                        </a>
                                    </div>
                                    <div class="item-image">
                                        <a href="javascript:;">
                                            <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="product-details">
                                <div class="product-name">
                                    <h3><a href="</a>">{{$item->name}}</a></h3>
                                </div>
                                <div class="rating-details">

                                </div>
                                <div class="main-price">
                                    <div class="set-price">
                                        <div>
                                            @if ($item->has_discount)
                                        <div class="dscount">
                                            <span class="old-price">
                                                {{$item->priceFormat('total')}} <span class="onsale-label">-{{$item->down_percent}}%</span>
                                            </span>

                                        </div>
                                        <span class="regular-price">{{$item->priceFormat('discount')}}</span>

                                        @else
                                        <span class="regular-price">{{$item->priceFormat('total')}}</span>
                                        @endif


                                        </div>

                                    </div>

                                    <div class="btns">
                                        <a href="#" class="btn btn-outline-default btn-add-cart btn-theme square">
                                            <i class="far fa-shopping-cart"></i>
                                        </a>
                                        <a href="#" class="btn btn-colored-default btn-theme">
                                            Mua Ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>


                <div class="section-buttons text-center d-sm-none">
                    <a href="javascript:;" class="btn btn-outline-default btn-def-size">Khám thêm</a>
                </div>
            </div>
        </div>




    @endif
@endif