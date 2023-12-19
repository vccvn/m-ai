@extends($_layout.'blog')
@section('title', $page_title)
@include($_lib.'register-meta')

@section('page.header.show', 'breadcrumb')
@section('content')
@if (count($results))
    
<div class="row g-4 ">

    @foreach ($results as $result)
        
    @endforeach
    <div class="col-lg-4 col-md-6">
        <div class="card blog-categority">
            <a href="{{$u = $result->getViewUrl()}}" class="blog-img">
                <img src="{{$result->getThumbnail()}}" alt="{{$result->title}}" class="card-img-top blur-up lazyload bg-img">
            </a>
            <div class="card-body">
                @if ($result->category)
                <h5><a href="{{$result->category->getViewUrl()}}">{{$result->category->name}}</a></h5>
                @endif
                
                <a href="{{$u}}">
                    <h2 class="card-title">
                        {{$result->title}}
                    </h2>
                </a>
                <div class="blog-desc">
                    {{$result->getShortDesc(120)}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    {{$results->links($_template.'pagination')}}
</div>

@else
    <div class="alert alert-warning text-center">
        Không tìm thấy kết quả phù hợp!
    </div>
@endif
@endsection