<?php
use Gomee\Helpers\Arr;

// $input->prepareCrazyInput();
?>
    <?php

        $opts = $input->getInputData();
        $slt = '';
        $className = $input->className??($input->class_name??($input->classname??$input->class));

        $input->attr('type', null);
        $input->tagName = 'div';
        $input->className = 'inp-radio-group crazy-radio checkbox-radio display-inline';

        $change = $input->hidden('change');
        if(!$change) $change = $input->data('on-change');
        if(!$change) $change = $input->data('change');
        if(!$change) $change = $input->attr('on-change');
        
        if ($change) {
            $input->addClass('crazy-radio-group');
            $input->attr('crazy-on-change', $change);
            $input->attr('on-change', $change);
        }
        // die('');
        
        if (is_array($opts) || is_object($opts)) {
            $properties = $input->_attrs;
            $df = $input->defVal();
            if(!$df && $df!==0){
                if(is_array($opts)){
                    foreach ($opts as $value => $text) {
                        $df = $value;
                        break;
                    }
                }
            }
            // $input->html($slt);
        }
    ?>

<div {!! $input->attrsToStr() !!}>
    @foreach ($opts as $k => $v)
        <label class="inp-label checkbox-label {{ $input->label_class }} m-radio pr-3" id="radio-label-{{$input->name . '--' . $k }}">
            <input type="radio" name="{{$input->name}}" value="{{ $k }}"  {{($df == $k) ? ' checked="checked"' : ""}} class="{{$className }}"> <span></span> <i> {{ $v }} </i>
        </label>
    @endforeach
</div>