@php
    $u = $article->getViewUrl();


    switch ($article->id) {
        case $options->theme->about->about_page_id:
            $layout = 'master';
            break;
        case $options->theme->policy->page_id:
            $layout = 'master';
            break;
        case $options->theme->products->suggest_page_id: 
            $layout = 'master';
            break;
        default:
            $layout = 'blog';
            break;
    }
@endphp

@extends($_layout.$layout)
@section('title', $page_title)
@include($_lib.'register-meta')

@section('page.header.show', 'breadcrumb')
@section('content')
    @switch($article->id)
        @case($options->theme->about->about_page_id)
            {!! $html->about_contents->components !!}
            @break
        @case($options->theme->policy->page_id)
            <div class="policy-page-section section section-small pt-20 pb-10 pb-md-20 pb-lg-40 pb-xl-50">
                <div class="container-max">
                    {!! $html->policy_contents->components !!}
                </div>
            </div>
            @break
        @case($options->theme->products->suggest_page_id)
            <!-- abc -->
            @include($_template.'style-sets.suggestion-styles')
            @break
        @default

            <h1 class="page-title mb-12">{{$page_title}}</h1>
            @if (count($pages))
                
            <div class="row g-4 ">

                @foreach ($pages as $page)
                    <div class="col-lg-4 col-md-6">
                    <div class="card blog-categority">
                        <a href="{{$u = $page->getViewUrl()}}" class="blog-img">
                            <img src="{{$page->getThumbnail()}}" alt="{{$page->title}}" class="card-img-top blur-up lazyload bg-img">
                        </a>
                        <div class="card-body">
                            @if ($page->category)
                            <h5><a href="{{$page->category->getViewUrl()}}">{{$page->category->name}}</a></h5>
                            @endif
                            
                            <a href="{{$u}}">
                                <h2 class="card-title">
                                    {{$page->title}}
                                </h2>
                            </a>
                            <div class="blog-desc">
                                {{$page->getShortDesc(120)}}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>

            <div class="col-12">
                {{$pages->links($_template.'pagination')}}
            </div>

            @else
                <div class="alert alert-warning text-center">
                    Không tìm thấy kết quả phù hợp!
                </div>
            @endif


            
    @endswitch
@endsection