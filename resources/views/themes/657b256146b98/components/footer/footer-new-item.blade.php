<p>
    @switch($data->icon_type)
        @case('image')
            <img src="{{$data->image}}" alt="">
            @break
        @case('font')
            <i class="{{$data->icon}}"></i>
            @break
        @default
            
    @endswitch
    
    <span>{{$data->title}}</span>
</p>