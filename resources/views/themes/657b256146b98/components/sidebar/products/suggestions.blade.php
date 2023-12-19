
@php
$activedProduct = get_active_model('product');
$products = $activedProduct->getSuggestionProducts([
    '@limit' => $data->limit && $data->limit > 0 ? $data->limit : 4,
    '@sorttype' => $data->sorttype(1),
    '@with' => ['promoAvailable']
]);
@endphp

@if (count($products))
    <div class="suggestion-products widget">
        <h3 class="widget-title">
            {{str_replace('{product_name}', $activedProduct->name, $data->title('Phối đồ cùng {product_name}'))}}
        </h3>
        <div class="widget-content suggestion-product-list">
            @foreach ($products as $item)
                @php
                    $hasPromo = $item->hasPromo();
                    // $reviews = $product->getReviewData();
                    $hasOption = $item->hasOption();
                    $u = $item->getViewUrl();
                    $downPercent = $item->getDownPercent();
                                       $listPrice = $item->priceFormat('list');
                                       $finalPrice = $item->priceFormat('final');
                @endphp                 
                
                <div class="suggestion-item">
                    <div class="suggestion-thumbnail">
                        <a href="{{$u}}">
                            <img src="{{$item->getThumbnail()}}" />
                        </a>
                    </div>
                    <div class="suggestion-detail">
                        <h3><a href="{{$u}}">{{$item->sub('name', 50, '...')}}</a></h3>
                        <p class="suggedstion-price">

                            @if ($hasPromo)
                                <span class="old-price">
                                    {{$listPrice}}
                                </span>
                                <span class="regular-price">{{$finalPrice}}</span>
                                <span class="onsale-label">-{{$downPercent}}%</span>
                            @else
                                <span class="regular-price">{{$finalPrice}}</span>
                            @endif
                            
                            
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif