@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', 'Dashboard')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Dashboard')


@section('content')



    <div class="row">
        <div class="col-12">


            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--tab">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon m--hide">
                                <i class="la la-gear"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Biểu đồ lượt view

                            </h3>
                        </div>
                    </div>

                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">

                            <li class="m-portlet__nav-item mr-4">
                                {{$monthViewTotal > 0 ?'+':''}} {{number_format($monthViewTotal, 0, ',', '.')}}% / tháng
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="m_morris_3" style="height:500px;"></div>
                </div>
            </div>

            <!--end::Portlet-->

        </div>

        <div class="col-md-7 col-xl-8">

            <!--begin::Portlet-->
            <div class="m-portlet" style="height: 100%">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon m--hide">
                                <i class="la la-gear"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Tổng quan gian hàng

                            </h3>
                        </div>
                    </div>

                </div>
                <div class="m-portlet__body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th class="id-col" style="width: 70px">STT</th>
                                    <th class="text-left">Tên gian hàng</th>
                                    <th>Lượt view</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($showroomStatistics as $showroom)
                                    <tr>
                                        <td class="id-col" style="width: 70px">{{$loop->index+1}}</td>
                                        <td class="text-left">{{$showroom->name}}</td>
                                        <td>{{$showroom->view_total}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!--end::Portlet-->
        </div>



        <div class="col-md-5 col-xl-4">

            <!--begin::Portlet-->
            <div class="m-portlet" style="height: 100%">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon m--hide">
                                <i class="la la-gear"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Đơn hàng

                            </h3>
                        </div>
                    </div>

                </div>
                <div class="m-portlet__body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                
                            </thead>
                            <tbody>
                                @foreach ($merchantTemplates as $template)
                                    @php
                                        $p = crazy_arr($template->package_data);
                                    @endphp
                                    <tr>
                                        <td colspan="2"><strong>{{$template->template_name}}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{$p->price_format}}</td>
                                        <td class="text-right">Hết hạn: {{date('d/m/Y', strtotime($template->expired_at))}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!--end::Portlet-->
        </div>
    </div>

@endsection
@section('js')
    <script src="{{ asset('static/manager/assets/vendors/custom/flot/flot.bundle.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('static/manager/assets/demo/default/custom/components/charts/morris-charts.js') }}" type="text/javascript"></script> --}}
    @php
        $data = [];
        $detail = [];
        $barColors = [];
        $colorList = ['#ffac16', '#0b62a4'];
        if ($statistics && count($statistics)) {
            $data = $statistics;
            foreach ($statistics as $i => $d) {
                // $date = date('d/m/Y', strtotime($d['view_date']));
                // $data[] = [$date, $d['view_total']];
                $barColors[] = $colorList[$i % 2]; 
                $detail[] = $d;
            }
        }
        add_js_data('merchant_data', [
            'statistics' => $data,
            'table' => $detail,
            'barColors' => $barColors
        ]);
    @endphp
    <script>
        // var t = function(t) {

        //     return merchant_data.statistics;
        // }(0);
        // console.log(t);
        // $.plot(
        //     $("#m_flotcharts_6"),
        //     [{
        //         data: t,
        //         lines: {
        //             lineWidth: 1
        //         },
        //         shadowSize: 0
        //     }], {
        //         series: {
        //             bars: {
        //                 show: !0
        //             }
        //         },
        //         bars: {
        //             barWidth: .8,
        //             lineWidth: 0,
        //             shadowSize: 0,
        //             align: "left"
        //         },
        //         grid: {
        //             tickColor: "#eee",
        //             borderColor: "#eee",
        //             borderWidth: 1
        //         }
        //     }
        // )

        new Morris.Bar({
            element: "m_morris_3",
            data: merchant_data.table,
            barColors: merchant_data.barColors,
            xkey: "label",
            ykeys: ["views"],
            labels: ["Lượt xem"]
        })
    </script>
@endsection
