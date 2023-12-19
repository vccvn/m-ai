@php
    $name = $data->title;
    $image = $data->image;
    $routeParams = [];

    $url = route('web.products');
    if($data->collection_id && $collection = $helper->getProductCollection(['id' => $data->collection_id])){
        if(!$name) $name = $collection->name;
        if(!$image) $image = $collection->image;
        $args = $collection->getProductParams();


        $url.="?collection=" . $data->collection_id;
        $routeParams = $collection->urlParams;
    }
    else{

        $args['@limit'] = $data->limit && $data->limit > 0 ? $data->limit : 4;
        if ($data->sorttype) {
        $args['sorttype'] = $data->sorttype;

        $url.="?sorttype=" . $data->sorttype(1);
    }


        if($data->match_label && $data->match_label != 'none' && $data->labels){
            $args[$data->match_label == 'all' ? '@matchAllLabel' : '@hasAnyLabel'] = $data->labels;

            $url.="&match_labels=" .implode(',', $data->labels);
        }
        if($data->match_tag && $data->match_tag != 'none' && $data->tags){
            $args[$data->match_tag == 'all' ? '@matchAllTag' : '@hasAnyTag'] = $data->tags;
                $url.="&match_tags=" .implode(',', $data->tags);
        }
    }

        $args['@sorttype'] = $data->sorttype(1);

        if($args){
            $args = array_merge($args, [
                '@with' => ['promoAvailable'],
                '@withOption' => true,
                '@withGallery' => true,
                '@withCategory' => true

            ]);
        }

@endphp
                    <div>
                        <div class="product-box">
                            <div class="img-wrapper">
                                <div class="front">
                                    <a href="{{$url}}">
                                        <img src="{{$image}}" class="bg-img blur-up lazyload" alt="">
                                    </a>
                                </div>
                                <div class="back">
                                    <a href="{{$url}}">
                                        <img src="{{$image}}" class="bg-img blur-up lazyload" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="product-details">
                                <a href="{{$url}}" class="font-default">

                                    <div class="main-price">

                                        <h5 class="ms-0">{{$name}}</h5>

                                        <i class="fa fa-angle-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
