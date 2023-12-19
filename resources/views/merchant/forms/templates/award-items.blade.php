<?php

$def = $input->defVal();
// $options = $input->data;
$defaultValues = old($input->name, array_merge((array) $input->data, (array) $def));

$wrapper = $input->copy();
$wrapper->removeClass()->addClass('input-award-items-wrapper');
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
            <div class="award-item item-{{ $loop->index }}">
                <div class="row mb-3">
                    <label for="{{ $input->name . '-' . $loop->index . '-' . 'order' }}" class="col-form-label col-4 col-lg-3">Thứ hạng</label>
                    <div class="col-3">
                        <div class="input-group">
                            <input type="number" min="1" max="100" step="1" name="{{ $input->name . '[' . $loop->index . ']' . '[order]' }}" id="{{ $input->name . '-' . $loop->index . '-' . 'order' }}" class="form-control m-input" placeholder="Nhập thứ hạng" value="{{ $item['order'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-5 col-xl-6 text-right">
                        <a href="javascript:void(0)l" class="btn btn-sm btn-secondary btn-remove-award-item">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    @if ($errors->has($input->name . '.' . $loop->index . '.' . 'order'))
                        <div class="col-3"></div>
                        <div class="col-9">
                            <div class="error">
                                {{ $errors->first($input->name . '.' . $loop->index . '.' . 'order') }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    <label for="{{ $input->name . '-' . $loop->index . '-' . 'discount-value' }}" class="col-form-label col-4 col-lg-3">Chiết khấu</label>
                    <div class="col-3">
                        <div class="input-group">
                            <input type="number" min="0" max="100" step="1" name="{{ $input->name . '[' . $loop->index . ']' . '[discount_value]' }}" id="{{ $input->name . '-' . $loop->index . '-' . 'discount-value' }}" class="form-control m-input" placeholder="Nhập chiết khấu" value="{{ $item['discount_value'] ?? '' }}">

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    %
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($errors->has($input->name . '.' . $loop->index . '.' . 'discount_value'))
                        <div class="col-12"></div>
                        <div class="col-4 col-lg-4"></div>
                        <div class="col-8 col-lg-9">
                            <div class="error">
                                {{ $errors->first($input->name . '.' . $loop->index . '.' . 'discount_value') }}
                            </div>
                        </div>
                    @endif
                </div>


                <div class="row">
                    <label for="{{ $input->name . '-' . $loop->index . '-' . 'description' }}" class="col-form-label col-4 col-lg-3">Mô tả giải thưởng</label>
                    <div class="col-8 col-lg-9">
                        <div class="input-group">
                            <textarea name="{{ $input->name . '[' . $loop->index . ']' . '[description]' }}" id="{{ $input->name . '-' . $loop->index . '-' . 'description' }}" cols="30" rows="10" class="form-control m-input" placeholder="Nhập Nội dung">{{ $item['description'] ?? '' }}</textarea>
                        </div>
                    </div>


                    @if ($errors->has($input->name . '.' . $loop->index . '.' . 'description'))
                        <div class="col-4 col-lg-4"></div>
                        <div class="col-8 col-lg-9">
                            <div class="error">
                                {{ $errors->first($input->name . '.' . $loop->index . '.' . 'description') }}
                            </div>
                        </div>
                    @endif
                </div>


            </div>
        @endforeach
    </div>
    <div class="buttoms">
        <a href="#" class="btn btn-info btn-block btn-add-award-item">Thêm phần thưởng</a>
    </div>

    <script type="text/template">
        <div class="award-item item-{$index}">

            <div class="row mb-3">
                <label for="{{$input->name . '-{$index}-' . 'order'}}" class="col-form-label col-4 col-lg-3">Thứ hạng</label>
                <div class="col-3">
                    <div class="input-group">
                        <input type="number" min="1" max="100" step="1" name="{{$input->name . '[{$index}]' . '[order]'}}" id="{{$input->name . '-{$index}-' . 'order'}}" class="form-control m-input" placeholder="Nhập thứ hạng" value="">
                    </div>
                </div>
                <div class="col-5 col-xl-6 text-right">
                    <a href="javascript:void(0)l" class="btn btn-sm btn-secondary btn-remove-award-item">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>

            <div class="row mb-3">
                <label for="{{$input->name . '-{$index}-' . 'discount-value'}}" class="col-form-label col-4 col-lg-3">Chiết khấu</label>
                <div class="col-3">
                    <div class="input-group">
                        <input type="number" min="0" max="100" step="1" name="{{$input->name . '[{$index}]' . '[discount_value]'}}" id="{{$input->name . '-{$index}-' . 'discount-value'}}" class="form-control m-input" placeholder="Nhập chiết khấu" value="">

                        <div class="input-group-append">
                            <span class="input-group-text">
                                %
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <label for="{{$input->name . '-{$index}-' . 'description'}}" class="col-form-label col-4 col-lg-3">Mô tả phần thưởng</label>
                <div class="col-8 col-lg-9">
                    <div class="input-group">
                        <textarea name="{{$input->name . '[{$index}]' . '[description]'}}" id="{{$input->name . '-{$index}-' . 'description'}}" cols="30" rows="10" class="form-control m-input" placeholder="Nhập Nội dung"></textarea>
                    </div>
                </div>
            </div>







        </div>
    </script>
</div>
@php
    add_js_src('static/features/forms/award-items/script.js');
    add_css_link('static/features/forms/award-items/style.min.css');

@endphp
