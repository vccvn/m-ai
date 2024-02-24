

<div class="modal fade place-modal" id="place-modal" tabindex="-1" role="dialog" aria-labelledby="place-modal-title">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form action="{{route($route_name_prefix.'location.places.ajax-create')}}" method="POST" id="add-place-form">
                <input type="hidden" name="id" id="place-id" value="">

                <div class="modal-header custom-style bg-info">
                    <h5 class="modal-title" id="place-modal-title">
                        <i class="fa fa-info-circle"></i>
                        <span>Thêm địa điểm</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Tên</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" name="name" id="place-name" class="form-control m-input" placeholder="Nhập tên địa điểm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Loại địa điểm</label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.crazyselect', [
                                'input' => html_input([
                                    'type' => 'crazyselect',
                                    'name' => 'type_id',
                                    'id' => 'type-id',
                                    'call' => 'get_place_type_options'
                                ])
                            ])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Tỉnh / thành phố</label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.crazyselect', [
                                'input' => html_input([
                                    'type' => 'crazyselect',
                                    'name' => 'region_id',
                                    'id' => 'region_id',
                                    'data' => 'get_region_options',
                                    '@change' => 'App.location.changeRegionID'
                                ])
                            ])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Quận / huyện <small>(tuỳ chọn)</small></label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.crazyselect', [
                                'input' => html_input([
                                    'type' => 'crazyselect',
                                    'name' => 'district_id',
                                    'id' => 'district_id',
                                    'data' => ['' => 'Chọn quận huyện'],
                                    'params' => [['id' => "jkghjkh"]],
                                    '@change' => 'App.location.changeDistrictID'
                                ])
                            ])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Xã / phường <small>(tuỳ chọn)</small></label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.crazyselect', [
                                'input' => html_input([
                                    'type' => 'crazyselect',
                                    'name' => 'ward_id',
                                    'id' => 'ward_id',
                                    'data' => ['' => 'Chọn quận huyện'],
                                    'params' => [['id' => "jghjfg"]],
                                ])
                            ])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Địa chỉ <small>(tuỳ chọn)</small></label>
                        <div class="col-md-8 col-lg-9">
                            <input type="text" name="address" id="place-address" class="form-control m-input" placeholder="Nhập địa chỉ">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-lg-3 col-form-label">Ảnh hiển thị <small>(tuỳ chọn)</small></label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.media', [
                                'input' => html_input([
                                    'type' => 'media',
                                    'name' => 'thumbnail_id',
                                    'id' => 'thumbnail_id',
                                ])
                            ])
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info btn-submit">Thêm</button>
                    <button type="button" class="btn btn-secondary btn-cancel" data-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>

