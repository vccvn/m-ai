<?php
use Gomee\Html\Input;
use Gomee\Helpers\Arr;
add_js_src('static/manager/js/input.video.js');
?>

    <div class="video-url-input" id="input-video-{{$input->id }}" data-id="{{$input->id }}">
        
        <div class="url-input">
            {!! $input !!}
        </div>
        <div class="video-preview">

        </div>

        
    </div>
    

