@extends($_layout.'master')
@include($_lib.'register-meta')

@section('content')


    <!-- user dashboard section start -->
    <section class="section-b-space min-80vh">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <ul class="nav nav-tabs custome-nav-tabs flex-column category-option">

                        <li class="nav-item mb-2">
                            <a class="nav-link font-light {{$key?'': 'active'}}" href="{{route('web.orders.manager')}}"><i class="fas fa-angle-right"></i>Tất cả đơn hàng</a>
                        </li>

                        @foreach ($status_list as $item)
                            <li class="nav-item mb-2">
                                <a class="nav-link font-light {{$key == $item->key ? "active":""}}" href="{{route('web.orders.list', ['status_key' => $status_keys[$item->key]])}}"><i class="fas fa-angle-right"></i>{{$item['label']}}</a>
                            </li>
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link font-light {{$key?'': 'active'}}" href="{{route('web.customers.logout')}}"><i class="fas fa-angle-right"></i> Đăng xuất tài khoản</a>
                        </li>

                    </ul>
                </div>

                <div class="col-lg-9">
                    <div class="filter-button dash-filter dashboard">
                        <button class="btn btn-solid-default btn-sm fw-bold filter-btn">Hiển thị mennu</button>
                    </div>
                    <div class="main">
                        <div class="box-head mb-3">
                            <h3>{{$list_title}}</h3>
                        </div>

                        @if (count($orders))
                            <div class="table-responsive">
                                <table class="table cart-table">
                                    <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Thời gian</th>
                                        <th>Thanh toán</th>
                                        <th>Trạng thái</th>
                                        <th class="product-price">Giá trị</th>
                                        <th>Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($orders as $order)
                                        <tr class="{{parse_classname('order-item', 'order-item-'.$order->id)}}" id="{{'order-item-'.$order->id}}">
                                            <td><p class="mt-0">{{$order->code}}</p></td>
                                            <td><p class="mt-0">{{$order->dateFormat('d/m/Y')}}</p></td>
                                            <td class="wide-column">
                                                <p class="mt-0 order-status">{{$order->getPaymentMethodText()}}
                                                    @if ($order->status >= 0)
                                                    <br>
                                                    ({{$order->getPaymentStatusLabel()}})
                                                    @endif

                                                </p>
                                            </td>
                                            <td><p class="mt-0 order-status">{{$order->getStatusLabel()}}</p></td>
                                            <td class="product-price"><p class="theme-color fs-6">{{$helper->getCurrencyFormat($order->total_money)}}</p></td>
                                            <td>
                                                <a href="{{route('web.orders.detail', ['code' => $order->code??$order->id])}}" class="">Chi tiết</a>
                                                @if ($order->canCancel())
                                                    |
                                                    <a href="#" class="{{parse_classname('btn-cancel-order')}}" data-id="{{$order->id}}" data-order-code="{{$order->code}}">Hủy</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{$orders->links($_template.'pagination')}}
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                Không có đơn hàng nào!
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- user dashboard section end -->

    {{--<div class="ps-order-tracking pt-60">--}}
    {{--    <div class="container">--}}
    {{--        <div class="row">--}}
    {{--            <div class="col-lg-3 mb-md-5 pb-50">--}}

    {{--                <div class="widget widget_shop p-3">--}}
    {{--                    <a class="nav-link {{$key?'': 'active'}}" href="{{route('web.orders.manager')}}">Tất cả đơn hàng</a>--}}
    {{--                    @foreach ($status_list as $item)--}}
    {{--                    <a class="nav-link {{$key == $item->key ? "active":""}}" href="{{route('web.orders.list', ['status_key' => $status_keys[$item->key]])}}">{{$item['label']}}</a>--}}
    {{--                    @endforeach--}}
    {{--                    <a class="nav-link" href="{{route('web.customers.logout')}}">Thoát</a>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--            <div class="col-lg-9">--}}
    {{--                <figure class="ps-block--vendor-status">--}}
    {{--                    <figcaption>{{$list_title}}</figcaption>--}}
    {{--                    @if (count($orders))--}}
    {{--                        <div class="">--}}
    {{--                            <table class="table ps-table ps-table--vendor">--}}
    {{--                                <thead>--}}
    {{--                                    <tr>--}}
    {{--                                        <th>Mã</th>--}}
    {{--                                        <th>Thời gian</th>--}}
    {{--                                        <th>Phương thức thanh toán</th>--}}
    {{--                                        <th>Trạng thái</th>--}}
    {{--                                        <th>Giá trị</th>--}}
    {{--                                        <th>Hành động</th>--}}
    {{--                                    </tr>--}}
    {{--                                </thead>--}}
    {{--                                <tbody>--}}
    {{--                                    @foreach ($orders as $order)--}}
    {{--                                    <tr class="{{parse_classname('order-item', 'order-item-'.$order->id)}}" id="{{'order-item-'.$order->id}}">--}}
    {{--                                        <td>{{$order->id}}</td>--}}
    {{--                                        <td>{{$order->dateFormat('d/m/Y')}}</td>--}}
    {{--                                        <td class="wide-column">{{$order->payment_method_name}}</td>--}}
    {{--                                        <td>{{$order->getStatusLabel()}}</td>--}}
    {{--                                        <td>{{$helper->getCurrencyFormat($order->total_money)}}</td>--}}
    {{--                                        <td>--}}
    {{--                                            <a href="{{route('web.orders.detail', ['id' => $order->id])}}" class="">Chi tiết</a>--}}
    {{--                                            @if ($order->canCancel())--}}
    {{--                                            |--}}
    {{--                                            <a href="#" class="{{parse_classname('btn-cancel-order')}}" data-id="{{$order->id}}">Hủy</a>--}}
    {{--                                            @endif--}}
    {{--                                        </td>--}}
    {{--                                    </tr>--}}

    {{--                                    @endforeach--}}
    {{--                                </tbody>--}}
    {{--                            </table>--}}

    {{--                            {{$orders->links($_template.'pagination')}}--}}
    {{--                        </div>--}}
    {{--                    @else--}}
    {{--                        <div class="alert alert-warning text-center">--}}
    {{--                            Không có đơn hàng nào!--}}
    {{--                        </div>--}}
    {{--                    @endif--}}

    {{--                </figure>--}}

    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--</div>--}}
@endsection
