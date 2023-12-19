<?php
use Gomee\Helpers\Arr;
$input->addClass('module-exams-table');
$input->type = 'text';
$input->addClass('color-picker form-control');
//dd($input->value);
$examContent = new Arr(old($input->name, is_array($input->value) ? $input->value : json_decode($input->value, true)));

$wrapper = $input->copy();
$maxIndex = !empty($examContent->subjects) ? max(array_keys((array) $examContent->subjects)) : 0;
$wrapper->data('max-index', $maxIndex);
$subject_options = get_subject_options();
$topic_map = get_subject_topic_map();

$subjectDataMap = get_subject_data_map();
$maxTopicIndex = -1;
$subjects = $examContent->subjects;

$baseName = $input->name;
?>

<div class="">

    <div class="mt-1  form-list-exams" data-max-index="{{ $maxIndex }}">

        <div class="append-data-table">
            @php
                $defSubject = array_key_first($subject_options);
            @endphp
            @if (!empty($examContent) && $subjects && is_countable($subjects))
                @foreach ($subjects as $key => $subjectData)
                    @php
                        $subject = new Arr($subjectData);
                        $subjectData = new Arr($subjectDataMap[$subject->subject_id ?? $defSubject] ?? []);

                    @endphp
                    @if (!empty($subject->topics))
                        <div class="subject-item-block mb-4 border p-3" id="subject-item-block-{{ $key }}">
                            <div class="row mb-3">
                                <div class="col-8 xol-sm-10 col-lg-11">
                                    <label for="input-title-text-{{ $key }}">Đề mục</label>

                                </div>
                                <div class="col-3 xol-sm-2 col-lg-1 text-right">
                                    <a href="#" class="btn btn-sm btn-secondary btn-remove-subject-block" data-ref="#subject-item-block-{{ $key }}"><i class="fa fa-trash"></i></a>
                                </div>
                                <div class="col-12 mt-1">
                                    <textarea id="input-title-text-{{ $key }}" name="{{ $input->name }}[subjects][{{ $key }}][title]" class="form-control m-input crazy-ckeditor" placeholder="Tiêu đê khối kiến thức / Môn thi">{{ $subject->title }}</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <label for="input-subject-select-{{ $key }}">Môn</label>
                                    <select name="{{ $input->name }}[subjects][{{ $key }}][subject_id]" id="input-subject-select-{{ $key }}" class="form-control m-input subject-select-input">
                                        @foreach ($subject_options as $val => $text)
                                            <option value="{{ $val }}" @if ($val == $subject->subject_id) @php
                                $defSubject = $val;
                                @endphp selected @endif>{{ $text }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4 col-md-5 col-lg-5">
                                    <label for="input-grade-select-{{ $key }}">Khối</label>
                                    <select name="{{ $input->name }}[subjects][{{ $key }}][grade_id]" id="input-grade-select-{{ $key }}" class="form-control m-input grade-select-input">
                                        <option value="0">Tất cả các khối</option>
                                        @if ($grades = $subjectData->grades)
                                            @foreach ($grades as $slug => $grade)
                                                <option value="{{ $grade['id'] }}" @if ($grade['id'] == $subject->grade_id) @php $defGrade = $grade['id']; @endphp selected @endif>{{ $grade['name'] }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-2 col-lg-2">
                                    <label for="input-point-number-{{ $key }}">Điểm mỗi câu</label>
                                    <input type="number" min="0" step="0.1" id="input-point-number-{{ $key }}" name="{{ $input->name }}[subjects][{{ $key }}][points]" value="{{ $subject->points }}" class="form-control m-input" placeholder="Tổng điểm">
                                </div>
                            </div>
                            <label for="" class="mt-2">Ma trận câu hỏi (theo số câu)</label>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-exams" style="min-width: 1000px">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="pin-left" style="min-width: 150px;">Chuyên đề</th>
                                            <th class="slabel level-1">Mức 1</th>
                                            <th class="slabel level-2">Mức 2</th>
                                            <th class="slabel level-3">Mức 3</th>
                                            <th class="slabel level-4">Mức 4</th>
                                            <th class="slabel level-5">Mức 5</th>
                                            <th class="slabel level-6">Mức 6</th>
                                            <th class="slabel level-7">Mức 7</th>
                                            <th class="slabel level-8">Mức 8</th>
                                            <th class="slabel level-9">Mức 9</th>
                                            <th class="slabel level-10">Mức 10</th>
                                            <th class="pin-right"></th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $topicIndex = max(array_keys($subject->topics ?? [])); ?>

                                    <tbody class="append-topic-{{ $key }}" data-max-index="{{ $topicIndex }}">
                                        @if (is_array($topics = $subject->topics))
                                            @foreach ($topics as $keyTopic => $topicData)
                                                @php
                                                    $topic = new Arr($topicData);
                                                    $maxTopicIndex++;
                                                @endphp
                                                <tr class="append-topic-item-{{ $key . '-' . $keyTopic }}" data-index="{{$topicIndex}}">

                                                    <th scope="row" class="pin-left" style="min-width: 150px;">
                                                        <select name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][topic_id]" id="filter-topic-id-{{ $key }}-{{ $keyTopic }}" class="form-control  topic-select-input">
                                                            <option value="0">Tất cả</option>
                                                            @if ($subject->subject_id && array_key_exists($subject->subject_id, $topic_map) && ($topic_options = $topic_map[$subject->subject_id]))
                                                                @foreach ($topic_options as $tid => $tname)
                                                                    <option value="{{ $tid }}" {{ $tid == $topic->topic_id ? 'selected' : '' }}>{{ $tname }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </th>
                                                    <td class="sinput level-1"><input type="number" min="0" class="form-control question_number question_number_first_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][first_level_question_qty]" value="{{ $topic->first_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi" pattern="[0-9]">
                                                    </td>
                                                    <td class="sinput level-2"><input type="number" min="0" class="form-control question_number question_number_second_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][second_level_question_qty]" value="{{ $topic->second_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>
                                                    <td class="sinput level-3"><input type="number" min="0" class="form-control question_number question_number_third_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][third_level_question_qty]" value="{{ $topic->third_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi">
                                                    </td>
                                                    <td class="sinput level-4"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][fourth_level_question_qty]" value="{{ $topic->fourth_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>
                                                    <td class="sinput level-5"><input type="number" min="0" class="form-control question_number question_number_fifth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][fifth_level_question_qty]" value="{{ $topic->fifth_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>
                                                    <td class="sinput level-6"><input type="number" min="0" class="form-control question_number question_number_sixth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][sixth_level_question_qty]" value="{{ $topic->sixth_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>

                                                    <td class="sinput level-7"><input type="number" min="0" class="form-control question_number question_number_sixth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][seventh_level_question_qty]" value="{{ $topic->seventh_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>
                                                    <td class="sinput level-8"><input type="number" min="0" class="form-control question_number question_number_sixth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][eighth_level_question_qty]" value="{{ $topic->eighth_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>
                                                    <td class="sinput level-9"><input type="number" min="0" class="form-control question_number question_number_sixth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][ninth_level_question_qty]" value="{{ $topic->ninth_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>
                                                    <td class="sinput level-10"><input type="number" min="0" class="form-control question_number question_number_sixth_level" name="{{ $input->name }}[subjects][{{ $key }}][topics][{{ $keyTopic }}][tenth_level_question_qty]" value="{{ $topic->tenth_level_question_qty ?? 0 }}" placeholder="Nhập số câu hỏi"></td>

                                                    <th style="min-width: 80px;" class="pin-right">
                                                        <a href="javascript:void(0)" data-original-title="Thêm" data-toggle="m-tooltip" data-topic-index="{{ $keyTopic }}" data-index="{{ $key }}" data-index-subject="{{ $key }}" data-placement="left" title="" class="text-accent btn-edit-item btn btn-outline-accent btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                                                            <i class="flaticon-add">
                                                            </i>
                                                        </a>
                                                        <a href="javascript:void(0);" data-index="{{ $key . '-' . $keyTopic }}" data-toggle="m-tooltip" data-placement="left" data-original-title="Xóa" class="btn-delete text-danger btn btn-outline-danger btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                                                            <i class="flaticon-delete-1"></i>
                                                        </a>
                                                    </th>
                                                </tr>
                                                @if ($err = $errors->first($input->name . '.subjects.'.$key.'.topics.'.$keyTopic ))
                                                    <tr>
                                                        <td colspan="12">{{$err}}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

        </div>
        <div class="add-product-block mt-3">
            <div class="input-group justify-content-center">
                <div class="input-group-append">
                    <a href="javascript:void(0);" class="btn-add-exams-item btn btn-success">
                        Thêm môn thi +
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@section('subject-item')
    <div class="subject-item-block mb-4  border p-3" id="subject-item-block-{$index_subject}">
        <div class="row mb-3">
            <div class="col-8 xol-sm-10 col-lg-11">
                <label for="input-title-text-{$index_subject}">Đề mục</label>

            </div>
            <div class="col-3 xol-sm-2 col-lg-1 text-right">
                <a href="#" class="btn btn-sm btn-secondary btn-remove-subject-block" data-ref="#subject-item-block-{$index_subject}"><i class="fa fa-trash"></i></a>
            </div>
            <div class="col-12 mt-1">
                <textarea id="input-title-text-{$index_subject}" name="{{ $input->name }}[subjects][{$index_subject}][title]" class="form-control m-input crazy-ckeditor" placeholder="Tiêu đê khối kiến thức / Môn thi"></textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-5 col-md-5 col-lg-5">
                <label for="input-subject-select-{$index_subject}">Môn</label>
                {$subject_select}
            </div>
            <div class="col-sm-4 col-md-5 col-lg-5">
                <label for="input-grade-select-{$index_subject}">Khối</label>

                {$grade_select}
            </div>
            <div class="col-sm-3 col-md-2 col-lg-2">
                <label for="input-point-number-{$index_subject}">Điểm mỗi câu</label>
                <input type="number" min="0" step="0.1" id="input-point-number-{$index_subject}" name="{{ $input->name }}[subjects][{$index_subject}][points]" value="" class="form-control m-input" placeholder="Tổng điểm">
            </div>
        </div>
        <label for="" class="mt-2">Ma trận câu hỏi (theo số câu)</label>
        <div class="table-responsive">
            <table class="table table-bordered text-center table-exams" id="table-exams-{$index_subject}" style="min-width: 1000px">
                <thead class="thead-light">

                    <tr>
                        <th class="pin-left" style="min-width: 150px;">Chuyên đề</th>
                        <th class="slabel level-1">Mức 1</th>
                        <th class="slabel level-2">Mức 2</th>
                        <th class="slabel level-3">Mức 3</th>
                        <th class="slabel level-4">Mức 4</th>
                        <th class="slabel level-5">Mức 5</th>
                        <th class="slabel level-6">Mức 6</th>
                        <th class="slabel level-7">Mức 7</th>
                        <th class="slabel level-8">Mức 8</th>
                        <th class="slabel level-9">Mức 9</th>
                        <th class="slabel level-10">Mức 10</th>
                        <th class="pin-right"></th>
                    </tr>
                </thead>
                <tbody class="append-topic-{$index_subject} append-topic" data-index="{$index_subject}">

                </tbody>
            </table>

        </div>
    </div>
@endsection
@section('topic-item')
    <tr class="append-topic-item-{$index_subject}-{$index_topic}" data-index="{$index_topic}">

        <th class="pin-left" scope="row" style="min-width: 150px;">
            {$topic_select}
        </th>
        <td class="sinput level-1"><input type="number" min="0" class="form-control question_number question_number_first_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][first_level_question_qty]" value="0" placeholder="Nhập số câu hỏi">
        </td>
        <td class="sinput level-2"><input type="number" min="0" class="form-control question_number question_number_second_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][second_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <td class="sinput level-3"><input type="number" min="0" class="form-control question_number question_number_third_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][third_level_question_qty]" value="0" placeholder="Nhập số câu hỏi">
        </td>
        <td class="sinput level-4"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][fourth_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <td class="sinput level-5"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][fifth_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <td class="sinput level-6"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][sixth_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>

        <td class="sinput level-7"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][seventh_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <td class="sinput level-8"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][eighth_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <td class="sinput level-9"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][ninth_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <td class="sinput level-10"><input type="number" min="0" class="form-control question_number question_number_fourth_level" name="{{ $input->name }}[subjects][{$index_subject}][topics][{$index_topic}][tenth_level_question_qty]" value="0" placeholder="Nhập số câu hỏi"></td>
        <th class="pin-right" style="min-width: 70px">
            <a href="javascript:void(0)" data-original-title="Thêm" data-toggle="m-tooltip" data-index="{$index_topic}" data-index-subject="{$index_subject}" data-placement="left" title="" class="text-accent btn-edit-item btn btn-outline-accent btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                <i class="flaticon-add">
                </i>
            </a>
            <a href="javascript:void(0);" data-index="{$index_subject}-{$index_topic}" data-index-subject="{$index_subject}" data-toggle="m-tooltip" data-placement="left" data-original-title="Xóa" class="btn-delete text-danger btn btn-outline-danger btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                <i class="flaticon-delete-1"></i>
            </a>
        </th>
    </tr>
@endsection



@php

    set_admin_template_data('modals', 'modal-library');
    add_js_src('static/manager/js/exams.form.js?V=' . get_app_version());
    add_js_data('exams_create_data', [
        'data' => [
            'max_topic_index' => $maxTopicIndex,
            'subject_options' => get_subject_options(),
            'subject_topic_map' => get_subject_topic_map(),
            'subject_data_map' => $subjectDataMap,
            'empty_exam' => [],
        ],
        'templates' => [
            'subject_item' => $__env->yieldContent('subject-item'),
            'topic_item' => $__env->yieldContent('topic-item'),
        ],
    ]);

@endphp
