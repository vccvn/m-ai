<div class="policy-page-content">
    <div class="page-content-header">
        <h3 class="page-content-title">{{$data->title}}</h3>
    </div>
    <div class="page-content-texts">
        @if ($data->content_type=='custom')
            {!! $data->content !!}
            @elseif (($data->content_type == 'custom-page') && $data->content_page_id && ($page = $helper->getPage(['id' => $data->content_page_id, 'privacy' => ['public', 'private', 'protected', 'publish']])))
                {!! $page->content !!}
            @elseif($page = get_active_model('page')){
                {!! $page->content !!}
            }
        @endif
    </div>
</div>