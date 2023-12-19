@php
    if($data->suggest_page_id && $page = get_page(['id' => $data->suggest_page_id])) $url = $page->getViewUrl();
    else $url = $data->url;
@endphp
<div class="product-discover extra-block flexable">
    <div class="icon">
        <img src="{{$data->icon}}" alt="{{$data->title}}">
    </div>
    <div class="detail">
        <h5 class="pd-title">{{$data->title}}</h5>
        <div class="pd-desc">
            @if ($data->field && $active = set_active_model('product'))
                {{ $active->{$data->field} }}
            @else
                {{$data->description}}
            @endif
        </div>
        <div class="pd-link">
            <a href="{{$url}}">{{$data->btn_text}}</a>
        </div>
    </div>
</div>