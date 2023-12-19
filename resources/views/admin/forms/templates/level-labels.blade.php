<?php
use Gomee\Html\Input;
use Gomee\Helpers\Arr;

$defaultLabels = [
    1=>'Level 1',
    2=>'Level 2',
    3=>'Level 3',
    4=>'Level 4',
    5=>'Level 5',
    6=>'Level 6',
    7=>'Level 7',
    8=>'Level 8',
    9=>'Level 9',
    10=>'Level 10',
];

$dataValues = old($input->name, $input->defval());
if (!is_array($dataValues)) {
    $dataValues = [];
}

?>

<div class="level-labels">
    <div class="row">
        <div class="col-md-8 col-xl-col-7">
            @foreach ($defaultLabels as $key => $label)
                <div class="row mb-2 level-group level-group-{{$key}}">
                    <label for="{{ $input->name . '-' . $key }}" class="col-label col-sm-5 col-md-3" style="padding: 0.85rem 1.15rem;">Level {{ $key }}</label>
                    <div class="col-sm-7 col-md-9">
                        <input type="text" name="{{ $input->name }}[{{ $key }}]" id="{{ $input->name . '-' . $key }}" class="form-control m-inpit" value="{{ $dataValues[$key] ?? $label }}">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
