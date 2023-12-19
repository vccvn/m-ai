@php
    $args = [];
    $routeParams = [];

    if($style->collection_id && $collection = $helper->getProductCollection(['id' => $style->collection_id])){
        $args = $collection->urlParams;
    }

    if($style->isComponentData){
        if($style->sorttype){
            $args['sorttype'] = $style->sorttype;
        }

        if($style->categories){
            $args['categories'] = is_array($style->categories)?implode(',', $style->categories):'';
        }
        if($style->match_label && $style->match_label != 'none' && $style->labels){
            $args[$style->match_label == 'all' ? 'match_labels' : 'has_label'] = is_array($style->labels)?implode(',', $style->labels):'';
        }
        if($style->match_tag && $style->match_tag != 'none' && $style->tags){
            $args[$style->match_tag == 'all' ? 'match_tags' : 'has_tag'] = is_array($style->tags)?implode(',', $style->tags):"";
        }
        $u = url_merge(route('web.products'), $args, null, null, true);
    }
    else{
        $u = url_merge(route('web.products', ['style' => $style->id]));
    }




@endphp
<div class="set-item style-set-item personal-style-item">

    <div class="thumbnail">
        <a href="{{$u}}" class="btn-view-products" data-tab="{{$tab??0}}">
            <img src="{{$style->image??$style->thumbnail_url}}" class="bg-img blur-up lazyload" alt="{{$style->name?$style->name:$style->title}}">
        </a>
    </div>
    <div class="mt-10">
        <a href="{{$u}}" class="btn-view-products d-block" data-tab="{{$tab??0}}">
            <div class="item-name flex-center">
                <h5>{{$style->name?$style->name:$style->title}}</h5>
                <div class="right-icon ml-auto">
                    <img src="{{theme_asset('images/union.png')}}" alt="">
                </div>
            </div>
        </a>
    </div>
</div>
