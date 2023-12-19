@if ($data->show)

@php
add_js_src(theme_asset('components/common/script.js'));
        $title = $data->title;
        $args = [];
        $routeParams = [];
        $url = route('web.products');
        if($data->list_type == 'collection'){
            if($data->collection_id && $collection = $helper->getProductCollection(['id' => $data->collection_id])){
                if(!$title) $title = $collection->name;
                $args = $collection->getProductParams();
                $args['@limit'] = $data->limit && $data->limit > 0 ? $data->limit : 4;
                $routeParams = $collection->urlParams;
                // $url.="?collection=" . $data->collection_id;
            }
        }else{
            $args['@limit'] = $data->limit && $data->limit > 0 ? $data->limit : 4;
            $args['@sorttype'] = $data->sorttype(1);
            // $url.="?sorttype=" . $data->sorttype(1);
            if($data->match_label && $data->match_label != 'none' && $data->labels){
                $args[$data->match_label == 'all' ? '@matchAllLabel' : '@hasAnyLabel'] = $data->labels;
                $routeParams['labels'] = implode(',', $data->labels);
                // $url.="&match_labels=" .implode(',', $data->labels);

            }
            if($data->match_tag && $data->match_tag != 'none' && $data->tags){
                $args[$data->match_tag == 'all' ? '@matchAllTag' : '@hasAnyTag'] = $data->tags;
                $routeParams['labels'] = implode(',', $data->labels);
                // $url.="&match_tags=" .implode(',', $data->tags);

            }
        }

        if($args){
            $args = array_merge($args, [
                '@with' => ['promoAvailable'],
                '@withOption' => true,
                '@withGallery' => true,
                '@withCategory' => true

            ]);
        }
    @endphp
    @if ($args && count($products = $helper->getProducts($args)))
        <div class="section product-list-section section-default ovxh pb-0 pb-sm-10 pb-md-10 pb-xxl-20 pt-12 pt-md-20 pt-xl-25 section-large">
            <div class="container container-max">
                <div class="filter-options mb-10 mb-lg-12">
                    <div class="select-options section-header  mb-0">
                        <h2 class="section-title mb-0">{{$title}}</h2>
                    </div>
                    <div class="grid-options d-sm-inline-block d-none">
                        <a href="{{$url}}" class="theme-color seemore">Xem thêm <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
                <div class="row g-sm-4 g-3 row-cols-lg-4 row-cols-md-2 row-cols-2 mt-1 product-style-2 ratio_asos product-list-section">
                    @foreach ($products as $product)
                        <div class="mt-0">
                            @include($_template.'products.grid-item', [
                                'product' => $product,
                                'item_class' => 'mb-12 mb-lg-20',
                                'use_thubnail_slide' => true
                            ])
                        </div>
                    @endforeach
                </div>


                <div class="section-buttons mt-10 text-center d-sm-none">
                    <a href="{{$url}}" class="btn btn-outline-default btn-def-size">Xem thêm</a>
                </div>
            </div>
        </div>


    @endif


@endif
