<?php
$sec_class = isset($section_class)?$section_class:null;
$att_class = isset($attribute_class)?$attribute_class:null;
$atn_class = isset($attribute_name_class)?$attribute_name_class:null;
$avl_class = isset($value_list_class)?$value_list_class:null;
$avi_class = isset($value_item_class)?$value_item_class:null;
$sel_class = isset($select_class)?$select_class:null;
$img_class = isset($image_class)?$image_class:null;
$txt_class = isset($value_text_class)?$value_text_class:null;
$rad_class = isset($radio_class)?$radio_class:null;
$lab_class = isset($label_class)?$label_class:(isset($value_label_class)?$value_label_class:null);

$sle_class = isset($select_class)?$select_class:null;
?>
<div class="crazy-product-detail attribute-wrapper">

    
    @if ($variant_attributes = $attributes)

        <ul class="attribute-nav mobile d-md-none mt-20 mb-0">
            @foreach ($variant_attributes as $attribute)
                <li>
                    <a href="#attr-{{$attribute->name}}-{{$attribute->id}}" class="attr-nav-item">{{$attribute->label}}</a>
                </li>
            @endforeach
        </ul>
        
        <div class="{{$sec_class}} {{parse_classname('product-variants')}}">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xxl-4">
                @foreach ($variant_attributes as $attribute)
                    <?php
                        $avt = $attribute->advance_value_type;
                    ?>
                    <figure class="{{$att_class}} {{parse_classname('product-attribute-item','product-variant-'.$avt,'product-attribute-'.$avt)}} attribute-item-block" id="attr-{{$attribute->name}}-{{$attribute->id}}">
                        <figcaption class="{{$atn_class}} ">{{$attribute->label}}</figcaption>
                        @if ($avt != 'default')
                            <ul class="{{$avl_class}} {{parse_classname('product-attribute-values', 'product-attribute-'.$avt.'-values')}} {{$attribute->list_class}}">

                                @php
                                    $def = null;
                                    foreach ($attribute->values as $attr) {
                                        if(in_array($attr->id, $attr_values)) {
                                            $def = $attr->id;
                                            break;
                                        }

                                        if($attr->is_default) $def = $attr->id;
                                    }
                                @endphp
                                @foreach ($attribute->values as $attrValue)
                                    <li class="{{$avi_class}} {{parse_classname('variant-value-item','product-attribute-value-item', 'pav-item')}}">
                                        <input type="radio" id="product-variants-{{$item_id}}-{{$attribute->name}}-{{$attrValue->id}}" name="attrs[{{$item_id}}][{{$attribute->name}}]" class="{{$rad_class}} {{parse_classname('radio-value-input')}} {{$attrValue->item_class}}" data-value-id="{{$attrValue->id}}" value="{{$attrValue->id}}" @if(($def && $def == $attrValue->id) || (!$def && $loop->index == 0)) checked @endif>
                                        <label for="product-variants-{{$item_id}}-{{$attribute->name}}-{{$attrValue->id}}" class="{{$lab_class}} " @if ($avt == "color") style="background-color:{{$attrValue->advance_value}}" @endif>
                                            @if ($avt == 'image')
                                            <img src="{{$attrValue->image_url}}" alt="{{$attrValue->text}}" title="{{$attrValue->text}}" class="{{$img_class}}">
                                            @endif
                                            <span>{{$attrValue->text}}</span>
                                        </label>
                                        {{-- <span class="attr-text {{$txt_class}} "><span>{{$attrValue->text}}</span></span> --}}
                                    </li>
                                @endforeach


                            </ul>
                        @else
                            <ul class="{{$avl_class}} {{parse_classname('product-attribute-values')}} {{$attribute->list_class}}">

                                @php
                                    $def = null;
                                    foreach ($attribute->values as $attr) {
                                        if(in_array($attr->id, $attr_values)) {
                                            $def = $attr->id;
                                            break;
                                        }

                                        if($attr->is_default) $def = $attr->id;

                                    }
                                @endphp
                                @foreach ($attribute->values as $attrValue)
                                    <li class="{{$avi_class}} {{parse_classname('variant-value-item','product-attribute-value-item', 'pav-item')}}">
                                        <input type="radio" id="product-variants-{{$item_id}}-{{$attribute->name}}-{{$attrValue->id}}" name="attrs[{{$item_id}}][{{$attribute->name}}]" class="{{$rad_class}} {{parse_classname('radio-value-input')}} {{$attrValue->item_class}}" data-value-id="{{$attrValue->id}}" value="{{$attrValue->id}}" @if(($def && $def == $attrValue->id) || (!$def && $loop->index == 0)) checked @endif>
                                        <label for="product-variants-{{$item_id}}-{{$attribute->name}}-{{$attrValue->id}}" class="{{$lab_class}} ">
                                            <span>{{$attrValue->text}}</span>
                                        </label>
                                    </li>
                                @endforeach


                            </ul>
                        @endif

                    </figure>
                @endforeach
                
            </div>
        </div>
    
        @if ($errors->has('attrs.'.$item->id))
            <div class="alert alert-danger mt-12">
                {{$errors->first('attrs.'.$item->id)}}
            </div>
        @endif

    @endif

</div>