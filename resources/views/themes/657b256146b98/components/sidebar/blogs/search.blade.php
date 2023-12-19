<!-- Search Bar Start -->
<div class="search-section">
    <form action="{{route('web.search')}}" method="get">
        <div class="input-group search-bar">
            <input type="search" class="form-control search-input" placeholder="{{$data->placeholder('Tìm kiếm')}}" name="s" value="{{$request->s}}">
            <button class="input-group-text search-button" id="basic-addon3">
                <i class="fas fa-search text-color"></i>
            </button>
        </div>
    </form>
</div>
<!-- Search Bar End -->
