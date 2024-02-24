
        <div class="package-options">
            @if (is_array($packages = $template->packages) && count($packages))

                @foreach ($packages as $i => $d)

                    <div class="package-item" data-index="{{ $i }}" data-package='@json($d->all())'>
                        <h3>
                            {{ $d->times }}
                        </h3>
                        @if (!$d->price)
                            <p class="per-month">
                                {{ $d->trial }}
                            </p>
                        @else
                            <p class="per-month">Thanh toán hằng tháng chỉ từ {{ $d->price_per_month_format }}</p>
                        @endif
                        <div class="price-select">
                            <p class="price">
                                {{ $d->price_format }}
                            </p>

                            <div class="buttons">
                                <a href="#" class="btn btn-danger btn-choose" data-id="{{$template->id}}" data-package="{{$i+1}}">
                                    @if ($d->price)
                                        <i class="fa fa-shopping-cart"></i>
                                        Đặt hàng
                                    @else
                                        Dùng thử
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
            @endif
        </div>
