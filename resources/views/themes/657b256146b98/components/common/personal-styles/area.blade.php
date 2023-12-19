@php
    $styles = [];
    if ($data->list_type == 'custom'){
        if($children && $_style = $children->getComponentDatas()){
            $styles = $_style;
        }
    }elseif($user = auth()->user()){
        $styles = get_personal_style_sets([
            '@limit' => 4,
            'user_id' => $user->id
        ]);
    }
@endphp
@if ($data->show && count($styles))
@php
add_js_src(theme_asset('components/common/script.js'));
@endphp
    <div class="section section-personal-styles ovxh section-default section-large">
        <div class="container container-max">
            <div class="section-header">
                <h2 class="section-title">{{$data->title}}</h2>
                <div class="section-description d-none d-md-block">
                    <p>{{$data->description}}</p>
                </div>

            </div>
            <div class="section-content wtf-slide">
                <div class="style-list ovh-xs mt-md-30 pb-10">
                    <div class="">
                        <div class="min-width-456">
                            <div class=" ratio_asos personal-style-slide">
                                @foreach ($styles as $style)
                                    @include($_template.'style-sets.person-item', [
                                        'style' => $style,
                                        'tab' => $loop->index
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="style-products style-tabs">
                    @foreach ($styles as $style)
                    @php
                        $args = [];
                        $routeParams = [];

                        if($style->collection_id && $collection = $helper->getProductCollection(['id' => $style->collection_id])){
                            $routeParams = $collection->urlParams;
                            $args = $collection->getProductParams();

                        }

                        // $args['@limit'] = $style->limit && $style->limit > 0 ? $style->limit : 4;
                        if($style->isComponentData){
                            $args['@limit'] = $style->limit && $style->limit > 0 ? $style->limit : 8;
                            if($style->sorttype){
                                $args['@sorttype'] = $style->sorttype;
                                $routeParams['sorttype'] = $style->sorttype;
                            }

                            if($style->categories){
                                $args['@categories'] = $style->categories;
                                $routeParams['categories'] = is_array($style->categories)?implode(',', $style->categories):'';
                            }
                            if($style->match_label && $style->match_label != 'none' && $style->labels){
                                $routeParams[$style->match_label == 'all' ? 'match_labels' : 'has_label'] = is_array($style->labels)?implode(',', $style->labels):'';
                                $args[$style->match_label == 'all' ? '@matchAllLabel' : '@hasAnyLabel'] = $style->labels;
                                // $args["p.p"] = 0;
                            }
                            if($style->match_tag && $style->match_tag != 'none' && $style->tags){
                                $routeParams[$style->match_tag == 'all' ? 'match_tags' : 'has_tag'] = is_array($style->tags)?implode(',', $style->tags):"";
                                $args[$style->match_tag == 'all' ? '@matchAllTag' : '@hasAnyTag'] = $style->tags;
                            }
                        }else{
                            $args['@limit'] = $style->limit && $style->limit > 0 ? $style->limit : 8;
                            $args['@style'] = $style->id;
                            $routeParams['style'] = $style->id;
                        }
                        $args = array_merge($args, [
                            '@with' => ['promoAvailable'],
                            '@withReviews' => true,
                            '@withOption' => true,
                            '@withCategory' => true,
                            '@withGallery' => true,

                        ]);

                    @endphp
                    <div class="style-tab pt-20 pt-lg-30 {{$loop->index == 0 ? 'active' : ''}}" data-tab-index="{{$loop->index}}">
                        @php
                            $products = $helper->getProducts($args);
                        @endphp
                        @include($_template.'products.slides', [
                            'products' => $products,
                            'use_header' => true,
                            'use_thubnail_slide' => true,
                            'title' => 'Phong cách "' . ($style->name?$style->name:$style->title) . '"',
                            'seemore' => route('web.products', $routeParams),
                            'slide_class' => 'style-product-slide',
                            'block_class' =>  'd-none d-md-block',
                            'slide_thumbnail_class' => 'style-thumbnail-slide'
                        ])

                        @include($_template.'products.list', [
                            'products' => $products,
                            'use_header' => true,
                            'use_thubnail_slide' => true,
                            'title' => 'Phong cách "' . ($style->name?$style->name:$style->title) . '"',
                            'list_class' => 'd-md-none',
                            // 'use_thubnail_slide' => true
                        ])
                    </div>

                    @endforeach
                </div>
            </div>

        </div>
    </div>

@endif
