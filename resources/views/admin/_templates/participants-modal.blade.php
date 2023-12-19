<div class="modal fade participants-modal" id="participants-modal" tabindex="-1" role="dialog" aria-labelledby="participants-modal-title">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header custom-style bg-info">
                <h5 class="modal-title" id="participants-modal-title">
                    <i class="fa fa-paint-brush"></i>
                    Thêm người tham gia
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="user-form">
                    <div class="mt-1 mb-4 crazy-form-group row " id="participant-select-form-group">
                        <label class="col-md-4 col-lg-3 col-form-label" for="participant-select">
                            Người

                        </label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.crazyselect', [
                                'input' => new \Gomee\Html\Input([
                                    'name' => 'participant_select',
                                    'id' => 'participant-select',
                                    "type" => "crazyselect",
                                    "label" => "Người tham gia",
                                    "@select-type" => "search",
                                    "@search-route" => "admin.users.select-option",
                                    "call" => "get_user_options",
                                    "params" => [
                                        [
                                            "id" => ":defval",
                                            "@limit" => 10
                                        ],
                                        "Chọn một"
                                    ]
                                ])
                            ])

                        </div>

                    </div>

                    <div class="mt-1 mb-4 crazy-form-group row " id="sort_type-form-group">
                        <label class="col-md-4 col-lg-3 col-form-label" for="sort_type">
                            Quyền
                        </label>
                        <div class="col-md-8 col-lg-9">
                            @include($_base.'forms.templates.crazyselect', [
                                'input' => new \Gomee\Html\Input([
                                    'name' => 'status_select',
                                    'id' => 'status-select',
                                    "type" => "crazyselect",
                                    "label" => "Người đăng bài",
                                    'data' => \App\Base\Constants\ParticipantConstant::STATUS_LABELS
                                ])
                            ])

                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info btn-participant-select" id="btn-participant-select">Thêm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
