@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', 'Nhập prompt')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Nhập prompt')


@section('content')



    <div class="row mt-4 mt-5 pt-4">
        <div class="col-sm-8 col-md-7 col-lg-6 ml-auto mr-auto">

            <!--begin::Portlet-->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Nhập liệu
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">

                    <!--begin::Section-->
                    <div class="m-section m-section--last ">
                        <form action="{{route('admin.gpt.prompts.import')}}" method="POST" enctype="multipart/form-data">
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
                                <label for="import_file">Chọn file</label>
                                <div>
                                    @include('admin.forms.templates.file', [
                                        'input' => html_input([
                                            'type' => 'file',
                                            'label' => 'Import File',
                                            'id' => 'import_file',
                                            'required' => 'true',
                                            'name' => 'import_file',
                                            'accept'=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                        ]),
                                    ])
                                </div>
                                @if ($em = $errors->first('import_file'))
                                    <div class="text-danger">
                                        {{$em}}
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3 pt-3 text-center">
                                <button type="submit" class="btn btn-info">Bắt đầu</button>
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
