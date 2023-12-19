@php
    $hasPromo = $product->hasPromo();
    $reviews = $product->getReviewData();
    $hasOption = $product->hasOption();
    $u = $product->getViewUrl();
    $style_attrs = $product->style_attrs ?? [];
@endphp

                    <div class="product-box product-item {{ isset($item_class) ? $item_class : '' }}">
                        <form action="{{ route('web.orders.add-to-cart') }}" class="product-item-form {{ parse_classname('product-item-form') }}" data-product-id="{{ $product->id }}">
                            @csrf
                            @if ($product->options)
                                @foreach ($product->options as $opt)
                                    @if (!in_array($opt->name, ['color', 'size']))
                                        @php
                                            $def = null;
                                            if ($opt->values) {
                                                foreach ($opt->values as $av) {
                                                    if ($av->is_default) {
                                                        $def = $av->value_id;
                                                    }
                                                }
                                                if (!$def) {
                                                    $i = 0;
                                                    foreach ($opt->values as $av) {
                                                        if ($i == 0) {
                                                            $def = $av->value_id;
                                                        }
                                                        $i++;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <input type="hidden" name="attrs[{{ $opt->name }}]" value="{{ $def }}">
                                    @endif
                                @endforeach
                            @endif
                            <div class="prod-thumbnail">
                                <div class="img-wrapper {{ isset($use_thubnail_slide) && $use_thubnail_slide ? (isset($thumbnail_class) && $thumbnail_class ? $thumbnail_class : 'product-thumbnail-slide') : '' }}">

                                    <a href="{{ $u }}">
                                        <img src="{{ $product->getImage() }}" class="bg-img blur-up lazyload" alt="{{ $product->name }}">
                                    </a>
                                    @if (isset($use_thubnail_slide) && $use_thubnail_slide && $product->gallery && count($product->gallery))
                                        @foreach ($product->gallery as $item)
                                            <a href="{{ $u }}">
                                                <img src="{{ $item->url }}" class="bg-img blur-up lazyload" alt="{{ $product->name }}">
                                            </a>
                                        @endforeach
                                    @endif

                                </div>

                                @if (($sizeAttr = $product->getOrderOption('size')) && $sizeAttr->values)
                                    @php
                                        $defVal = null;
                                        foreach ($sizeAttr->values as $av) {
                                            if (in_array($av->value_id, $style_attrs)) {
                                                $defVal = $av->value_id;
                                            }
                                        }
                                    @endphp
                                    <div class="hover-product-attribute-sizes">
                                        @foreach ($sizeAttr->values as $av)
                                            {{-- <span class="size-item" title="{{$av->text}}">{{$av->text}}</span> --}}
                                            <div class="size-item">
                                                <input type="radio" name="attrs[{{ $sizeAttr->name }}]" id="product-item-attr-{{ $product->id }}-{{ $sizeAttr->attribute_id }}-{{ $av->value_id }}" value="{{ $av->value_id }}" @checked(($defVal && $av->value_id == $defVal) || (!$defVal && ($av->is_default || (!$sizeAttr->default && $loop->index == 0))))>
                                                <label for="product-item-attr-{{ $product->id }}-{{ $sizeAttr->attribute_id }}-{{ $av->value_id }}">{{ $av->text }}</label>
                                            </div>
                                        @endforeach

                                    </div>
                                @endif

                            </div>


                            <div class="product-details">
                                <div class="attributes">
                                    @if (($colorAttr = $product->getTypeOrderOption('color')) && $colorAttr->values)
                                        @php
                                            $defVal = null;
                                            foreach ($colorAttr->values as $av) {
                                                if (in_array($av->value_id, $style_attrs)) {
                                                    $defVal = $av->value_id;
                                                }
                                            }
                                        @endphp

                                        <div class="product-attribute-colors">
                                            @foreach ($colorAttr->values as $av)
                                                <div class="color-item">
                                                    <input type="radio" name="attrs[{{ $colorAttr->name }}]" id="product-item-attr-{{ $product->id }}-{{ $colorAttr->attribute_id }}-{{ $av->value_id }}" value="{{ $av->value_id }}" @checked(($defVal && $av->value_id == $defVal) || (!$defVal && ($av->is_default || (!$colorAttr->default && $loop->index == 0))))>
                                                    <label for="product-item-attr-{{ $product->id }}-{{ $colorAttr->attribute_id }}-{{ $av->value_id }}"style="background-color: {{ $av->advance_value }}" title="{{ $av->text }}"></label>
                                                </div>
                                            @endforeach

                                        </div>
                                    @else
                                        <div class="product-attribute-colors">
                                            <span class="color-item" style="background-color: #fff" title=""></span>
                                        </div>

                                    @endif
                                </div>
                                <div class="product-name">
                                    <h2><a href="{{ $u }}">{{ $product->name }}</a></h2>
                                </div>
                                <div class="product-price {{ $hasPromo ? 'has-promo' : '' }}">
                                    @php
                                    $downPercent = $product->getDownPercent();
                                    $listPrice = $product->priceFormat('list');
                                    $finalPrice = $style_attrs ? get_currency_format($product->checkPrice($product->style_attrs)) : $product->priceFormat('final');
                                @endphp

                                    <div class="mobile d-sm-none">
                                        @if ($product->price_status > -1)

                                            @if ($hasPromo)
                                                <span class="old-price">
                                                    {{ $listPrice }}
                                                </span>
                                                <span class="onsale-label mobile">-{{ $downPercent }}%</span>
                                            @endif
                                            <span class="regular-price {{ $hasPromo ? 'd-block' : '' }}">{{ $finalPrice }}</span>
                                        @else
                                        <span class="regular-price">
                                            liên hệ: <a href="tel:{{$p = get_contact_phone()}}" class="phone-link">{{$p}}</a>
                                        </span>
                                        @endif


                                    </div>
                                    <div class="desktop d-none d-sm-flex">
                                        @if ($product->price_status > -1)
                                            @if ($hasPromo)
                                                <span class="old-price">
                                                    {{ $listPrice }}
                                                </span>
                                            @endif
                                            <div class="regular-group">
                                                <span class="regular-price {{ $hasPromo ? 'mr-sm-12' : '' }}">{{ $finalPrice }}</span>
                                                @if ($hasPromo && $product->price_status > -1)
                                                    <span class="onsale-label desktop">-{{ $downPercent }}%</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="regular-group">
                                                <span class="regular-price {{ $hasPromo ? 'mr-sm-12' : '' }}">
                                                    liên hệ: <a href="tel:{{$p = get_contact_phone()}}" class="phone-link">{{$p}}</a>
                                                </span>
                                            </div>
                                        @endif

                                    </div>


                                </div>
                                <div class="rating-details">

                                </div>
                                @if ($ecommerce->allow_place_order && $product->price_status > 0 && $product->status > 0 && $product->available_in_store)
                                    <div class="btns">
                                        <a href="{{ $u }}" class="btn btn-outline-default btn-theme square btn-add-cart {{ parse_classname('add-to-cart') }}" data-product-id="{{ $product->id }}">
                                            <img src="{{ theme_asset('images/header/cart.png') }}" alt="">
                                        </a>
                                        <a href="{{ $u }}" class="btn btn-colored-default btn-theme btn-buy-now {{ parse_classname('add-to-cart') }}" data-product-id="{{ $product->id }}" data-redirect="checkout">
                                            Mua ngay
                                        </a>
                                    </div>
                                @else
                                    <div class="cta">
                                        <a href="{{route('web.contacts.form')}}" class="contact-link btn btn-theme btn-colored-default" style="margin: 0 auto">Liên hệ đặt hàng</a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
