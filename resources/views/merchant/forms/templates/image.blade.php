<?php
set_merchant_template_data('modals', 'modal-library');
$input->type = 'text';
$input->addClass('color-picker form-control');
$val = $input->val();
if($val){
    $input->style = "color: $val";
}
?>

    <div class="input-group">
    
        {!! $input !!}
        <div class="input-group-append">
            <button type="button" class="btn btn-info color-picker-btn" data-input-id="{{$input->id }}" data-change-color-selector="#{{$input->id }}" id="button-group-{{$input->id }}"><i class="fa fa-paint-brush"></i></button>
        </div>
    

    </div>