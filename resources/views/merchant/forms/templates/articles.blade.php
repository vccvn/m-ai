<?php

$def = $input->defVal();
// $options = $input->data;
$defaultValues = old($input->name, array_merge((array) $input->data, (array) $def));

$wrapper = $input->copy();
$wrapper->removeClass()->addClass('input-article-wrapper');
$wrapper->name = null;
$wrapper->type = null;
$wrapper->placehoder = null;
$wrapper->id = $wrapper->id . '-wrapper';

$inputGroup = crazy_arr($defaultValues);
$maxIndex = count($defaultValues);
$wrapper->data('max-index', $maxIndex);
?>


<div {!! $wrapper->attrsToStr() !!}>

    <div class="list">
        @foreach ($defaultValues as $item)
            <div class="article-item item-{{ $loop->index }}">
                <div class="row mb-2">
                    <label for="{{ $input->name . '-' . $loop->index . '-' . 'title' }}" class="col-label col-10 col-lg-11">Tiêu đề</label>
                    <div class="col-2 col-xl-1 text-right">
                        <a href="javascript:void(0)l" class="btn btn-sm btn-secondary btn-remove-article">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" name="{{ $input->name . '[' . $loop->index . ']' . '[title]' }}" id="{{ $input->name . '-' . $loop->index . '-' . 'title' }}" class="form-control m-input" placeholder="Nhập tiêu đề" value="{{ $item['title'] ?? '' }}">
                </div>

                <label for="{{ $input->name . '-' . $loop->index . '-' . 'content' }}">Nội dung</label>
                <div class="">
                    <textarea name="{{ $input->name . '[' . $loop->index . ']' . '[content]' }}" id="{{ $input->name . '-' . $loop->index . '-' . 'content' }}" cols="30" rows="10" class="form-control m-input" placeholder="Nhập Nội dung">{{ $item['content'] ?? '' }}</textarea>
                </div>
            </div>
        @endforeach
    </div>
    <div class="buttoms">
        <a href="#" class="btn btn-info btn-block btn-add-article">Thêm Nội dung</a>
    </div>

    <script type="text/template">
        <div class="article-item item-{$index}">
            <div class="row mb-2">
                <label for="{{$input->name . '-{$index}-title'}}" class="col-label col-10 col-lg-11">Tiêu đề</label>
                <div class="col-2 col-xl-1 text-right">
                    <a href="javascript:void(0)l" class="btn btn-sm btn-secondary btn-remove-article">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="text" name="{{$input->name . '[{$index}][title]'}}" id="{{$input->name . '-{$index}-title'}}" class="form-control m-input" placeholder="Nhập tiêu đề" value="">
            </div>

            <label for="{{$input->name . '-{$index}-' . 'content'}}">Nội dung</label>
            <div class="">
                <textarea name="{{$input->name . '[{$index}][content]'}}" id="{{$input->name . '-{$index}-content'}}" cols="30" rows="10" class="form-control m-input" placeholder="Nhập Nội dung"></textarea>
            </div>
        </div>
    </script>
</div>
@php
    add_js_src('static/features/forms/articles/script.js');
    add_css_link('static/features/forms/articles/style.min.css');

@endphp
