@if ($slider = get_slider(['status' => 1, 'slug' => 'home']))
<div class="slider-saction">
    <div id="slider-carosel-{{$slider->id}}" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach ($slider->items as $key => $item)
                @if ($loop->index == 0)
                    <button type="button" data-bs-target="#slider-carosel-{{$slider->id}}" data-bs-slide-to="{{ $loop->index }}" class="active" aria-current="true" aria-label="{{ $item->item }}"></button>
                @else
                    <button type="button" data-bs-target="#slider-carosel-{{$slider->id}}" data-bs-slide-to="{{ $loop->index }}" aria-label="{{ $item->item }}"></button>
                @endif
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach ($slider->items as $key => $item)
                @if ($loop->index == 0)
                    <div class="carousel-item active">
                        <a href="{{$item->link}}" class="d-block w-100"><img src="{{ $item->getImage() }}" class="d-block w-100" alt="{{ $item->title }}"></a>
                    </div>
                @else
                    <div class="carousel-item">
                        <a href="{{$item->link}}" class="d-block w-100"><img src="{{ $item->getImage() }}" class="d-block w-100" alt="{{ $item->title }}"></a>
                    </div>
                @endif
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#slider-carosel-{{$slider->id}}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#slider-carosel-{{$slider->id}}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
@endif
