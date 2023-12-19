<?php
use Gomee\Helpers\Arr;

$input->prepareCrazyInput();
?>
@if($input->data)

<?php

$data = new Arr($input->data);
$docs = new Arr($input->data_docs);
$defVal = $input->defVal();
?>


<div class="row">
    @foreach ($data->all() as $value => $text)
        <?php 
            $doc = new Arr($docs->get($value)); 
            if($loop->index == 0 && (!$defVal && $defVal !== 0)){
                $defVal = $value;
            }
        ?>
        <div class="col-lg-6">
            <label class="m-option">
                <span class="m-option__control">
                    <span class="m-radio m-radio--brand m-radio--check-bold">
                        <input type="radio" name="{{$input->name}}" value="{{$value}}" @if($value == $defVal) checked @endif />
                        <span></span>
                    </span>
                </span>
                <span class="m-option__label">
                    <span class="m-option__head">
                        <span class="m-option__title">
                            {{$doc->title?$doc->title:$text}}
                        </span>
                        @if ($doc->label)
                            <span class="m-option__focus">
                                {{$doc->label}}
                            </span>    
                        @endif
                        
                    </span>
                    @if ($doc->description)
                        <span class="m-option__body">
                            {{$doc->description}}
                        </span>
                    @endif
                    
                </span>
            </label>
        </div>
    @endforeach
    
</div>
@endif
