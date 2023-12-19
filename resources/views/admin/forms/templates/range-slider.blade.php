@php
    $range = [0, 0];
    if(is_array($rangeDef = $input->hidden('range'))){
        $range = [
            $rangeDef['from']?? ($rangeDef[0]??0),
            $rangeDef['to']?? ($rangeDef[1]??0)
        ];
    }
    $val = $input->val();
    $from = 0;
    $to = 0;
    if($val){
        if(is_array($val)){
            $from = $val['from']??($val[0]??0);
            $to = $val['to']??($val[1]??1);
        }
        elseif(count($ep = explode(';', $val)) == 2){
            $from = $ep[0];
            $to = $ep[1];
        }
    }

add_js_src('static/features/range-slider/script.js');
@endphp
<div class="m-ion-range-slider crazy-range-slider" data-min="{{$range[0]}}" data-max="{{$range[1]}}" data-from="{{$from}}" data-to="{{$to}}" data-prefix="{{$input->hidden('prefix')}}">
    <input type="hidden" id="{{$input->id}}" name="{{$input->name}}" value="{{$from}};{{$to}}" class="hidden-range-input" />
</div>
