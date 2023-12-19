@extends($_layout . 'master')
@section('title', $page_title)
@include($_lib . 'register-meta')

@section('content')


    <!-- Box contents Cart -->
    <section class="box-cart min-80vh">
        <div class="container">

            @if ($cart && $cart->details && count($cart->details))

                <h3 class="title-cart">{{ $page_title }}</h3>
                @if ($e = session('warning_message'))
                    <div class="alert alert-warning text-center">{{ $e }}</div>
                @endif
                <div class="row">
                    <div class="col-md-7">
                        <form action="{{ route('web.orders.checkout') }}" method="POST" class="form form--cart {{ parse_classname('checkout-form', 'cart-form', 'cart-section') }}" data-cart-type="{{ $cart->type }}" data-cart-id="{{ $cart->id }}">
                            <input type="hidden" name="cart_type" value="{{ $cart->type }}">
                            @csrf
                            <div class="production">
                                <ul class="box-policy">
                                    <li>
                                        <img src="{{ theme_asset('images/custom/cart/icon-delivery-truck.png') }}" alt="icon-delivery-truck" srcset="" />
                                        <span>Miễn phí giao hàng</span>
                                    </li>
                                    <li>
                                        <img src="{{ theme_asset('images/custom/cart/icon-hand-cart.png') }}" alt="icon-hand-cart" srcset="" />
                                        <span>Đổi trả trong vòng 15 ngày</span>
                                    </li>
                                </ul>

                                <div class="card">
                                    <div class="card-body">
                                        <img src="{{ theme_asset('images/custom/cart/icon-sale.png') }}" alt="icon-sale" />
                                        <span>Mua thêm 151.000đ để nhận FREESHIP và 1 voucher <br />
                                            200k cho lần mua tiếp theo</span>
                                        <a href="#">Xem thêm</a>
                                    </div>
                                </div>

                                <div class="prod-cart">
                                    <h3 class="title">Sản phẩm</h3>

                                    @foreach ($cart->details as $item)
                                        <div class="product-box row {{ parse_classname('cart-item', 'cart-item-' . $item->id) }}" id="cart-item-{{ $item->id }}">
                                            <div class="img-wrapper col-lg-4 col-md-4 t-img-wrapper">
                                                <a href="{{ $item->link }}">
                                                    <img src="{{ $item->image }}" alt="{{ $item->product_name }}" style="width: 100%">
                                                </a>
                                            </div>
                                            <div class="product-details col-lg-8 col-md-8 mt-md-0">
                                                <div class="row">
                                                    <div class="cart-item-info-left">
                                                        <p class="prod_name">
                                                            <a href="{{ $item->link }}">{{ $item->product_name }}</a>
                                                        </p>
                                                        <p class="material_size d-none d-md-block">
                                                            @if ($item->attributes && count($item->attributes))
                                                                @foreach ($item->attributes as $attr)
                                                                    <span data-item-attribute-name="{{ $attr->name }}" class="{{ parse_classname('cart-item-attribute', 'cart-item-attribute-selected', 'cart-item-attribute-value-' . $attr->name) }}">{{ $attr->text }}</span>
                                                                    @if (!$loop->last)
                                                                        /
                                                                    @endif
                                                                @endforeach
                                                            @endif

                                                        </p>
                                                    </div>
                                                    <div class="cart-item-info-right">
                                                        {{-- @if ($item->has_promo)
                                                                <p class="price-current {{parse_classname('item-total-list-price')}}">{{$item->getPriceFormat('total_list')}}</p>
                                                            @endif
                                                             --}}
                                                        <p class="price-sale">{{ $item->getPriceFormat('final') }}</p>
                                                        <p class="price-sale total-price {{ parse_classname('item-total-price') }}">{{ $item->getPriceFormat('total') }}</p>
                                                    </div>

                                                </div>
                                                <div class="row d-md-none">
                                                    <div class="col-12">
                                                        <p class="material_size">
                                                            @if ($item->attributes && count($item->attributes))
                                                                @foreach ($item->attributes as $attr)
                                                                    <span data-item-attribute-name="{{ $attr->name }}" class="{{ parse_classname('cart-item-attribute', 'cart-item-attribute-selected', 'cart-item-attribute-value-' . $attr->name) }}">{{ $attr->text }}</span>
                                                                    @if (!$loop->last)
                                                                        /
                                                                    @endif
                                                                @endforeach
                                                            @endif

                                                        </p>
                                                    </div>
                                                </div>


                                                <div class="row pt-40">
                                                    @if ($item->attributes && count($item->attributes))
                                                        @foreach ($item->attributes as $attr)
                                                            @if (($itemOption = $item->getOrderOption($attr->name)) && $itemOption->values)
                                                                <div class="col-md-6 mt-15">
                                                                    <label for="cart-item-attr-{{ $itemOption->attribute_id }}" class=" margin-top-20">{{ $itemOption->label ?? $itemOption->name }}</label>
                                                                    <select class="form-select t-form-select {{ parse_classname('cart-item-attribute') }}" data-item-id="{{ $item->id }}" name="attr[{{ $item->id }}][]" id="cart-item-attr-{{ $itemOption->attribute_id }}">
                                                                        @foreach ($itemOption->values as $attrVal)
                                                                            <option value="{{ $attrVal->value_id }}" @if ($attrVal->value_id == $attr->value_id) selected @endif>{{ $attrVal->text }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <div class="col-12"></div>
                                                    <div class="col-md-">
                                                        <div class="number-input cart-item-qty">
                                                            <button type="button" class="quantity-left-minus"></button>
                                                            <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" data-item-id="{{ $item->id }}" class="quantity {{ parse_classname('product-order-quantity', 'quantity', 'item-quantity') }}">
                                                            <button type="button" class="plus quantity-right-plus"></button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="cart-item-info-closed text-right">
                                                    <span class="cart-item-remove {{ parse_classname('remove-cart-item') }}" data-item-id="{{ $item->id }}">✕</span>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach


                                </div>



                            </div>

                        </form>
                    </div>

                    <div class="col-md-5">
                        <div class="pay">
                            <div class="box-info">
                                <h3 class="title">Thông tin thanh toán</h3>
                                <form action="{{ route('web.orders.place-order') }}" method="POST" class="form form--cart {{ parse_classname('checkout-form', 'placeh-order-form', 'cart-section') }}">
                                    @csrf
                                    <input type="hidden" name="cart_type" value="{{ $cart->type }}">
                                    <div class="card box-cart">
                                        @php
                                            $form = $cart->getForm([
                                                'className' => 'form-control',
                                            ]);

                                            $info = $form->get('billing_name', 'billing_phone_number', 'billing_email', 'billing_region_id', 'billing_district_id', 'billing_ward_id', 'billing_address');
                                            $shipping = $form->get('shipping_name', 'shipping_email', 'shipping_phone_number', 'shipping_region_id', 'shipping_district_id', 'shipping_ward_id', 'shipping_address');
                                        @endphp
                                        <div class="form-cart">
                                            <div class="row">
                                                @foreach ($info as $input)
                                                    <div class="col-md-{{ $input->name == 'billing_email' || $input->name == 'billing_address' ? 12 : 6 }} mr-input">
                                                        {{-- <label for="{{ $input->id }}">
                                                                {{ $input->label }}
                                                                @if ($input->required)
                                                                    <sup>*</sup>
                                                                @endif
                                                            </label> --}}
                                                        <div class="form-field">
                                                            {!! $input !!}
                                                        </div>
                                                        @if ($input->error)
                                                            <div class="error has-error">{{ $input->error }}</div>
                                                        @endif
                                                    </div>
                                                @endforeach

                                                <div class="col-md-12 mr-input">
                                                    {!! $form->note !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="discount-block">
                                                <div class="coupon-public">
                                                    <div class="coupons">
                                                        @if (($user = $request->user()) && count($coupons = app(\App\Repositories\Promos\PromoRepository::class)->getUserAvailablePromos($user->id)))
                                                            @foreach ($coupons as $coupon)
                                                                <div class="coupon coupon-item" data-coupon-code="{{ $coupon->code }}">
                                                                    <div class="coupon-left">
                                                                    </div>
                                                                    <div class="coupon-right">
                                                                        <div class="coupon-title">
                                                                            {{ $coupon->code }}
                                                                            <span class="coupon-count"><i>(còn {{ $coupon->total - $coupon->usage }})</i></span>
                                                                        </div>
                                                                        <div class="coupon-description">
                                                                            {{ $coupon->getDownTotalFLabel() }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="apply-voucher mb-20">
                                            <div class="input-group">
                                                <input type="text" class="form-control {{ parse_classname('coupon-code') }}" id="coupon_code" name="coupon_code" placeholder="Mã giảm giá" value="{{ old('coupon_code') }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-apply-voucher {{ parse_classname('btn-apply-coupon') }}">Áp dụng</button>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="ps-block--shopping-total">
                                            <h3 class="title">Hóa đơn</h3>
                                            <div class="table-responsive">
                                                <table class="table bill-table">
                                                    <tbody>
                                                        <tr>
                                                            <td>Tạm tính</td>
                                                            <td><span class="{{ parse_classname('cart-sub-total-amount') }}">{{ $helper->getCurrencyFormat($cart->sub_total) }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Khuyến mãi</td>
                                                            <td><span class="{{ parse_classname('cart-promo-amount') }}">Chưa có</span></td>
                                                        </tr>

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td>Tổng thành tiền</td>
                                                            <td><span class="{{ parse_classname('cart-total-ammount') }}">{{ $helper->getCurrencyFormat($cart->total_money) }}</span></td>

                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                        </div>


                                        <div class="box-method">
                                            <h3 class="title">Hình thức thanh toán</h3>
                                            <div class="method {{ parse_classname('payment-methods') }}">
                                                @if (count($methods = $helper->getPaymentMethodOptions()))
                                                    <?php
                                                    $defaultMethod = old('payment_method');
                                                    ?>
                                                    @foreach ($methods as $method)
                                                        @php
                                                            if (!$loop->index && !$defaultMethod) {
                                                                $defaultMethod = $method->method;
                                                            }
                                                        @endphp
                                                        <label for="payment-{{ $method->value }}" class="payment-method__item payment-group {{ parse_classname('payment-method-option') }}">
                                                            <span class="payment-method__item-custom-checkbox custom-radio">
                                                                <input type="radio" id="payment-{{ $method->value }}" name="payment_method" autocomplete="off" value="{{ $method->value }}" @if ($method->value == $defaultMethod || (!$defaultMethod && !$loop->index)) checked @endif>
                                                                <span class="checkmark"></span>
                                                            </span>
                                                            <span class="payment-method__item-icon-wrapper">
                                                                <img src="{{ $method->icon }}" alt="{{ $method->title }}">
                                                            </span>
                                                            <span class="payment-method__item-name">{{ $method->title }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if ($err = $errors->first('payment_method'))
                                                <div class="crazy-error">
                                                    {{ $err }}
                                                </div>
                                            @endif

                                        </div>

                                        <div class="box-method">
                                            {{-- <h3 class="title">
                                                    Gợi ý sản phẩm đi kèm
                                                </h3> --}}

                                            {{-- <div class="row box-prod-relate-to">
                                                    <div class="col-md-4">
                                                        <img src="./assets/images/custom/cart/san-pham/san-pham-lien-quan.png" alt="" style="width: 100%">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <p class="t-box-prod-name">
                                                            <a href="#">Áo khoác da “Racing”</a>
                                                        </p>
                                                        <span class="price-current">1.320.000đ</span>
                                                        <span class="price-sale">1.000.000đ</span>
                                                        <span class="product-promotion-percent">-20%</span>
                                                    </div>
                                                </div>

                                                <div class="row box-prod-relate-to">
                                                    <div class="col-md-4">
                                                        <img src="./assets/images/custom/cart/san-pham/san-pham-lien-quan.png" alt="" style="width: 100%">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <p class="t-box-prod-name">
                                                            <a href="#">Áo khoác da “Racing”</a>
                                                        </p>
                                                        <span class="price-current">1.320.000đ</span>
                                                        <span class="price-sale">1.000.000đ</span>
                                                        <span class="product-promotion-percent">-20%</span>
                                                    </div>
                                                </div> --}}

                                        </div>

                                        <div class="row btn-pay">
                                            <button type="submit" class="btn btn-apply-voucher">Thanh toán</button>
                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-content mt-40 mb-40">
                    <div class="empty-image text-center mb-10">
                        <img src="{{ theme_asset('images/empty-cart.png') }}" alt="">
                    </div>
                    <div class=" theme-color text-center">
                        Không có sản phẩm nào trong giỏ hàng
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- End Box contents Cart -->


@endsection
@section('js')
    <script>
        window.needReloadIfNotTheSame = true;
    </script>
@endsection
@php
    add_js_src(theme_asset('js/cart.js'));
@endphp
