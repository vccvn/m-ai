@php
$subjectOptions = get_subject_options();
$topicMap = get_subject_topic_map();
$subjectLevelLabelMap = get_subject_data_map();
@endphp
@extends($_layout.'main')
{{-- khai báo title --}}
@section('title', 'Nhập câu hỏi')
{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', "Ngân hàng câu hỏi")

{{-- Nội dung --}}
@section('content')

<!--Begin::Main Portlet-->
<div class="m-portlet m-portlet--full-height m-portlet--last m-portlet--head-md m-portlet--responsive-mobile">

    <!--begin: Portlet Head-->
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    Thêm Câu hỏi
                </h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item">
                    <a href="#" data-toggle="m-tooltip" class="m-portlet__nav-link m-portlet__nav-link--icon" data-direction="left" data-width="auto" title="Get help with filling up this form">
                        <i class="flaticon-info m--icon-font-size-lg3"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!--end: Portlet Head-->

    <!--begin: Portlet Body-->
    <div class="m-portlet__body">
        <form action="{{route('admin.questions.create.save')}}" id="import-question-form">
            <div class="import-area">

                <div class="import-step step--2">
                    <div class="step-body">
                        <div class="question-list mt-4">

                        </div>
                    </div>
                </div>
            </div>


        </form>
    </div>

    <!--end: Portlet Body-->

    <div class="m-portlet__foot m-portlet__no-border">
        <div class="m-form__actions m-form__actions--solid">
            <div class="row">
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="input-group">

                        <select name="subject_id" id="input-subject-select" class="form-control m-input">
                            @foreach ($subjectOptions as $value => $text)
                            <option value="{{$value}}">{{$text}}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <input type="number" name="quantity" id="question-quantity" value="1" min="1" step="1" class="form-control m-input max-80" style="max-width: 80px">
                        </div>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary btn-add-empty-question">Thêm</button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-9 col-lg-9 text-sm-right">
                    <button type="button" class="btn btn-info btn-submit-form">
                        Lưu
                    </button>
                    <a href="{{route('admin.questions.list')}}" class="btn btn-secondary">
                        Hủy
                    </a>
                </div>


            </div>
        </div>
    </div>

</div>

<!--End::Main Portlet-->

@endsection

@section('question-item')
@php
$inputTag = view('admin.forms.input', [
'config' => [
'type' => 'crazytag',
'name' => 'aaaaaaaaaaaaaaaa',
'id' => 'iiiiiiiiiiiii',
'@type' => 'dynamic',
'@search-route' => 'admin.tags.data',
'@create-route' => 'admin.tags.create',
'@create-field' => 'tags',
],
])->render();
$itemTag = str_replace(['aaaaaaaaaaaaaaaa', 'iiiiiiiiiiiii'], ['questions[{$index}][tags]', 'question-{$index}-tags'], $inputTag);

@endphp
<div class="question-item m-portlet" id="question-item-{$index}" data-index="{$index}">

    <div class="question-item-header">
        <div class="row mb-3">
            <div class="col-9 col-md-10 col-lg-11 name">
                <h4>Câu hỏi {$order}: </h4>
            </div>
            <div class="col-3 col-md-2 col-lg-1 buttons">
                <a href="javascript:;" class="btn btn-sm btn-secondary btn-remove-question" data-index="{$index}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
        {{-- <div class="tags mb-4">
            <label for="question-{$index}-tags">Tags:</label>
            {!! $itemTag !!}
        </div> --}}
        <div class="row mb-3">
            <div class="col-sm-6 col-md-3">
                <label for="question-{$index}-subject-id">Chọn môn thi</label>
                <div class="input-wrapper">
                    {$subject_select}
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="question-{$index}-grade-id">Chọn Khối kiến thức / Dạng đề</label>
                <div class="input-wrapper topic-wrapper">
                    {$grade_select}
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="question-{$index}-topic-id">Chọn chuyên đề</label>
                <div class="input-wrapper topic-wrapper">
                    {$topic_select}
                </div>
            </div>
            <div class="col-sm-6-6 col-md-3">
                <label for="question-{$index}-difficult-level">Độ khó</label>
                <div class="difficult-level-select">
                    <label for="question-{$index}-difficult-level-1">
                        <i class="fa fa-star"></i>
                    </label>
                    <input type="radio" name="questions[{$index}][difficult_level]" id="question-{$index}-difficult-level-1" class="difficult-level-1" value="1" checked>
                    @for ($i = 2; $i < 11; $i++)

                    <label for="question-{$index}-difficult-level-{{$i}}">
                        <i class="fa fa-star"></i>
                    </label>
                    <input type="radio" name="questions[{$index}][difficult_level]" id="question-{$index}-difficult-level-{{$i}}" class="difficult-level-{{$i}}" value="{{$i}}">
                    @endfor
                </div>

            </div>
        </div>
    </div>
    <div class="question-body" data-subject-slug="{$subject_slug}">
        <div class="question-content-area">
            <input type="hidden" name="questions[{$index}][content_hash]" value="${content_hash}">
            <div class="label-block">
                <label for="question-{$index}-content">Nội dung</label> <span class="text-error text-danger question-error">${error_message}</span>
            </div>
            <div class="content-wrapper editable-block subject-slug-data">
                <div class="preview">
                    <div class="content">${preview}</div>
                    <div class="click-to-edit">
                        <i class="fa fa-edit"></i> Chỉnh sửa
                    </div>
                </div>
                <div class="editor-block">
                    <textarea name="questions[{$index}][content]" id="question-{$index}-content" rows="10" mode="math" hight="150">{$content}</textarea>
                </div>

            </div>
        </div>

        <div class="row question-options">
            <div class="col-6 col-md-3">
                <label for="question-{$index}-type">Loại câu hỏi</label>
                <div class="input-wrapper">
                    ${typeSelect}
                </div>
            </div>
            <div class="col-6 col-md-3 simple-type-item">
                <label for="question-{$index}-answer-count">Số câu trả lời</label>
                <div class="input-wrapper">
                    <select name="questions[{$index}][answer_count]" id="question-{$index}-answer-count" class="form-control m-input answer-count-select-input">

                        <option value="2" class="value-2">2</option>
                        <option value="3" class="value-3">3</option>
                        <option value="4" class="value-4" selected>4</option>
                        <option value="5" class="value-5">5</option>
                        <option value="6" class="value-6">6</option>
                        <option value="7" class="value-7">7</option>
                        <option value="8" class="value-8">8</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6-6 col-md-6 col-xl-3 simple-type-item">

                <label for="question-{$index}-correct-answer">Chọn Đáp án</label>
                <div class="input-wrapper correct-answer-list">
                    {$correct_answer_radio}
                </div>
            </div>

        </div>

        <div class="question-answer-area simple-type-item">
            <div class="row answer-list">
                {$answerList}
            </div>
        </div>

        <div class="question-children mt-3 group-type-item d-none">
            <p><strong>Danh sách câu hỏi</strong></p>
            <div class="list"></div>
            <div class="buttons mt-2">
                <a href="javascript:void(0);" class="btn btn-info btn-block btn-add-child">Thêm</a>
            </div>
        </div>


    </div>
</div>
@endsection

@section('question-child')
<div class="question-child" id="question-item-{$index}-children-${childIndex}" data-index="${childIndex}">
    <input type="hidden" name="questions[{$index}][children][{$childIndex}]id]" value="${id}">
    <div class="question-child-header">
        <div class="row mb-3">
            <div class="col-8 col-md-10 col-lg-11 name">
                <h5>Câu hỏi {$order}: </h5>
            </div>
            <div class="col-4 col-md-2 col-xl-1 buttons text-right">
                <a href="javascript:;" class="btn-remove-children-question" data-index="{$childIndex}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
        <div class="question-content-area">
            <input type="hidden" name="questions[{$index}][children][{$childIndex}][content_hash]" value="${content_hash}">
            <div class="label-block row">
                <div class=" col-8 col-md-10 col-xl-11">

                    <label for="question-{$index}-children-${childIndex}-content">Nội dung</label> <span class="text-error text-danger question-error">${error_message}</span>

                </div>
                <div class="col-4 col-md-2 col-xl-1 buttons text-right">
                    <a href="javascript:;" class="btn btn-sm btn-secondary btn-remove-children-question" data-index="{$childIndex}">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="content-wrapper editable-block subject-slug-data">
                <div class="preview">
                    <div class="content">${preview}</div>
                    <div class="click-to-edit">
                        <i class="fa fa-edit"></i> Chỉnh sửa
                    </div>
                </div>
                <div class="editor-block">
                    <textarea name="questions[{$index}][children][{$childIndex}][content]" id="question-{$index}-children-${childIndex}-content" rows="10" mode="math" hight="150">{$content}</textarea>
                </div>

            </div>
        </div>
    </div>
    <div class="question-body">
        <div class="row children-question-options">
            <div class="col-md-10 col-lg-11">
                <div class="row mb-3">

                    <div class="col-6 col-md-3">
                        <label for="question-{$index}-children-${childIndex}-answer-count">Số câu trả lời</label>
                        <div class="input-wrapper">
                            <select name="questions[{$index}][children][{$childIndex}][answer_count]" id="question-{$index}-children-${childIndex}-answer-count" class="form-control m-input children-answer-count-select-input">

                                <option value="2" class="value-2">2</option>
                                <option value="3" class="value-3">3</option>
                                <option value="4" class="value-4" selected>4</option>
                                <option value="5" class="value-5">5</option>
                                <option value="6" class="value-6">6</option>
                                <option value="7" class="value-7">7</option>
                                <option value="8" class="value-8">8</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6-6 col-md-6 col-xl-4">

                        <label for="question-{$index}-children-${childIndex}-correct-answer">Chọn Đáp án</label>
                        <div class="input-wrapper">
                            {$correct_answer_radio}
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="children-question-answer-area">
            <div class="row children-answer-list">
                {$answerList}
            </div>
        </div>
    </div>
</div>
@endsection

@section('answer-item')
<div class="col-md-6 answer-item">
    <div class="answer-inner editable-block">
        <label for="question-{$index}-answer-{$slug}">{$label}.</label>
        <div class="answer-wrapper subject-slug-data">
            <div class="preview">
                <div class="content">${preview}</div>

            </div>
            <div class="editor-block">
                <textarea name="questions[{$index}][answers][{$slug}]" id="question-{$index}-answer-{$slug}" rows="10" mode="math" hight="100">{$content}</textarea>
            </div>

        </div>
        <div class="click-to-edit">
            <i class="fa fa-edit"></i> Chỉnh sửa
        </div>
    </div>
</div>
@endsection



@section('answer-child')
<div class="col-md-6 children-answer-item">
    <div class="answer-inner editable-block">
        <label for="question-{$index}-children-${childIndex}-answer-{$slug}">{$label}.</label>
        <div class="answer-wrapper subject-slug-data">
            <div class="preview">
                <div class="content">{$content}</div>

            </div>
            <div class="editor-block">
                <textarea name="questions[{$index}][children][{$childIndex}][answers][{$slug}]" id="question-{$index}-children-${childIndex}-answer-{$slug}" rows="10" mode="math" hight="100">{$content}</textarea>
            </div>

        </div>
        <div class="click-to-edit">
            <i class="fa fa-edit"></i> Chỉnh sửa
        </div>
    </div>
</div>
@endsection


@php
set_admin_template_data('modals', 'modal-library');

// add_js_src('static/plugins/ckeditor5/plugins/ckplugins.js');
add_js_src('static/plugins/ckeditor5/build4/ckeditor.js');
// add_js_src('static/plugins/ckeditor5/build/ckeditor-mathtype.js');
add_js_src('static/manager/js/ckeditor.js?v=' . get_app_version());
// add_js_src('static/plugins/ckeditor5-35/build/ckeditor.js');
// add_js_src('static/features/questions/editor.js');

add_js_src('static/features/questions/question-item.js?v=' . get_app_version());
add_js_src('static/features/questions/create.js?v=' . get_app_version());
add_css_link('static/features/questions/style.min.css?v=' . get_app_version());
add_js_data('question_create_data', [
'urls' => [
'convert' => route('admin.questions.doc-to-html'),
'html_analytics' => route('admin.questions.html-questions'),
],
'templates' => [
'file_upload_item' => $__env->yieldContent('file-upload-item'),
'question_item' => $__env->yieldContent('question-item'),
'answer_item' => $__env->yieldContent('answer-item'),
'question_child' => $__env->yieldContent('question-child'),
'answer_child' => $__env->yieldContent('answer-child'),

],
'data' => [
'subject_options' => get_subject_options(),
'subject_topic_map' => get_subject_topic_map(),
'subject_data_map' => get_subject_data_map(),
'questions' => [

],
'empty_question' => [
'content' => '',
'is_correct' => null,
'error_message' => '',
'answers' => [
'A' => [
'content' => '',
'is_correct' => false
],
'B' => [
'content' => '',
'is_correct' => false
],
'C' => [
'content' => '',
'is_correct' => false
],
'D' => [
'content' => '',
'is_correct' => false
]
]
]
]

])


@endphp
