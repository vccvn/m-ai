<?php
$input = isset($params)? html_input($params) : (isset($input)?$input:crazy_arr());
$attr_id = $input->hidden('id');
$attr_name = $input->name;

$is_variant = $input->hidden('is-variant');

$rname = $is_variant?'variants':$root_name;

$price_type = $input->hidden('price-type');
$input->id = $rname.'-'.$input->name.'-'.$attr_id;

$rname = $is_variant?'variants':$root_name;
$ns = $rname . '.' . $attr_name;
$itemID = 'product-' . ($is_variant?'variants': 'attributes') . ($is_variant?'': '_'. (($attr->is_required?'required':'optional'))) . '_' . $attr_name;
?>

<div class="m-accordion__item" id="{{$itemID}}">
    <div class="m-accordion__item-head collapsed" role="tab" id="{{$itemID}}_head" data-toggle="collapse" href="#{{$itemID}}_body" aria-expanded="false">
        {{-- <span class="m-accordion__item-icon">
            <i class="fa flaticon-user-ok"></i>
        </span> --}}
        <span class="m-accordion__item-title">
            {{$params['label']??$params['name']}}
            @if (array_key_exists('required', $params) && !in_array($params['required'], ["0", "false"]))
                <i class="m-badge m-badge--danger m-badge--dot"></i>
            @endif

            @if ($errors->has($ns))
                <i class="ml-3 text-danger">-- {{$errors->first($ns)}}</i>
            @endif

        </span>
        

        <span class="m-accordion__item-mode"></span>
    </div>
    <div class="m-accordion__item-body collapse" id="{{$itemID}}_body" role="tabpanel" aria-labelledby="{{$itemID}}_head" data-parent="#m_accordion_{{$is_variant?'variants':$root_name}}" style="">
        <div class="m-accordion__item-content">
            @include($_base.'forms.attribute-input', [
                'root_name' => $root_name,
                'input' => $input
            ])
        </div>
    </div>
</div>