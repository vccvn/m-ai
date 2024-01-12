@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', 'Nhập prompt')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Nhập prompt')


@section('content')



    <div class="row">
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
                    <div class="m-section m-section--last">
                        <form action="" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="topic_id">Chọn chủ đề</label>
                                <div>
                                    @include('admin.forms.templates.crazyselect', [
                                        'inpuit' => html_input([
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
                            </div>
                            <div class="form-group">
                                <label for="import_file">Chọn file</label>
                                <div>
                                    @include('admin.forms.templates.file', [
                                        'inpuit' => html_input([
                                            'type' => 'file',
                                            'label' => 'Import File',
                                            'id' => 'import_file',
                                            'required' => 'true',
                                            'name' => 'import_file'
                                        ]),
                                    ])
                                </div>
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
