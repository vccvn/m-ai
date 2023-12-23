<?php

use Crazy\Html\Input;
use Crazy\Helpers\Arr;


add_ai_prompt_assets();

$input->addClass("ai-prompt");
$input->type = "textarea";

?>





<div id="{!! $input->id.'-tabs' !!}" class="ai-prompt-wrapper">
    {!! $input !!}
</div>
