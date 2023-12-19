<?php
use Gomee\Html\Input;
use Gomee\Helpers\Arr;

add_js_src('static/crazy/js/attribute.js');
add_css_link('/static/plugins/coloris/coloris.min.css');
add_js_src('/static/plugins/coloris/coloris.min.js');
add_js_src('/static/features/common/common.js');
add_css_link('/static/features/common/common.min.css');

$wrapper = $input->copy();
$wrapper->type = "attribute";
$wrapper->prepareCrazyInput();

$inputParams = $wrapper->getInputData(false);
$attributeGroupLabels = [
    'attributes' => 'Thuộc tính',
    'variants' => 'Biến thể'
];

$wrp = $wrapper->copy();
$wrp->removeClass()->addClass("product-attributes");
$wrp->name = null;
$wrp->id = 'product-attributes';
$wrp->type = null;
$wrp->placeholder = null;
?>



<div {!! $wrp->attrsToStr() !!}>

    @foreach ($inputParams as $group => $attributes)
        
    <div class="attr-variants mb-4">
        <h6>{{$attributeGroupLabels[$group]}}</h6>
            
    
        @if ($group == 'attributes')
            @if ($attributes)
                @foreach ($attributes as $rule => $attrs)
                    <div class="m-accordion m-accordion--bordered" id="product-{{$group}}_{{$rule}}" role="tablist">
                        @foreach ($attrs as $attr)
                            <!--begin::Item-->
                            @php
                                $inputParams = $attr->toProductInputParam($wrapper->parent->hidden_id->val());
                                $inp = html_input($inputParams);
                                $attr_name = $inp->name;
                                $is_variant = $inp->hidden('is-variant');
                                $rname = $is_variant?'variants':$wrapper->name;
                                $ns = $rname . '.' . $attr_name;
                                $itemID = 'product-' . ($is_variant?'variants': 'attributes') . ($is_variant?'': '_'. (($attr->is_required?'required':'optional'))) . '_' . $attr_name;
                            @endphp
                            <div class="m-accordion__item" id="product-{{$group}}_{{$rule}}_{{$attr_name}}">
                                <div class="m-accordion__item-head collapsed" role="tab" id="{{$itemID}}_head" data-toggle="collapse" href="#{{$itemID}}_body" aria-expanded="false">
                                    {{-- <span class="m-accordion__item-icon">
                                        <i class="fa flaticon-user-ok"></i>
                                    </span> --}}
                                    <span class="m-accordion__item-title">
                                        {{$inputParams['label']??$inputParams['name']}}
                                        @if (array_key_exists('required', $inputParams) && !in_array($inputParams['required'], ["0", "false"]))
                                            <i class="m-badge m-badge--danger m-badge--dot"></i>
                                        @endif

                                        @if ($errors->has($ns))
                                            <i class="ml-3 text-danger">-- {{$errors->first($ns)}}</i>
                                        @endif

                                    </span>
                                    
                        
                                    <span class="m-accordion__item-mode"></span>
                                </div>
                                <div class="m-accordion__item-body collapse" id="{{$itemID}}_body" role="tabpanel" aria-labelledby="{{$itemID}}_head" data-parent="#m_accordion_{{$group}}" style="">
                                    <div class="m-accordion__item-content">
                                        
                                                    
                                            @include($_base.'forms.attribute-input', [
                                                'root_name' => $wrapper->name,
                                                'input' => $inp
                                            ])
                                
                                        
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        @else
            <div class="m-accordion m-accordion--bordered" id="product-variants" role="tablist">
                @if ($attributes)
                    @foreach ($attributes as $attribute)
                        <!--begin::Item-->
                        @php
                            $inputParams = $attribute->toProductInputParam($wrapper->parent->hidden_id->val());
                            $inp = html_input($inputParams);
                            $attr_name = $inp->name;
                            $is_variant = $inp->hidden('is-variant');
                            $rname = $is_variant?'variants':$wrapper->name;
                            $ns = $rname . '.' . $attr_name;
                            $itemID = 'product-' . ($is_variant?'variants': 'attributes') . ($is_variant?'': '_'. (($attr->is_required?'required':'optional'))) . '_' . $attr_name;
                        @endphp
                        <div class="m-accordion__item " id="{{$itemID}}">
                            <div class="m-accordion__item-head collapsed" role="tab" id="{{$itemID}}_head" data-toggle="collapse" href="#{{$itemID}}_body" aria-expanded="false">
                                {{-- <span class="m-accordion__item-icon">
                                    <i class="fa flaticon-user-ok"></i>
                                </span> --}}
                                <span class="m-accordion__item-title">
                                    {{$inputParams['label']??$inputParams['name']}}
                                
                                    @if ($errors->has($ns))
                                        <i class="ml-3 text-danger">-- {{$errors->first($ns)}}</i>
                                    @endif
                                </span>
                                <span class="m-accordion__item-mode"></span>
                            </div>
                            
                            <div class="m-accordion__item-body collapse" id="{{$itemID}}_body" role="tabpanel" aria-labelledby="{{$itemID}}_head" data-parent="#m_accordion_{{$group}}" style="">
                                <div class="m-accordion__item-content pb-0 pt-0 pl-0 pr-0">
                                                
                                        @include($_base.'forms.attribute-input', [
                                            'input' => $inp,
                                            'root_name' => $wrapper->name
                                        ])
                            
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
        
    </div>
    @endforeach

</div>