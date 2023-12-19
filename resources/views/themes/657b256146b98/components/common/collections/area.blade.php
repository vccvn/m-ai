@if ($data->show)

@php
add_js_src(theme_asset('components/common/script.js'));
@endphp

<div class="section collection-section section-large ovxh py-20 py-md-25 py-xxl-30">
    <div class="container container-max">
        <div class="filter-options section-header">
            <div class="select-options">
                <h3 class="section-title">{{$data->title}}</h3>
            </div>
            {{-- <div class="grid-options d-sm-inline-block d-none">
                <a href="#" class="theme-color seemore">Xem thêm <i class="fa fa-angle-right"></i></a>
            </div> --}}
        </div>
        <div class="product-style-2 ratio_asos product-list-section ovh">
            <div class="min-width-532">
                <div class="slide-ovh">

                    @if ($data->list_type == 'custom')
                        {!! $children !!}
                    @else
                        @php
                            $args = [
                                '@withCategories' => true
                            ];
                            if($data->list_type=='tagged'){
                                $args['id'] = $data->collection_tags?$data->collection_tags:[0];
                            }else{
                                $args['@sorttype'] = $data->sorttype;
                                $args['@limit'] = $data->limit && $data->limit > 0? $data->limit:4;
                            }
                        @endphp

                        @if (count($collections = $helper->getProductCollections($args)))

                            @foreach ($collections as $item)
                            @php
                                $url = route('web.products');
                                $url.="?collection=" . $item->id;
                            @endphp
                                <div>
                                    <div class="product-box">
                                        <div class="img-wrapper">
                                            <div class="front">
                                                <a href="{{$url}}">
                                                    <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                                </a>
                                            </div>
                                            <div class="back">
                                                <a href="{{$url}}">
                                                    <img src="{{$item->image}}" class="bg-img blur-up lazyload" alt="">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product-details">
                                            <a href="{{$url}}" class="font-default">

                                                <div class="title-box">

                                                    <h5 class="ms-0">{{$item->name}}</h5>

                                                    <i class="fa fa-angle-right"></i>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@endif
