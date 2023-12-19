

    <div class="review-item">
        <div class="review-inner">
            <div class="review-thumb">
                <img src="{{$data->avatar}}" alt="{{$data->name}}">
            </div>
            <div class="review-content">
                <div class="author">
                    <h3>{{$data->name}}</h3>
                    <p>{{$data->sub_title}}</p>
                </div>
                <ul class="ratting">
                @for ($i = 0; $i < $data->rating; $i++)
                <li><i class="fa fa-star"></i></li>
                @endfor
                </ul>

                <div class="comment">
                    {{$data->comment}}
                </div>
                @if ($data->images && is_array($data->images))
                    <div class="images">
                        <div class="image-list">
                            @foreach ($data->images as $url)
                                <a href="javascript:;">
                                    <img src="{{$url}}">
                                </a>
                            @endforeach
                        </div>
                    </div>

                @endif
            </div>
        </div>
        
    </div>
