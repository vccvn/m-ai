<div class="policy-drop-list">
    <div class="drop-list-header">
        <h3 class="drop-list-title">{{$data->title}}</h3>
    </div>
    <div class="drop-list-contents">
        {!! $children !!}
    </div>
</div>
@php
add_js_src(theme_asset('components/common/script.js'));
@endphp