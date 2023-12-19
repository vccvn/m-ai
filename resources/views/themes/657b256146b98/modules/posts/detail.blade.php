@extends($_layout.'blog')
@section('title', $page_title)
@include($_lib.'register-meta')

@section('page.header.show', 'breadcrumb')
@php
    $u = $article->getViewUrl();
@endphp
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="blog-details">
            <div class="blog-image-box">
                <img src="{{$article->getImage()}}" alt="{{$article->title}}" class="card-img-top">
                <div class="blog-title">
                    <div>
                        @include($_template.'share', [
                            'title' => $article->title,
                            'description' => $article->getShortDesc(300),
                            'url' => $u,
                            'image' => $article->getImage('social')
                        ])
                    </div>
                </div>
            </div>

            <div class="blog-detail-contain">
                <span class="font-light">{{$article->dateFormat('d/m/Y')}}</span>
                <h1 class="card-title">{{$article->title}}</h1>
                <div class="article-content">
                    {!! $article->content !!}
                </div>
            </div>
        </div>
{{--     
        @include($_template.'comments',[
            'comments' => [],
            'ref' => $article->type,
            'ref_id' => $article->id,
            'url' => $u
        ]) --}}

    </div>
</div>
@endsection