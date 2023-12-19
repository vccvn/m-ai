
    <div class="modal fade style-set-template-form-modal" id="style-set-template-form-modal" tabindex="-1" role="dialog" aria-labelledby="style-set-template-form-modal-title">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form action="{{route('admin.style-sets.personal.templates.ajax-create')}}" method="post" id="create-style-set-template-form">
                    <div class="modal-header custom-style bg-info">
                        <h5 class="modal-title" id="style-set-template-form-modal-title">
                            <i class="fa fa-paint-brush"></i>
                            Thêm Style Template
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include($_base.'forms.form-input-list', [
                            'inputs' => get_json_form_data('admin/modules/style-sets/personal/templates/form'),
                            'data' => get_personal_style_set_avatar_config(),
                            'config' => [
                                'form_group_options' => [
                                    'group_class' => '',
                                    'label_class' => '',
                                    'wrapper_class' => ''
                                ],
                                'form_group_style' => 'custom',
                                'log_style' => true,
                            ]
                        ])         
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info btn-add-template" id="btn-addstyle-set-template">Thêm</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


