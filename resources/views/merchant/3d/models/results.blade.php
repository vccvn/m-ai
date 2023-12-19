<?php
$columns = [
    'name' => 'Tiêu đề',
    'keywords' => 'Từ khoa',
];
extract(get_result_blade_vars('Model', isset($type) && strtolower($type) == 'trash' ? 'trash' : 'default'));
$current_page = request()->page;
if (!is_numeric($current_page) || $current_page < 1) {
    $current_page = 1;
}
$index = ($current_page - 1) * 10 + 1;

?>
@include($_template . 'list-filter', [
    'sortable' => array_merge($columns, [
        'updated_at' => 'Thời gian cập nhật',
    ]),
    'searchable' => $columns,
])

@if (count($results))
    <div class="list-model crazy-list">
        <div class="row">
            @foreach ($results as $item)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 model-item" id="crazy-item-{{ $item->id }}" data-name="{{ $item->name }}">

                    <!--begin:: Widgets/Blog-->
                    <div class="m-portlet m-portlet--bordered-semi m-portlet--full-height  m-portlet--rounded-force">

                        <div class="m-portlet__head m-portlet__head--fit">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-action">
                                    <button type="button" class="btn btn-sm m-btn--pill  btn-dark">
                                        {{$item->getStatusLabel()}}
                                    </button>
                                </div>
                            </div>
                        </div>


                        <div class="m-portlet__body model-body">
                            <div class="m-widget19">
                                <div class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides m-thumbnail" style="">
                                    <img src="{{ $item->getThumbnail() }}" alt="">
                                    <h3 class="m-widget19__title m--font-light">
                                        {{ $item->name }}
                                    </h3>
                                    <div class="m-widget19__shadow"></div>
                                </div>
                                <div class="m-widget19__content model-content">

                                    <div class="m-widget19__body">
                                        {{ $item->description }}
                                    </div>

                                    @if ($type != 'trash')
                                        <div class="m-tool">


                                            <a class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only        btn-outline-primary btn-sm" href="{{ route($route_name_prefix . '3d.models.update', ['id' => $item->id]) }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Sửa thông tin"><i class="fa fa-info"></i></a>
                                            {{-- <button type="button" class="btn m-btn--pill m-btn--air         btn-secondary btn-sm">Small button</button> --}}
                                            {{-- <button type="button" class="btn m-btn--pill m-btn--air         btn-outline-success btn-sm">Small button</button> --}}
                                            {{-- <button type="button" class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only        btn-outline-info btn-sm btn-edit-3d" data-id="{{ $item->secret_id }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Sửa 3D"><i class="fa fa-cube"></i></button> --}}
                                            <button type="button" class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only        btn-outline-warning btn-sm btn-copy-this-content" data-id="{{ $item->secret_id }}" data-copy-content="{{ route('web.ar.models.view', ['id' => $item->secret_id]) }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Copy View Model URL"><i class="fa fa-copy"></i></button>
                                            <button type="button" class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only        btn-outline-accent btn-sm btn-show-qr-code" data-qr-image="{{ $item->getQRCodeImageUrl() }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Hiển thị mã QR"><i class="fa fa-qrcode"></i></button>

                                            @if ($item->hasTrackingImage())
                                                <button type="button" class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only     btn-outline-brand btn-sm btn-show-tracking-image" data-tracking-image="{{ $item->getTrackingImageUrl() }}" data-secret="{{ $item->secret_id }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Hiển thị ảnh tracking"><i class="fa fa-image"></i></button>
                                            @elseif($item->is_processing)
                                                <button type="button" class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only     btn-outline-metal active btn-sm btn-show-tracking-imapp" data-toggle="m-tooltip" data-placement="right" data-original-title="Đang xử lý ảnh tracking"><i class="fa fa-image"></i></button>
                                            @endif

                                            <button type="button" class="btn m-btn--pill m-btn--air m-btn--icon m-btn--icon-only        btn-outline-danger btn-sm {{ $btn_class }}" data-id="{{ $item->id }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Xoá"><i class="fa fa-trash"></i></button>
                                            {{-- <button type="button" class="btn m-btn--pill m-btn--air         btn-outline-brand btn-sm">Small button</button> --}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--end:: Widgets/Blog-->
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-3">
                <a class="btn m-btn--pill m-btn--air m-btn--icon         btn-outline-primary btn-sm" href="{{ route($route_name_prefix . '3d.models.upload') }}" data-toggle="m-tooltip" data-placement="right" data-original-title="Tải lên file mới"><i class="fa fa-plus"></i> Thêm Model</a>

            </div>
            <div class="col-9 mx-auto text-right">
                {{ $results->links($_pagination . 'default') }}
            </div>
        </div>
    </div>
@elseif($type != 'trash')
    <div class="col-12 my-5 py-5">
        <div class="mb-4 text-center">
            <img src="{{ asset('static/manager/assets/images/empty.png') }}" alt="" style="max-width: 100%">
        </div>
        <div class="btn-s text-center">
            <a href="{{ route('merchant.3d.models.upload') }}" class="btn btn-info">
                <i class="fa fa-plus"></i>
                Upload Model
            </a>
        </div>
    </div>
@endif
