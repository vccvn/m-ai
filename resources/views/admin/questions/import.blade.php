@php
$subjectOptions = get_subject_options();
$topicMap = get_subject_topic_map();
$subjectDataMap = get_subject_data_map();
@endphp
@extends($_layout . 'main')
{{-- khai báo title --}}
@section('title', 'Nhập câu hỏi')
{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', 'Ngân hàng câu hỏi')

{{-- Nội dung --}}
@section('content')


<form action="{{ route('admin.questions.import.save') }}" id="import-question-form">
    <!--Begin::Main Portlet-->
    <div class="m-portlet m-portlet--full-height bg-light-gray">

        <!--begin: Portlet Head-->
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Nhập câu hỏi
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


            <div class="import-area">
                <div class="import-step step--1">
                    <div class="step-header">
                        <h3>Bước 1: Phân tích file đề thi tự động</h3>
                        <p>
                            <a href="#" class="color-gray">Hướng dẫn định dạng file word đề thi chuẩn</a>
                        </p>
                    </div>
                    <div class="step-body">
                        <label for="" class="form-label">Tải lên file word đề thi theo định dạng chuẩn và chuyển sang bước 2</label>
                        <div class="upload-group">
                            <div class="first-step">
                                <div class="subject-topic-area">
                                    <div class="row">
                                        <div class="col-3 sunject-selection">
                                            <label for="input-subject-select">Môn thi</label>
                                            <select name="subject_id" id="input-subject-select" class="form-control m-input">
                                                <option value="">Chọn môn thi</option>
                                                @foreach ($subjectOptions as $value => $text)
                                                <option value="{{ $value }}">{{ $text }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-3 grade-selection">
                                            <label for="input-grade-select">Khối / dạng đề</label>
                                            <select name="grade_id" id="input-grade-select" class="form-control m-input">
                                                <option value="">Chọn khối</option>
                                                {{-- @if ($subjectOptions && ($firstSubject = array_key_first($subjectOptions)) && $subjectDataMap && array_key_exists($firstSubject, $subjectDataMap) && ($fs = $subjectDataMap[$firstSubject]) && array_key_exists('grades', $fs) && ($grades = $fs['grades']))

                                                @foreach ($grades as $slug => $grade)
                                                <option value="{{ $grade['id'] }}" @if ($loop->last) selected @endif>{{ $grade['name'] }}</option>
                                                @endforeach

                                                @endif --}}
                                            </select>
                                        </div>

                                        <div class="col-3 topic-selection">
                                            <label for="input-topic-select">Chuyên đề</label>
                                            <select name="topic_id" id="input-topic-select" class="form-control m-input">
                                                <option value="">Chọn chuyên đề</option>
                                                {{-- @if ($subjectOptions && ($firstSubject = array_key_first($subjectOptions)) && $topicMap && array_key_exists($firstSubject, $topicMap) && ($topicOptions = $topicMap[$firstSubject]))

                                                @foreach ($topicOptions as $value => $text)
                                                <option value="{{ $value }}">{{ $text }}</option>
                                                @endforeach

                                                @endif --}}
                                            </select>
                                        </div>
                                        <div class="col-3 difficult-level-selection">
                                            <label for="input-difficult-level-select">Độ khó</label>
                                            <select name="difficult_level" id="input-difficult-level-select" class="form-control m-input">
                                                <option value="">Chọn độ khó</option>
                                                {{-- @if ($subjectOptions && ($firstSubject = array_key_first($subjectOptions)) && $subjectDataMap && array_key_exists($firstSubject, $subjectDataMap) && ($fs = $subjectDataMap[$firstSubject]) && array_key_exists('max_level', $fs) && ($max_level = $fs['max_level']) && array_key_exists('level_labels', $fs) && ($level_labels = $fs['level_labels']))
                                                @for ($i = 1; $i <= $max_level; $i++) <option value="{{ $i }}">{{ $level_labels[$i]??'' }}</option>

                                                    @endfor

                                                    @endif --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-dropzone question-dropzone m-dropzone--secondary" action="{{ route('admin.questions.do-upload') }}" id="upload-doc-form">
                                    @csrf
                                    <div class="m-dropzone__msg dz-message needsclick">
                                        <div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
                                        <h3 class="m-dropzone__msg-title">Kéo thả file vào đây hoặc click để chọn file upload.</h3>
                                        <span class="m-dropzone__msg-desc">Chọn một tải liệu mỗi lần</span>
                                    </div>
                                </div>
                            </div>

                            <div class="file-upload-list">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="import-step step--2">
                    <div class="step-header">
                        @php
                        $firstSubject = 0;
                        @endphp
                        <div class="row first-data">
                            <div class="col-md-6 col-lg-7 col-xl-8">
                                <h3>Bước 2: Chỉnh sửa câu hỏi thủ công</h3>
                                <p>
                                    <a href="#" class="color-gray">Hướng dẫn nhập đề thi thủ công</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="step-body">
                        <h3 class="list-title text-center">BIỂU MẪU NHẬP ĐỀ THI</h3>
                        <div class="question-list mt-4">

                        </div>
                    </div>
                </div>
            </div>



            <div class="buttons mt-5 pb-3 text-center">
                <button type="submit" class="btn btn-info">Lưu</button>
            </div>

        </div>

        <!--end: Portlet Body-->
    </div>

    <!--End::Main Portlet-->

</form>

@endsection

@section('file-upload-item')
<div class="file-upload-item" id="{$id}">
    <div class="thumbnail">
        <i class="fa fa-file-word"></i>
    </div>
    <div class="detail">
        <div class="info1">
            <div class="filename"><a target="_blank" href="/static/contents/exams/assets/${uuid}" class="btn-view-source">{$original_filename}</a></div>
            <a href="javascript:;" class="btn-remove-file-item btn btn-sm btn-secondary" data-id="{$id}">
                <i class="fa fa-trash"></i>
            </a>
        </div>
        <div class="progress m-progress--sm">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
        </div>
        <div class="info row">
            <div class="col-6">{$size}{$size_unit} / {$size}{$size_unit}</div>
            <div class="col-6 text-right"><span class="action-label">Đang phân tích...</span> <span class="analytic-progress">0%</span></div>
        </div>
    </div>
</div>
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
<div class="question-item m-portlet" id="question-item-{$index}" data-index="{$index}" data-doc-id="{$doc_id}">
    <input type="hidden" name="questions[{$index}][code]" value="${code}">
    <input type="hidden" name="questions[{$index}][point]" value="${point}">

    <input type="hidden" name="questions[{$index}][priority]" value="${priority}">
    <div class="question-item-header">
        <div class="row mb-3">
            <div class="col-9 col-md-10 col-lg-11 name">
                <h5>Câu hỏi ${order}: </h5>
            </div>
            <div class="col-3 col-md-2 col-lg-1 buttons">
                <a href="javascript:;" class="btn btn-sm btn-secondary btn-remove-question" data-index="{$index}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
        {{-- <div class="tags mb-3">
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
                        <option value="9" class="value-9">7</option>
                        <option value="10" class="value-8">10</option>
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
    {{-- <input type="hidden" name="questions[{$index}][children][{$childIndex}][id]" value="${id}"> --}}
    <input type="hidden" name="questions[{$index}][children][{$childIndex}][code]" value="${code}">
    <input type="hidden" name="questions[{$index}][children][{$childIndex}][point]" value="${point}">
    <input type="hidden" name="questions[{$index}][children][{$childIndex}][priority]" value="${priority}">
    <div class="question-child-header">
        <div class="row mb-3">
            <div class="col-8 col-md-10 col-lg-11 name">
                <h5>Câu hỏi ${order}: </h5>
            </div>
            <div class="col-4 col-md-2 col-xl-1 buttons text-right">
                <a href="javascript:;" class="btn-remove-children-question" data-index="{$childIndex}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
        <div class="question-content-area">
            <input type="hidden" name="questions[{$index}][children][{$childIndex}][content_hash]" value="${content_hash}">
            <div class="label-block">

                <label for="question-{$index}-children-${childIndex}-content">nội dung</label> <span class="text-error text-danger question-error">${error_message}</span>


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
                                <option value="9" class="value-7">9</option>
                                <option value="10" class="value-8">10</option>
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
add_js_src('static/plugins/ckeditor5/build4/ckeditor.js?V=' . get_app_version());
add_js_src('static/manager/js/ckeditor.js');

add_js_src('static/features/questions/question-item.js?v=' . get_app_version());
add_js_src('static/features/questions/import-wizard.js?v=' . get_app_version());
add_css_link('static/features/questions/style.min.css?V=' . get_app_version());
add_js_data('question_importer_data', [
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
'subject_data_map' => $subjectDataMap,
],
]);

@endphp
