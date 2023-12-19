@php
    add_js_src(theme_asset('components/features/filter.js'));
    add_js_data('filter_data', [
        'search_url' => route('web.products')
    ])
@endphp
<div class="category-option shop-sidebar">
    <div class="button-close mb-3">
        <button class="btn p-0"><i data-feather="arrow-left"></i> Close</button>
    </div>

    <div class="accordion category-name">
        <form method="GET" action="" id="hidden-filter-form"></form>
        <form method="GET" action="" id="product-filter-form">
            <div class="accordion-item category-rating">
                <div class="category-box widget">
                    <div class="search-wrapper">
                        <div class="search-group">
                            <div class="input-group">

                                <input type="text" name="keyword" class="form-control search-input" id="sidebar-search-input-text" placeholder="Tìm kiếm phong cách cá nhân" value="{{$request->keyword}}">

                            </div>
                            <button type="submit" class="btn btn-search"><i class="icon fa fa-search"></i></button>
                        </div>

                    </div>
                </div>

                <div class="price-box widget">
                    <h3 class="widget-title">Khoảng giá</h3>

                    <div class="widget-body">
                        <div class="price-range-group">
                            <input type="number" name="min_price" class="form-control price-input" min="0" step="1" placeholder="Từ 100.000đ" value="{{$request->min_price}}">
                            <i class="fa fa-angle-right"></i>
                            <input type="number" name="max_price" class="form-control price-input" min="0" step="1" placeholder="Đến 2.000.000đ" value="{{$request->max_price}}">
                        </div>
                    </div>
                </div>

                <div class="sort-box widget">
                    <h3 class="widget-title">Sắp xếp theo</h3>

                    <div class="widget-body submit-on-change">
                        @if ($opts = get_product_sortby_options())
                        <ul class="category-list sort-list">
                            @foreach ($opts as $value => $text)
                            @if (!in_array($value, [1, 4, 5, 'seller']))
                            @continue
                            @endif
                            <li>
                                <div class="form-check ps-0 custome-form-check">
                                    <input class="checkbox_animated check-it" type="radio" name="sorttype" id="sorttype--{{$value}}" value="{{$value}}" {!! $value==$request->sorttype?'checked':'' !!}>
                                    <label class="form-check-label" for="sorttype--{{$value}}">{{$text}}</label>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                        @endif
                    </div>
                </div>

                <div class="sort-box widget">
                    <h3 class="widget-title">Bộ lọc</h3>

                    <div class="widget-body submit-on-change">
                        @if ($cates = get_product_categories(['@countproduct' => true]))
                        @php
                            $cc = array_map('trim', explode(',', $request->categories));
                        @endphp
                        <ul class="category-list cate-list">
                            @foreach ($cates as $cate)
                            <li>
                                <div class="form-check ps-0 custome-form-check">
                                    <input class="checkbox_animated check-it" type="checkbox" name="categories[]" id="cate--{{$cate->id}}" value="{{$cate->id}}" {!! in_array($cate->id, $cc)?'checked':'' !!}>
                                    <label class="form-check-label" for="cate--{{$cate->id}}">{{$cate->name . ' ('.$cate->product_count.')'}}</label>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                        @endif
                    </div>
                </div>

                <div class="sort-box widget">
                    <h3 class="widget-title">Mục đích sử dụng</h3>

                    <div class="widget-body submit-on-change">
                        @if ($cates = get_product_tagss(['@limit' => 10]))
                        @php
                            $tags = array_map('trim', explode(',', $request->tags));
                        @endphp
                        <ul class="category-list cate-list">
                            @foreach ($cates as $cate)
                            <li>
                                <div class="form-check ps-0 custome-form-check">
                                    <input class="checkbox_animated check-it" type="checkbox" name="tags[]" id="cate--{{$cate->id}}" value="{{$cate->id}}"  {!! in_array($cate->id, $cc)?'checked':'' !!}>
                                    <label class="form-check-label" for="cate--{{$cate->id}}">{{$cate->name . ' ('.$cate->product_count.')'}}</label>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                        @endif
                    </div>
                </div>
                <div class="sort-box widget">
                    <h3 class="widget-title">Phong Cách</h3>

                    <div class="widget-body submit-on-change">
                        @if ($cates = get_product_collections(['@limit' => 40]))
                        <ul class="category-list cate-list">
                            @foreach ($cates as $cate)
                            @php
                            $args = $cate->getProductParams();
                            $count = get_product_count($args);
                            @endphp
                            @if (!$count)
                            @continue
                            @endif

                            <li>
                                <div class="form-check ps-0 custome-form-check">
                                    <input class="checkbox_animated check-it" type="radio" name="collection" id="collection--{{$cate->id}}" value="{{$cate->id}}"{!! $cate->id==$request->collection?'checked':'' !!}>
                                    <label class="form-check-label" for="collection--{{$cate->id}}">{{$cate->name . ' ('.$count.')'}}</label>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                        @endif
                    </div>
                </div>


            </div>

        </form>
    </div>
</div>
