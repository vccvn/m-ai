<?php
// su dung thu vien
use Gomee\Helpers\Arr;
use Gomee\Html\HTML;
use Gomee\Html\Form;
use Gomee\Html\Input;

// cac bien
// form config
$cfg = new Arr($config??[]);
$form = new Form([
	'inputs' => $inputs??[],
	'data' => $data??[],
	'errors' => $errors
], ['className'=>'form-control m-input'], []);
$form->query(['type' => ['radio', 'checkbox', 'crazyselect', 'file']])->map('removeClass', ['form-control', 'm-input']);
$form->query(['type' => 'checkbox'])->map('setOption', 'label_class', 'm-checkbox');
$form->query(['type' => 'radio'])->map('setOption', 'label_class', 'm-radio');
// dd($form);

$layout_column = ($cfg->layout_type == 'column');
?>


<!--begin::Form-->

    <div class="form-inputs">
        <div class="row {{$cfg->form_group_style ==  'custom' ? '': ' group form-group m-form__group pl-0 pr-0'}}">
            @if (is_array($cfg->form_groups) && count($cfg->form_groups))
                @foreach ($cfg->form_groups as $column)
                    @php $group = new Arr($column); @endphp
                    <div class="col-12 {{$group->class}}">
                        @include($_base.'forms.master-inputs', [
                            'list'=>$form->notInGroup($group->inputs),
                            'group' => $group,
                            'group_title' => $group->title,
                            'layout_type' => $cfg->layout_type,
                            'cfg' => $cfg
                        ])
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    @include($_base.'forms.master-inputs', [
                        'list'=>$form->notInGroup(),
                        'group' => new Arr(),
                        'layout_type' => $cfg->layout_type,
                        'cfg' => $cfg
                    ])
                </div>
            @endif
        </div>

    </div>
