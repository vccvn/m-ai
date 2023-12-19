@extends($_layout . 'main')
{{-- khai báo title --}}
@section('title', 'Thiết lập Gian hàng')
{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Gian hàng')
@section('body.class', 'm-brand--minimize m-aside-left--minimize body-light-bg ')
@section('subheader.disabled', 1)
@section('topbar.title', 'Chỉnh sửa Gian hàng')

@php
    array_unshift($showroomFormConfig['inputs'], [
        'namespace' => 'hidden_id',
        'name' => 'id',
        'id' => 'input-hidden-id',
        'type' => 'hidden',
        'value' => $showroom->id,
    ]);
@endphp

{{-- Nội dung --}}
@section('content')
    <div class="showroom-setting m-portlet m-portlet--last m-portlet--head-md m-portlet--responsive-mobile fit-100vh">
        <form action="" method="POST" enctype="multipart/form-data" class="settings-free m-form m-form--fit m-form--label-align-left crazy-form showroom-setting-form">
            @csrf
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Thiết lập gian hàng
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body m-border bottom size-1 silver solid">
                @include($_base . 'forms.form-input-list', $showroomFormConfig)
            </div>

            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Chỉnh sửa gian hàng
                            @if ($template && !$available)
                                <span class="text-danger ml-4">
                                    Mẫu gian hàng dã hết hạn {{ $expire }}
                                </span>
                            @endif
                        </h3>
                    </div>
                </div>

                <div class="m-portlet__head-tools">
                    <ul class="m-portlet__nav">

                        <li class="m-portlet__nav-item mr-4">
                            Chọn Mẫu
                        </li>

                        <li class="m-portlet__nav-item">
                            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right" m-dropdown-toggle="click">
                                <a href="#" class="m-dropdown__toggle btn btn-primary dropdown-toggle">
                                    {{ $template ? $template->name : 'Gian hàng mẫu 1' }}
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__section m-nav__section--first">
                                                        <span class="m-nav__section-text">Chọn mẫu</span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0)" class="m-nav__link btn-open-template-modal">
                                                            <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                            <span class="m-nav__link-text">Mẫu có sẵn</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="#" class="m-nav__link btn-contact">
                                                            <i class="m-nav__link-icon flaticon-share"></i>
                                                            <span class="m-nav__link-text">Yêu cầu thiết kế riêng</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        @if ($template && !$available)
                            <a href="javascript:void(0);" class="btn btn-danger btn-renewal-template" data-uuid="{{ $template->uuid }}">Gia hạn</a>
                        @endif
                    </ul>
                </div>

            </div>

            <div class="m-portlet__body m-border bottom size-1 silver solid">
                @if ($template)
                    <div class="row showroom-template-form">
                        <div class="col-md-7 col-lg-7 col-preview">
                            <div class="showroom-preview">
                                <iframe id="preview-3d-iframe" src="" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-5">
                            <div class="custom-panel">
                                <div class="panel-inner">

                                    <?php
                                    $rawInputs = $template->getInputs();
                                    $newInputs = [];
                                    $tabs = $template->getTabs();
                                    $inputData = $setting ? $setting->getSettingData() : [];
                                    ?>
                                    @if ($tabs && is_countable($tabs) && count($tabs))
                                        <div class="tab-wrapper">
                                            <ul class="nav nav-tabs  m-tabs-line m-tabs-line--primary" role="tablist">
                                                @foreach ($tabs as $tab)
                                                    <li class="nav-item m-tabs__item">
                                                        <a class="nav-link m-tabs__link {{ $loop->index == 0 ? 'active' : '' }}" data-toggle="tab" href="#showroom-setting-tab-{{ $tab['key'] }}" role="tab">{{ $tab['text'] }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content">

                                                @foreach ($tabs as $tab)
                                                    @php
                                                        $tapInputData = [];
                                                        $tabInputs = [];
                                                        $newInputs = [];
                                                        if ($tab['inputs']) {
                                                            foreach ($tab['inputs'] as $name) {
                                                                $tapInputData[$name] = $inputData[$name] ?? null;
                                                                $tabInputs[$name] = $rawInputs[$name];
                                                            }
                                                        }
                                                        
                                                        foreach ($tabInputs as $key => $inp) {
                                                            if ($inp['type'] == 'list') {
                                                                if (array_key_exists('item', $inp)) {
                                                                    $inp['max'] = array_key_exists('max', $inp) && $inp['max'] > 0 ? (int) $inp['max'] : 99;
                                                                    $inp['@item'] = $inp['item'];
                                                                    $newInputs[] = $inp;
                                                                }
                                                            } else {
                                                                $newInputs[] = $inp;
                                                            }
                                                        }
                                                        $formConfig = [
                                                            'inputs' => $newInputs,
                                                            'data' => $tapInputData ?? [],
                                                            'config' => [
                                                                'save_button_text' => 'Lưu',
                                                                'cancel_button_text' => 'Hủy',
                                                                'layout_type' => 'default',
                                                            ],
                                                            'attrs' => [
                                                                'method' => 'POST',
                                                                'action' => route($route_name_prefix . 'showrooms.setting'),
                                                                'id' => 'showroom-form-tab-' . $tab['key'],
                                                                'class' => 'crazy-form',
                                                            ],
                                                        ];
                                                        
                                                    @endphp



                                                    <div class="tab-pane {{ $loop->index == 0 ? 'active' : '' }}" id="showroom-setting-tab-{{ $tab['key'] }}" role="tabpanel">

                                                        <div class="inner-scrollable m-scrollable" data-scrollable="true">
                                                            @include($_base . 'forms.simple-input-list', $formConfig)
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            
                                            foreach ($rawInputs as $key => $inp) {
                                                if ($inp['type'] == 'list') {
                                                    if (array_key_exists('item', $inp)) {
                                                        $inp['max'] = array_key_exists('max', $inp) && $inp['max'] > 0 ? (int) $inp['max'] : 1;
                                                        $inp['@item'] = $inp['item'];
                                                        $newInputs[] = $inp;
                                                    }
                                                } else {
                                                    $newInputs[] = $inp;
                                                }
                                            }
                                            $formConfig = [
                                                'inputs' => $newInputs,
                                                'data' => $setting ? $setting->getSettingData() : [],
                                                'config' => [
                                                    'save_button_text' => 'Lưu',
                                                    'cancel_button_text' => 'Hủy',
                                                    'layout_type' => 'default',
                                                ],
                                                'attrs' => [
                                                    'method' => 'POST',
                                                    'action' => route($route_name_prefix . 'showrooms.setting'),
                                                    'id' => 'showroom-form',
                                                    'class' => 'crazy-form',
                                                ],
                                            ];
                                            
                                        @endphp

                                        <div class="outer-scrollable m-scrollable" data-scrollable="true">
                                            @include($_base . 'forms.simple-input-list', $formConfig)
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="btn btn-info btn-submit-form">
                                Lưu
                            </button>
                            <a href="javascript:history.back(1)" class="btn btn-secondary">
                                Huỷ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade template-modal" id="template-modal" tabindex="-1" role="dialog" aria-labelledby="template-modal-title">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="template-modal-title">
                        Chọn mẫu gian hàng
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="template-list row">
                        @if (count($templateList))
                            @php
                                $currency = crazy_arr([
                                    'USD' => 'USD',
                                    'VND' => 'VNĐ',
                                    'EUR' => 'EUR',
                                ]);
                                $dateUnit = crazy_arr([
                                    'day' => 'ngày',
                                    'month' => 'tháng',
                                    'year' => 'năm',
                                ]);
                            @endphp
                            @foreach ($templateList as $item)
                                <div class="col-sm-6 col-md-4 col-item">
                                    <div class="template-item">

                                        <div class="thumbnail">
                                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->name }}">
                                        </div>
                                        <div class="info">
                                            <div class="name">
                                                <h3>{{ $item->name }}</h3>
                                            </div>
                                        </div>

                                        @if ($item->trial)
                                            <span class="trial">{{ $item->trial }}</span>
                                        @endif

                                        <div class="info info-hover">
                                            <div class="inner">
                                                <div class="name">
                                                    <h3>{{ $item->name }}</h3>
                                                </div>
                                                <p class="description">{!! nl2br($item->description) !!}</p>

                                            </div>
                                        </div>
                                        <div class="package-info">
                                            <div class="price">
                                                @if ($item->min_price_per_month)
                                                    @if ($item->max_price_per_month)
                                                        <del>{{ $item->max_price_per_month }}</del>
                                                        <br>
                                                    @endif
                                                    {{ $item->min_price_per_month }}
                                                @else
                                                    Miễn phí
                                                @endif
                                            </div>
                                            <div class="buttons">
                                                @if ($setting && $setting->template_uuid == $item->uuid)
                                                    <button class="btn btn-secondary">Đang dùng</button>
                                                @elseif(!$item->has_own)
                                                    <a href="{{ route('merchant.templates.checkout', ['template_uuid' => $item->uuid]) }}" class="btn btn-info btn-buy-template" data-uuid="{{ $item->uuid }}">Mua mẫu</a>
                                                @elseif(($mat = $item->merchantActiveTemplate) && !$mat->expired_status)
                                                    <a href="{{ route('merchant.templates.checkout-renewal', ['template_uuid' => $item->uuid]) }}" class="btn btn-danger btn-renewal-template" data-uuid="{{ $item->uuid }}">Gia hạn</a>
                                                @else
                                                    <button class="btn btn-primary btn-choose-template" data-uuid="{{ $item->uuid }}">Áp dụng</button>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade template-modal" id="checkout-template-modal" tabindex="-1" role="dialog" aria-labelledby="checkout-template-modal-title">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkout-template-modal-title">
                        Tuỳ chọn thanh toán
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="checkout-template-section" class="template-packages"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade template-modal" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="payment-modal-title">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payment-modal-title">
                        Phương thức thanh toán
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="payment-section" class="payment-methods">
                        <form method="POST" action="{{ route('merchant.templates.order') }}" id="template-payment-form">
                            <input type="hidden" name="uuid" id="payment-template-uuid">
                            <input type="hidden" name="package_id" id="payment-package-id">

                            <div class="method {{ parse_classname('payment-methods') }}">
                                @if (count($methods = get_payment_method_options()))
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
                                            <input type="radio" id="payment-{{ $method->value }}" name="payment_method" autocomplete="off" value="{{ $method->value }}" @if ($method->value == $defaultMethod || (!$defaultMethod && !$loop->index)) checked @endif>
                                            <div class="payment-method__item-inner">
                                                <span class="payment-method__item-custom-checkbox custom-radio">

                                                    <span class="checkmark"></span>
                                                </span>
                                                <span class="payment-method__item-icon-wrapper">
                                                    <img src="{{ $method->icon }}" alt="{{ __($method->title) }}">
                                                </span>
                                                <span class="payment-method__item-name">
                                                    @if ($method->method == 'stripe')
                                                        <img src="{{ asset('static/contents/payments/icons/ccv.png') }}" alt="" style="width: 80%">
                                                    @else
                                                        {{ __($method->title) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning text-center">
                                        Chưa có phương thức thanh toán

                                    </div>
                                @endif
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-block">Tiến hành thanh toán</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@php
    add_js_src('/static/features/showrooms/setting.js');
    add_css_link('/static/features/showrooms/setting.min.css');
    add_js_data('showroom_setting_config', [
        'urls' => [
            'use_template' => route('merchant.showrooms.use-template'),
            'checkout' => route('merchant.templates.checkout'),
            'checkout_renewal' => route('merchant.templates.checkout-renewal'),
            'order' => route('merchant.templates.order'),
            'renewal' => route('merchant.templates.renewal'),
            'check' => route('merchant.templates.check'),
            'contact' => route('merchant.contacts.send'),
        ],
        'preview' => [
            'url' => $template ? $template->getPreviewUrl(auth()->user()->id) : '',
        ],
        'user' => $user,
    ]);
@endphp
@section('js')
    @if ($errorSession = session('error'))
        <script>
            App.Swal.error(@json($errorSession));
        </script>
    @elseif ($validateError = $errors->first())
        <script>
            App.Swal.error("Đã có lỗi xảy ra. Vui lòng kiểm tra lại thông tin");
        </script>
    @endif
@endsection
