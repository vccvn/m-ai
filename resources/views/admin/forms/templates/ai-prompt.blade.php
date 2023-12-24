<?php

use Crazy\Html\Input;
use Crazy\Helpers\Arr;


add_ai_prompt_assets();

$input->addClass("ai-prompt");
$input->type = "textarea";

add_css_link('/static/manager/css/ai-prompt.css');

?>





<div id="{!! $input->id.'-tabs' !!}" class="ai-prompt-wrapper">

    <div class="row">
        <div class="col-md-8">
            {!! $input !!}
        </div>
        <div class="col-md-4">
            @if ($criteria_list = get_criteria_list())
                <div class="criteria-list">
                    @foreach ($criteria_list as $criteria)
                        <div class="criteria-item" data-id="{{$criteria->id}}" data-label="{{$criteria->label}}">
                            <div class="inner-box">
                                <span class="criteria-tag">{{$criteria->label}}</span>  {{$criteria->description}}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
