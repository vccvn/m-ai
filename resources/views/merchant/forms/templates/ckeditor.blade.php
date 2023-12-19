<?php
// add_js_src('static/plugins/ckeditor5/plugins/ckplugins.js');
add_js_src('static/plugins/ckeditor5/build4/ckeditor.js?v=' . get_app_version());
add_js_src('static/manager/js/ckeditor.js?v=' . get_app_version());


$input->addClass("crazy-ckeditor");
$input->type="textarea";

add_js_data('CKEDITOR_DATA', [
    'urls' => [
        'upload' => route('merchant.files.media.ck-upload')
    ]
]);
?>

@if (in_array($input->attr('mode'), ['inline', 'balloon', 'word', 'clean']) && ($wrapper = $input->copy()))
    @php
        $wrapper->id.="-editor";
        $wrapper->data('input-id', $input->id);
        $wrapper->data('wrapper-id', $wrapper->id . '-wrapper');
        $wrapper->addClass('editor');
        $input->removeClass('crazy-ckeditor')->addClass('hidden-input');
        add_css_link('static/plugins/ckeditor5/css/styles.css');
    @endphp
    <style>
        ck-editor{
            display: block;
        }
        .hidden-input{
            display: none;
        }
    </style>
    <div class="ckeditor-section ck-mode-{{$input->attr('mode')}}" id="{{$wrapper->id.'-wrapper'}}">
        <div class="cke-row">
            <div class="document-editor__toolbar"></div>
        </div>
        <div class="cke-row row-editor" @if (is_numeric($h = $wrapper->attr('height')) && $h > 0)
            style="max-height: {{$h}}px"
        @endif>
            <div class="editor-container">
                <ck-editor {!! $wrapper->attrsToStr() !!}>
                    {!! $input->val() !!}
                </ck-editor>
            </div>
        </div>
        {!! $input !!}
    </div>
@else

    {!! $input !!}
@endif
