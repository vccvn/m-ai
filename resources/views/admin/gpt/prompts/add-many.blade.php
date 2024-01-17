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
                        <form action="{{ route('admin.gpt.prompts.add-many') }}" method="POST" id="add-many-form">
                            @csrf
                            <div class="form-group">
                                <label for="prompts">Danh sách prompt</label>
                                <div class="input-group">
                                    <textarea name="prompts" id="prompts" cols="30" rows="50" class="form-control m-input" placeholder="Nhập nội dung prompt" style="height: 500px;"></textarea>
                                </div>
                            </div>

                            <div class="buttons text-right">
                                <a href="#" class="btn btn-primary btn-sm btn-generate-info">Generate</a>
                            </div>

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

                            <div class="prompt-list">

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
    <script type="text/template" id="prompt-input-template">
        <div class="prompt-group">
            <div class="form-group">
                <label for="prompt-name-{$i}">Tên prompt (tuỳ chọn)</label>
                <div class="input-group">
                    <input type="text" name="prompts[{$i}][name]" id="prompt-name-{$i}" class="form-control m-input inp-prompt-name" placeholder="ví dụ: Tảo bảng thống kê" value="{$name}">
                </div>
            </div>
            <div class="form-group">
                <label for="prompt-description-{$i}">Mô tả (Tuỳ chọn)</label>
                <div class="input-group">
                    <textarea name="prompts[{$i}][description]" id="prompt-description-{$i}" cols="30" rows="10" class="form-control m-input" placeholder="Nhập nội dung Mô tả...">{$description}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="prompt-content-{$i}">Nội dung prompt</label>
                <div class="input-group">
                    <textarea name="prompts[{$i}][prompt]" id="prompt-content-{$i}" cols="30" rows="10" class="form-control m-input" placeholder="Nhập nội dung prompt"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="prompt-placeholder-{$i}">Placeholder</label>
                <div class="input-group">
                    <input type="text" name="prompts[{$i}][placeholder]" id="prompt-placeholder-{$i}" class="form-control m-input" placeholder="Nhập nội dung placeholder" value="{$placeholder}">
                </div>
            </div>

            <div class="mb-3" style="border-top: 0.5px dotted silver;height: 2px;"></div>

        </div>
    </script>
    <script>
        $(function() {
            let promptCount = {{ $promptCount }};
            let $topicId = $('#topic_id');
            let $inputPrompts = $('textarea#prompts');
            let prommptGroupTemplate = $('#prompt-input-template').html();
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
                let promptContent = $inputPrompts.val();
                if (promptContent.trim() == '') return App.Swal.warning('Vui lòng nhập danh sách');
                App.Swal.showLoading();
                App.api.post("{{ route('admin.gpt.prompts.analytics-list') }}", {
                        prompts: promptContent
                    })
                    .then(rs => {
                        App.Swal.hideLoading();
                        if (rs.status) {
                            let data = rs.data;
                            if (data && data.length) {
                                let i = -1;
                                let pl = data.map(p => {
                                    i++;
                                    p.i = i;
                                    App.str.eval(prommptGroupTemplate, p)
                                }).join('');
                                $('.prompt-list').html(pl);
                            }
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
                let fd = new FormData(this);
                let topic_id = $topicId.val();
                if (!topic_id || topic_id == 0 || topic_id == "0")
                    App.Swal.warning("Vui lòng chọn chủ đề");
                else if (!prompt_content || prompt_content.trim() == "")
                    App.Swal.warning("Vui lòng nhập nội dung prompt");
                else {
                    App.Swal.showLoading();
                    App.api.post("{{ route('admin.gpt.prompts.add-many') }}", fd)
                        .then(rs => {
                            if (rs.status) {

                                App.Swal.hideLoading();
                                // App.Swal.success("Đã thêm mới prompt thành công");

                                toastr.success("Đã thêm " + rs.data.success + " prompt thành công");
                                let $inpName = $('.inp-prompt-name');
                                if ($inpName.length == rs.data.success)
                                    $('.prompt-list').html('');
                                else{
                                    $inpName.each(function(i, el){
                                        try {
                                            rs.data.prompts.map(prompt => prompt.name == $(el).val() ? $(el).closest('.prompt-group').remove(): '')
                                        } catch (error) {

                                        }

                                    })
                                }

                                if(rs.data.failed > 0){
                                    App.Swal.warning('Có ' + rs.data.failed + " chưa dc thêm")
                                }

                                promptCount + rs.data.success;
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
