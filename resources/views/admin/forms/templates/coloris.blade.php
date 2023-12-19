<?php
add_css_link('/static/plugins/coloris/coloris.min.css');
add_js_src('/static/plugins/coloris/coloris.min.js');
add_js_src('/static/features/common/common.js');
add_css_link('/static/features/common/common.min.css');
$input->addClass('coloris');

?>

    <div class="coloris-wrapper {{$input->hiddenData('preview-type')}}">
    
        {!! $input !!}

    </div>