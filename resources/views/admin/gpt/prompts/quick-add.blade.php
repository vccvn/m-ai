@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', 'Thêm nhanh')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Thêm nhanh')


@section('content')



    <div class="row mt-4 mt-5 pt-4">
        <div class="col-sm-8 col-md-7 col-lg-6 ml-auto mr-auto">

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
                        <form action="{{route('admin.gpt.prompts.quick-add')}}" method="POST" id="quick-add-form">
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
                                        {{$em}}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="prompt-content">Nội dung prompt</label>
                                <div class="input-group">
                                    <textarea name="prompt" id="prompt-content" cols="30" rows="10" class="form-control m-input" placeholder="Nhập nội dung prompt"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="prompt-name">Tên prompt (tuỳ chọn)</label>
                                <div class="input-group">
                                    <textarea name="name" id="prompt-namr" class="form-control m-input" placeholder="ví dụ: Tảo bảng thống kê"></textarea>
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
                                    <textarea name="placeholder" id="prompt-placeholder" class="form-control m-input" placeholder="Nhập nội dung placeholder"></textarea>
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
@section('js')
    <script>
        $(function(){
            $('#quick-add-form').on('submit', function(e){
                e.preventDefault();
                // let fd = new FormData(this);
                let topic_id = $('#topic_id').val();
                let prompt_content = $('#prompt-content').val();
                let name = $('#prompt-name').val();
                let description = $('#prompt-description').val();
                let placeholder = $('#prompt-placeholder').val();
                if(!topic_id || topic_id == 0 || topic_id == "0")
                    App.Swal.warning("Vui lòng chọn chủ đề");
                else if(!prompt_content || prompt_content.trim() == "")
                    App.Swal.warning("Vui lòng nhập nội dung prompt");
                else
                    App.api.post("{{route('admin.gpt.prompts.quick-add')}}", {topic_id, name, prompt: prompt_content, description, placeholder})
                        .then(rs => {
                            if(rs.status){
                                App.Swal.success("Đã thêm mới prompt thành công");
                                $('#prompt-content').val('');
                                $('#prompt-name').val('');
                                $('#prompt-description').val('');
                                $('#prompt-placeholder').val('');
                            }else{
                                App.Swal.warning(rs.message);
                            }
                        })
                        .catch(e => {
                            App.Swal.error('Lỗi hệ thống.\n Vui lòng thử lại sau giây lát')
                            console.log(e.message)
                        })

            })
        })
    </script>
@endsection
