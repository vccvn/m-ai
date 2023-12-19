
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
                        <div class="post-list">
                            
                                <h3>{{$title?$title:'Mới nhất'}}</h3>
                            
                            @foreach ($posts as $post)
                                @php
                                    $i = $loop->index+1;
                                    if($i < 10) $i = "0$i";
                                    if($post->trashed_status) continue;
                                @endphp
                            <div class="popular-image">
                                <div class="popular-number">
                                    <h4 class="theme-color">{{$i}}</h4>
                                </div>
                                <div class="popular-contain">
                                    <h3><a href="{{$post->getViewUrl()}}">{{$post->sub('title', 48, '...')}}</a></h3>
                                    <p class="font-light mb-1">
                                        {{$post->getShortDesc(60, '...')}}
                                        {{-- <span>King Monster</span> in <span>News</span> --}}
                                    </p>
                                    {{-- <div class="review-box">
                                        <span class="font-light clock-time"><i data-feather="clock"></i>15m ago</span>
                                        <span class="font-light eye-icon"><i data-feather="eye"></i>8641</span>
                                    </div> --}}
                                </div>
                            </div>

                            @endforeach


                        </div>
                        <!-- Popular Post End -->
@endif