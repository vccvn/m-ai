<div class="search-full">
    <form action="{{route('web.products')}}" method="get">
        <div class="input-group">
            <span class="input-group-text">
                <i data-feather="search" class="font-light"></i>
            </span>
            <input type="text" class="form-control search-type" placeholder="Tìm kiếm phong cách của bạn" name="keyword" value="{{$request->keyword}}">
            <span class="input-group-text close-search">
                <i data-feather="x" class="font-light"></i>
            </span>
        </div>
    </form>
</div>
