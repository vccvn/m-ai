@if($data->show)
    <div class="advertise-section ovxh section-large">
        <div class="container container-max">
            <div class="advertise-list">
                @if ($data->type!='custom')
                    @if ($t = count($banners = get_banners(['position'=>'top_page','@limit'=>3])))
                        @foreach($banners as $banner)
                            <div class="add-item {{$t%2 == 1 && $loop->last ? 'df-sm-none': ''}}">
                                @if ($banner->type=='embed_code')
                                    {!! $banner->embed_code !!}
                                @else
                                    <a href="{{$banner->url}}" class="ads-img">
                                        <img src="{{$banner->image}}" alt="{{$banner->alt}}" class="rounded">
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                @else
                    {!! $children !!}
                @endif
            </div>
        </div>
    </div>
@endif