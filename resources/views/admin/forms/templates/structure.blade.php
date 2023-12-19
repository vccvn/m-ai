<?php
use Gomee\Html\Input;
use Gomee\Helpers\Arr;
$subjectDataMap = get_subject_data_map();
// add_js_src('static/crazy/js/specification.js');
// $input->type = "text";

$wrapper = $input->copy();

$wrapper->removeClass();
$wrapper->addClass("strucure-wrapper input-structure");
$wrapper->id.='-wrapper';
$wrapper->name.='-wrapper';

$data = $input->defval();
if(!is_array($data)) $data = [];
$maxIndex = -1;
?>

<div {!! $wrapper->attrsToStr() !!}>
    <div class="list">

    </div>
    <div class="buttons">

    </div>
    <script type="text/template" class="subject-template-struct">
        <div class="subject-block">
            <input type="hidden" name="">
            <div class="block-header">
                <div class="row">
                    <div class="col-5 col-md-5 col-lg-4 col-xl-3">
                        <label for="structure-data-{$index}-subject-id">Môn thi</label>
                        <select name="structure[${index}][subject_id]" id="structure-data-{$index}-subject-id"></select>
                    </div>
                    <div class="col-5 col-md-5 col-lg-4 col-xl-3"></div>
                    <div class="col-2 col-md-2 col-lg-4 col-xl-6 text-right"></div>

                </div>
            </div>
        </div>
    </script>
    <script type="application/ld+json" class="input-data">{!! json_encode($item) !!}</script>
</div>
