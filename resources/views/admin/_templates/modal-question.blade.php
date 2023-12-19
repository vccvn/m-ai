<?php

?>



<div class="m-portlet d-none" id="update-question-portlet">
    <form action="{{ route('admin.questions.ajax-save') }}" method="POST" id="portlet-update-question-forn">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="flaticon-info m--icon-font-size-lg3"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                        Cập nhật câu hỏi
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="form-body"></div>
        </div>
        <div class="m-portlet__foot">
            <div class="row align-items-center">
                <div class="col-lg-6 m--valign-middle">
                    {{-- Action --}}
                </div>
                <div class="col-lg-6 m--align-right">
                    <button type="submit" class="btn btn-brand btn-save-question">Cập nhật</button>
                    <button type="button" class="btn btn-secondary btn-cancel-edit">Hủy</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="modal fade question-modal" id="question-modal" tabindex="-1" role="dialog" aria-labelledby="question-modal-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header custom-style bg-info">
                <h5 class="modal-title" id="question-modal-title">
                    <i class="fa fa-question-circle"></i>
                    Chi tiết câu hõi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.questions.ajax-save') }}" method="POST" id="update-question-forn">
                    <div class="question-form-body question-list">

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-update-question">Cập nhật</button>
                <button type="button" class="btn btn-secondary btn-cancel" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


@section('question-item')
    @php
        $inputTag = view('admin.forms.input', [
            'config' => [
                'type' => 'crazytag',
                'name' => 'tags',
                'id' => 'tags',
                '@type' => 'dynamic',
                '@search-route' => 'admin.tags.data',
                '@create-route' => 'admin.tags.create',
                '@create-field' => 'tags',
            ],
        ])->render();

    @endphp
    <div class="question-item" id="question-item">
        <input type="hidden" name="id" value="{$id}">

        <input type="hidden" name="questions[{$index}][code]" value="${code}">
        <div class="question-item-header">
            {{-- <div class="tags mb-4">
                <label for="tags">Tags:</label>
                {!! $inputTag !!}
            </div> --}}
            <div class="row mb-3">
                <div class="col-md-10 col-lg-11 name">
                    <h5>Câu hỏi: </h5>
                </div>
                <div class="col-md-2 col-lg-1 buttons">
                    <a href="javascript:;" class="btn btn-sm btn-secondary btn-remove-question" data-index="{$index}">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-6 col-md-3">
                    <label for="subject-id">Chọn môn thi</label>
                    <div class="input-wrapper">
                        {$subject_select}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label for="grade-id">Chọn Khối</label>
                    <div class="input-wrapper topic-wrapper">
                        {$grade_select}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label for="topic-id">Chọn chuyên đề</label>
                    <div class="input-wrapper topic-wrapper">
                        {$topic_select}
                    </div>
                </div>
                <div class="col-sm-6-6 col-md-3">
                    <label for="difficult-level">Độ khó</label>
                    <div class="difficult-level-select">
                        <label for="difficult-level-1">
                            <i class="fa fa-star"></i>
                        </label>
                        <input type="radio" name="difficult_level" id="difficult-level-1" class="difficult-level-1" value="1" checked>
                        @for ($i = 2; $i < 11; $i++)
                            <label for="difficult-level-{{ $i }}">
                                <i class="fa fa-star"></i>
                            </label>
                            <input type="radio" name="difficult_level" id="difficult-level-{{ $i }}" class="difficult-level-{{ $i }}" value="{{ $i }}">
                        @endfor

                    </div>

                </div>
            </div>
        </div>
        <div class="question-body" data-subject-slug="{$subject_slug}">
            <div class="question-content-area">
                <input type="hidden" name="content_hash" value="${content_hash}">
                <div class="label-block">
                    <label for="content">Câu hỏi <small>({$index})</small></label> <span class="text-error text-danger question-error">${error_message}</span>
                </div>
                <div class="content-wrapper editable-block subject-slug-data">
                    <div class="preview">
                        <div class="content">${preview}</div>
                        <div class="click-to-edit">
                            <i class="fa fa-edit"></i> Chỉnh sửa
                        </div>
                    </div>
                    <div class="editor-block">
                        <textarea name="content" id="content" rows="10" mode="math" hight="150">{$content}</textarea>
                    </div>

                </div>
            </div>

            <div class="row question-options">
                <div class="col-6 col-md-3">
                    <label for="type">Loại câu hỏi</label>
                    <div class="input-wrapper">
                        ${typeSelect}
                    </div>
                </div>
                <div class="col-6 col-md-3 simple-type-item">
                    <label for="answer-count">Số câu trả lời</label>
                    <div class="input-wrapper">
                        <select name="answer_count" id="answer-count" class="form-control m-input answer-count-select-input">
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

                    <label for="correct-answer">Chọn Đáp án</label>
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
    <div class="question-child" id="children-${childIndex}" data-index="${childIndex}">
        <input type="hidden" name="children[{$childIndex}][id]" value="${id}">

        <input type="hidden" name="children[{$childIndex}][code]" value="${code}">
        <div class="question-child-header">
            <div class="question-content-area">
                {{-- <input type="hidden" name="children[{$childIndex}][content_hash]" value="${content_hash}"> --}}
                <div class="label-block row">
                    <div class=" col-8 col-md-10 col-xl-11">

                        <label for="children-${childIndex}-content">Câu hỏi <small>(${order})</small></label> <span class="text-error text-danger question-error">${error_message}</span>

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
                        <textarea name="children[{$childIndex}][content]" id="children-${childIndex}-content" rows="10" mode="math" hight="150">{$content}</textarea>
                    </div>

                </div>
            </div>
        </div>
        <div class="question-body">
            <div class="row children-question-options">
                <div class="col-md-10 col-lg-11">
                    <div class="row mb-3">

                        <div class="col-6 col-md-3">
                            <label for="children-${childIndex}-answer-count">Số câu trả lời</label>
                            <div class="input-wrapper">
                                <select name="children[{$childIndex}][answer_count]" id="children-${childIndex}-answer-count" class="form-control m-input children-answer-count-select-input">
                                    <option value="2" class="value-2">2</option>
                                    <option value="3" class="value-3">3</option>
                                    <option value="4" class="value-4" selected>4</option>
                                    <option value="5" class="value-5">5</option>
                                    <option value="6" class="value-6">6</option>
                                    <option value="7" class="value-7">7</option>
                                    <option value="8" class="value-8">8</option>
                                    {{-- <option value="8" class="value-8">8</option>
                                <option value="8" class="value-8">8</option> --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6-6 col-md-6 col-xl-4">

                            <label for="children-${childIndex}-correct-answer">Chọn Đáp án</label>
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
        <div class="answer-inner editable-block subject-slug-data">
            <label for="answer-{$slug}">{$label}.</label>
            <div class="answer-wrapper">
                <div class="preview">
                    <div class="content">${preview}</div>

                </div>
                <div class="editor-block">
                    <textarea name="answers[{$slug}]" id="answer-{$slug}" rows="10" mode="math" hight="100">{$content}</textarea>
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
            <label for="children-${childIndex}-answer-{$slug}">{$label}.</label>
            <div class="answer-wrapper subject-slug-data">
                <div class="preview">
                    <div class="content">{$content}</div>

                </div>
                <div class="editor-block">
                    <textarea name="children[{$childIndex}][answers][{$slug}]" id="children-${childIndex}-answer-{$slug}" rows="10" mode="math" hight="100">{$content}</textarea>
                </div>

            </div>
            <div class="click-to-edit">
                <i class="fa fa-edit"></i> Chỉnh sửa
            </div>
        </div>
    </div>
@endsection


@php

    $subjectLevelLabelMap = get_subject_data_map();
    // add_js_src('static/plugins/ckeditor5/plugins/ckplugins.js');
    add_js_src('static/plugins/ckeditor5/build4/ckeditor.js');
    // add_js_src('static/plugins/ckeditor5/build/ckeditor-mathtype.js');
    add_js_src('static/manager/js/ckeditor.js');
    // add_js_src('static/plugins/ckeditor5-35/build/ckeditor.js');
    // add_js_src('static/features/questions/editor.js');

    add_js_src('static/features/questions/question-item.js?v=' . get_app_version());
    add_js_src('static/features/questions/update.js?v=' . get_app_version());
    add_css_link('static/features/questions/style.min.css?v=' . get_app_version());
    add_js_data('question_update_data', [
        'urls' => [
            'update' => route('admin.questions.ajax-save'),
            'detail' => route('admin.questions.ajax-detail'),
        ],
        'templates' => [
            'question_item' => $__env->yieldContent('question-item'),
            'answer_item' => $__env->yieldContent('answer-item'),
            'question_child' => $__env->yieldContent('question-child'),
            'answer_child' => $__env->yieldContent('answer-child'),
        ],
        'data' => [
            'subject_options' => get_subject_options(),
            'subject_topic_map' => get_subject_topic_map(),
            'subject_data_map' => $subjectLevelLabelMap,
            'questions' => [],
            'empty_question' => [
                'content' => '',
                'is_correct' => null,
                'answers' => [
                    'A' => [
                        'content' => '',
                        'is_correct' => false,
                    ],
                    'B' => [
                        'content' => '',
                        'is_correct' => false,
                    ],
                    'C' => [
                        'content' => '',
                        'is_correct' => false,
                    ],
                    'D' => [
                        'content' => '',
                        'is_correct' => false,
                    ],
                ],
            ],
        ],
    ]);

@endphp
