@if ($reviews && count($reviews))
    <div class="review-list">

        @foreach ($reviews as $item)
            @php
                $attrs = $item->getAttrs();
                $attrData = [];
                foreach ($attrs as $attr) {
                    $attrData[] = $attr->text_value;
                }
                $attrStr = implode('/', $attrData);
                
            @endphp
            <div class="row review-item">
                <div class="col-sm-4 col-lg-3 review-info">
                    <h5 class="review-name">{{ $item->name }}</h5>
                    <p class="review-product">{{ $product->name }} {{ $attrStr }}</p>
                    <ul class="rating review-rating">
                        @for ($i = 0; $i < $item->rating; $i++)
                            <li>
                                <i class="fas fa-star theme-color"></i>
                            </li>
                        @endfor
                    </ul>
                </div>
                <div class="col-sm-8 col-lg-9 review-detail">
                    <p class="review-comment">{!! nl2br($item->comment) !!}</p>
                    <p class="review-date">{{ $item->timeFormat('H:i d-m-Y') }}</p>
                </div>
            </div>
        @endforeach


    </div>
    <div class="review-paginate review-ajax-pagination">{!! $reviews->links($_template . 'pagination') !!}</div>


@endif
