<div class="add-item {{$data->class}}">
    @if ($data->type=='embed_code')
        {!! $data->embed_code !!}
    @else
        <a href="{{$data->url}}" class="ads-img">
            <img src="{{$data->image}}" alt="{{$data->alt}}" class="rounded">
        </a>
    @endif
</div>