<?php
add_js_src('/static/manager/js/location.js');
add_js_src('/static/features/places/form.js');
set_merchant_template_data('modals', 'modal-place');

?>

    @include($_base.'forms.templates.crazyselect', [
        'input' => html_input([
            'type' => 'crazyselect',
            'name' => $input->name,
            'id' => $input->id,
            "@select-type" => $input->hidden('select-type'),
            "@search-route" => $input->hidden('search-route'),
            'call' => 'get_place_options',
            'params' => [['id' => $input->value]],
            'value' => $input->value,
            '@advance' => $input->hidden('advance')
        ])
    ])
