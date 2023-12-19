@if($data->show)
    <div class="d-flex">
        <span class="about-h2">{{$data->order}}</span>
        <div class="item-content-custom">
            <img src="{{$data->image}}" alt="">
            <h3>{{$data->title}}</h3>
            <h4>{{$data->desc}}</h4>
        </div>
    </div>
@endif
