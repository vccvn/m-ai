<?php
add_css_link('/static/features/checktree/style.min.css');
add_js_src('/static/features/checktree/script.js');

$def = [];
$opts = $input->getInputData();
if(is_array($opts) || is_object($opts)){
    
    if(is_array($df = $input->defVal())){
        $def = $df;
    }
}
$defaultValues = old($input->name, $def);
if(!is_array($defaultValues)) $defaultValues = [];
$listType = 'list';
$input->name.='[]';
$disable = is_array($d = $input->hiddenData('disable')) ? $d : [];
// dump($options);

$wrapper = $input->copy();

$wrapper->removeClass()->addClass("m-checkbox-{$listType}");
$wrapper->addClass('check-tree');
$wrapper->name = null;
$wrapper->type = null;
$wrapper->placehoder = null;
$wrapper->id = $wrapper->id. '-wrapper';

$render = function($render, $opts, $level = 0) use($input, $wrapper, $defaultValues, $disable){
    $code = '<div class="check-tree-block '.($level ? 'children' : 'parent').'" data-level="'.$level.'">';
        if (count($opts)){
            foreach ($opts as $value => $text){
                $code .= '<div class="check-block-item">';
                if(is_array($text) && array_key_exists('label', $text)  && array_key_exists('data', $text)){
                    $code .= '
                    <label class="m-checkbox m-checkbox--solid m-checkbox--info parent-checkbox">
                        <input type="checkbox" class="crazy-checkbox" name="'.$input->name.'" id="' . $input->id.'-'.str_slug($value) . '" value="'.$value.'" '. (in_array($value, $disable)?' disabled="true"':'') . (in_array($value, $defaultValues)?' checked ':'').'> 
                        '.$text['label'].'
                        <span></span>
                    </label>';
                    $code .= $render($render, $text['data'], $level+1);
                }else {
                    $code .= '
                    <label class="m-checkbox m-checkbox--solid m-checkbox--info">
                        <input type="checkbox" class="crazy-checkbox" name="'.$input->name.'" id="' . $input->id.'-'.str_slug($value) . '" value="'.$value.'" '. (in_array($value, $disable)?' disabled="true"':'') . (in_array($value, $defaultValues)?' checked ':'').'> 
                        '.$text.'
                        <span></span>
                    </label>
                    ';
                }
                $code .= '</div>';
            }
        }
    $code .='</div>';
    return $code;

};

?>

<div {!! $wrapper->attrsToStr() !!}>
    {!!$render($render, $opts)!!}
</div>




