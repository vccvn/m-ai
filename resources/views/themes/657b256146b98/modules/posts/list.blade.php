@extends($_layout.'blog')
@section('title', $page_title)
@include($_lib.'register-meta')

@section('page.header.show', 'breadcrumb')
@section('content')
<h1 class="page-title mb-20">{{$page_title}}</h1>
@if (count($posts))

<div class="row g-4 ">

    @foreach ($posts as $post)
    <div class="col-lg-4 col-md-6">
        <div class="card blog-categority">
            <a href="{{$u = $post->getViewUrl()}}" class="blog-img">
                <img src="{{$post->getThumbnail()}}" alt="{{$post->title}}" class="card-img-top blur-up lazyload bg-img">
            </a>
            <div class="card-body">
                @if ($post->category)
                <h5><a href="{{$post->category->getViewUrl()}}">{{$post->category->name}}</a></h5>
                @endif
                
                <a href="{{$u}}">
                    <h2 class="card-title">
                        {{$post->title}}
                    </h2>
                </a>
                <div class="blog-desc">
                    {{$post->getShortDesc(120)}}
                </div>
            </div>
        </div>
    </div>    
    @endforeach
    
</div>

<div class="col-12">
    {{$posts->links($_template.'pagination')}}
</div>

@else
    <div class="alert alert-warning text-center">
        Không tìm thấy kết quả phù hợp!
    </div>
@endif
@endsection