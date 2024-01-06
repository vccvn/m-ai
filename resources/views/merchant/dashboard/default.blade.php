@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', 'Dashboard')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Dashboard')


@section('content')
    @php
        $webType = get_web_type();

        $user = auth()->user();
        $agent = get_agent_account($user->id);
        $wallet = get_user_wallet($user->id);
        $refUser = $user->agent_id || $user->ref_code ? get_user($user->agent_id ? ['id' => $user->agent_id] : ['affiliate_code' => $user->ref_code ?? 'JJJRSRFSRSRS']) : null;
    @endphp


    <div class="row">
        <div class="col-12">

            <!--begin::Portlet-->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Admin Dashboard
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">

                    <!--begin::Section-->
                    <div class="m-section m-section--last">

                        <p>Đại lý: {{ $agent && $agent->policy ? ' [ ' . $agent->policy->name . ']' : '' }}</p>

                        <p>Hoa hồng: {{ get_price_format($wallet->balance, 'VND') }}</p>
                        <p>Số dư tháng: {{ $agent ? $agent->month_balance : 0 }} tháng</p>
                        <p>Mục tiêu: {{ $agent ? get_price_format($agent->revenue, 'VND') . ($agent->policy ? ' / ' . get_price_format($agent->policy->revenue_target, 'VND') : '') : '' }}</p>

                        @if ($refUser)
                            <p>Người giới thiệu: {{ $refUser->name }}</p>
                        @endif
                        <p>
                        <div class="input-group">

                            <label for="affiliate" class="input-group-prepend" class="mb-0" style="margin-bottom: 0;"><span class="input-group-text">Mã giới thiệu của bạn</span></label>
                            <input type="text" name="affiliate" id="affiliate" class="form-control m-inpur" value="{{ $user->affiliate_code }}">
                        </div>

                        </p>
                    </div>

                    <!--end::Section-->
                </div>
            </div>

            <!--end::Portlet-->
        </div>
    </div>

@endsection
