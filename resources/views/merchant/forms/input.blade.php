<?php
// su dung thu vien
use Gomee\Helpers\Arr;
use Gomee\Html\HTML;
use Gomee\Html\Form;
use Gomee\Html\Input;
$inputConfig = $input ?? ($config ?? []);
if (is_array($inputConfig)) {
    $inputConfig['value'] = $value ?? ($inputConfig['value'] ?? null);
}
$inputObj = new Input($inputConfig);
$inputObj->addClass('form-control m-input');
$inputObj->prepareCrazyInput();
?>
@if (is_support_template($inputObj->template, $inputObj->type, $_base . 'form.templates.'))
    @include($_base . 'forms.templates.' . $inputObj->template, ['input' => $inputObj])
@else
    {!! $inputObj !!}
@endif
