<div class="drop-list-item">
    <div class="item-header">
        <h3 class="item-title">{{$data->title}}</h3>
    </div>
    <div class="item-content">
        @if (($data->content_type == 'page') && $data->content_page_id && ($page = $helper->getPage(['id' => $data->content_page_id, 'privacy' => ['public', 'private', 'protected', 'publish']])))
            {!! $page->content !!}
        @else
            {!! $data->content !!}    
        @endif
    </div>
</div>