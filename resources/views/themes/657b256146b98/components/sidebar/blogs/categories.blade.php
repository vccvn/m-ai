@php
    $url = null;
    $args = [
        '@limit' => $data->cate_number?$data->cate_number:10,
        '@advance' => ['post_count'],
    ];
    $title = null;
    if($data->get_by_dynamic_active && $active = $helper->getActiveModel('dynamic')){
        $args['dynamic_id'] = $active->id;
        $title = $active->name;
        
    }
    else{
        if($data->dynamic_id && $dynamic = $helper->getDynamic(['id' => $data->dynamic_id])){
            $args['dynamic_id'] = $data->dynamic_id;
            $title = $dynamic->name;
            // $url = $dynamic->getViewUrl();
        }
        if($data->category_id && $category = $helper->getPostCategory(['id' => $data->category_id])){
            $args['parent_id'] = $data->category_id;
            if(!$title) $title = $category->name;
        }
        
    }
    if($data->title) $title = $data->title;
@endphp


@count($categories = $helper->getPostCategories($args))
                        <!-- Category section Start -->
                        <div class="category-section">
                            <div class="popular-title">
                                <h3>{{$title}}</h3>
                            </div>
                            <ul>
                                
                                @foreach ($categories as $category)
                                    <li class="category-box">
                                        <a href="{{$category->getViewUrl()}}">
                                            <div class="category-product">
                                                <div class="cate-shape">
                                                    <i class="fas fa-globe text-color"></i>
                                                </div>
    
                                                <div class="cate-contain">
                                                    <h5 class="text-color">{{$category->name}}</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                
                            </ul>
                        </div>
                        <!-- Category section End -->
@endcount
