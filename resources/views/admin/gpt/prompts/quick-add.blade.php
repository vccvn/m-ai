@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', 'Thêm nhanh')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Thêm nhanh')


@section('content')



    <div class="row mt-4 mt-5">
        <div class="col-sm-8 col-md-8 col-lg-8 ml-auto mr-auto">
            <div class="mb-3">
                Họ tên : {{ $user->name }}
                <br>
                Số Prompt đã tạo: <span class="prompt-count">{{ $promptCount }}</span>
            </div>
            <!--begin::Portlet-->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Thêm nhanh
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">

                    <!--begin::Section-->
                    <div class="m-section m-section--last ">
                        <form action="{{ route('admin.gpt.prompts.quick-add') }}" method="POST" id="quick-add-form">
                            @csrf

                            <div class="form-group">
                                <label for="topic_id">Chọn chủ đề</label>
                                <div>
                                    @include('admin.forms.templates.crazyselect', [
                                        'input' => html_input([
                                            'type' => 'crazyselect',
                                            'label' => 'Danh mục',
                                            'required' => 'true',
                                            'id' => 'topic_id',
                                            'name' => 'topic_id',
                                            'call' => 'get_topic_options',
                                            '@label-type' => 'header',
                                            '@change' => 'Prompts.form.changeCategory',
                                        ]),
                                    ])
                                </div>
                                @if ($em = $errors->first('topic_id'))
                                    <div class="text-danger">
                                        {{ $em }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="prompt-content">Nội dung prompt</label>
                                <div class="input-group">
                                    <textarea name="prompt" id="prompt-content" cols="30" rows="10" class="form-control m-input" placeholder="Nhập nội dung prompt"></textarea>
                                </div>
                            </div>
                            <div class="bonus-info">
                                <div class="bonus-header">
                                    <div class="title">
                                        Thông tin bổ xung <i class="fa fa-angle-down"></i>
                                    </div>
                                    <div class="buttons">
                                        <a href="#" class="btn btn-primary btn-sm btn-generate-info">Generate</a>
                                    </div>
                                </div>
                                <div class="bonus-body">
                                    <div class="form-group">
                                        <label for="prompt-name">Tên prompt (tuỳ chọn)</label>
                                        <div class="input-group">
                                            <input type="text" name="name" id="prompt-name" class="form-control m-input" placeholder="ví dụ: Tảo bảng thống kê">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="prompt-description">Mô tả (Tuỳ chọn)</label>
                                        <div class="input-group">
                                            <textarea name="description" id="prompt-description" cols="30" rows="10" class="form-control m-input" placeholder="Nhập nội dung Mô tả..."></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="prompt-placeholder">Nội dung placeholder</label>
                                        <div class="input-group">
                                            <input type="text" name="placeholder" id="prompt-placeholder" class="form-control m-input" placeholder="Nhập nội dung placeholder">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="mt-3 pt-3 text-center">
                                <button type="submit" class="btn btn-info">Thêm</button>
                            </div>
                        </form>
                    </div>

                    <!--end::Section-->
                </div>
            </div>

            <!--end::Portlet-->
        </div>
    </div>

@endsection
@php
    add_css_link('/static/features/gpt/prompts/quick-add.min.css?v=1');
@endphp
@section('js')
    <script>
        $(function() {
            let promptCount = {{ $promptCount }};
            let $topicId = $('#topic_id');
            let $promptContent = $('#prompt-content');
            let $promptName = $('#prompt-name');
            let $description = $('#prompt-description');
            let $placeholder = $('#prompt-placeholder');
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            function generateInfo() {
                let promptContent = $promptContent.val();
                if (promptContent.trim() == '') return App.Swal.warning('Vui lòng nhập nội dung');
                App.Swal.showLoading();
                App.api.post("{{ route('admin.gpt.prompts.analytics') }}", {
                        prompt: promptContent
                    })
                    .then(rs => {
                        App.Swal.hideLoading();
                        if (rs.status) {
                            let data = rs.data;
                            let {
                                name,
                                description,
                                placeholder
                            } = data;
                            if (name) $promptName.val(name);
                            if (description) $description.val(description);
                            if (placeholder) $placeholder.val(placeholder);
                        } else {
                            App.Swal.warning(rs.message);
                        }
                    })
                    .catch(e => {
                        App.Swal.hideLoading();
                        App.Swal.error("Lỗi không xác định");
                    })
            }
            $('#quick-add-form').on('submit', function(e) {
                e.preventDefault();
                // let fd = new FormData(this);
                let topic_id = $topicId.val();
                let prompt_content = $promptContent.val();
                let name = $promptName.val();
                let description = $description.val();
                let placeholder = $placeholder.val();
                if (!topic_id || topic_id == 0 || topic_id == "0")
                    App.Swal.warning("Vui lòng chọn chủ đề");
                else if (!prompt_content || prompt_content.trim() == "")
                    App.Swal.warning("Vui lòng nhập nội dung prompt");
                else {
                    App.Swal.showLoading();
                    App.api.post("{{ route('admin.gpt.prompts.quick-add') }}", {
                            topic_id,
                            name,
                            prompt: prompt_content,
                            description,
                            placeholder
                        })
                        .then(rs => {
                            if (rs.status) {

                                App.Swal.hideLoading();
                                // App.Swal.success("Đã thêm mới prompt thành công");

                                toastr.success("Đã thêm prompt [" + rs.data.name + "] thành công");
                                $promptContent.val('');
                                $promptName.val('');
                                $description.val('');
                                $placeholder.val('');
                                promptCount++;
                                $('.prompt-count').html(promptCount);
                            } else {
                                App.Swal.warning(rs.message);
                            }
                        })
                        .catch(e => {
                            App.Swal.error('Lỗi hệ thống.\n Vui lòng thử lại sau giây lát')
                            console.log(e.message)
                        })
                }

            });

            $('.bonus-header .title').on('click', e => {
                if ($('.bonus-info').hasClass('show'))
                    $('.bonus-info').removeClass('show')
                else
                    $('.bonus-info').addClass('show')
            });
            $('.btn-generate-info').on('click', e => {
                e.preventDefault();
                generateInfo();
            })
        })
    </script>
@endsection
