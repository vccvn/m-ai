
@php
$args = [
    '@limit' => $data->post_number?$data->post_number:20,
    '@sort' => $data->sorttype?$data->sorttype:1
];
$title = null;
if($data->dynamic_id && $dynamic = $helper->getDynamic(['id' => $data->dynamic_id])){
    $args['dynamic_id'] = $data->dynamic_id;
    $title = $dynamic->name;
}
if($data->category_id && $category = $helper->getPostCategory(['id' => $data->category_id])){
    $args['@category'] = $data->category_id;
    if(!$title) $title = $category->name;
}

if($data->content_type && $data->content_type != 'all'){
    $args['content_type'] = $data->content_type;

}
if($data->title) $title = $data->title;
$args['trashed_status'] = 0;
@endphp
@if (count($posts = $helper->getPosts($args)))
    <!-- Popular Post Start -->
    <div class="product-seo-posts">
        @foreach ($posts as $post)
            @php
                $i = $loop->index+1;
                if($i < 10) $i = "0$i";
                if($post->trashed_status) continue;
            @endphp
        <div class="post-item">
            <div class="post-thumb">
                <a href="{{$u = $post->getViewUrl()}}">
                    <img src="{{$post->getThumbnail()}}" />
                </a>
            </div>
            <div class="post-detail">
                <h3><a href="{{$u}}">{{$post->sub('title', 32, '...')}}</a></h3>
                <p class="font-light mb-1">
                    {{$post->getShortDesc(32, '...')}}
                </p>
                <p class="font-light mb-1">
                    {{$post->dateFormat('d/m/Y')}}
                </p>
            </div>
        </div>

        @endforeach


    </div>
    <!-- Popular Post End -->
@endif