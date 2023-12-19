@if($data->show)

    <div class="discover-section ovxh">

        <div class="thumbnail">
            <img src="{{$data->image}}" alt="{{$data->alt}}">
        </div>
        <div class="content">
            <h3>{{$data->title}}</h3>
            <h5>{{$data->desc}}</h5>
            <p class="">{{$data->content}}</p>
            <div class="btns">
                <a href="{{$data->url}}" class="btn btn-def-size btn-colored-default">Khám phá ngay</a>
            </div>
        </div>


    </div>
@endif