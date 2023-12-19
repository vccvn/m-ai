<?php
$routeParams = [];
if($data->list_type == "collection"){
    if ($data->collection_id && $collection = $helper->getProductCollection(['id' => $data->collection_id])) {
        $routeParams = $collection->urlParams;
    }
    $url = url_merge(route('web.products'), $routeParams, null, null, true);
}
elseif($data->list_type != "custom"){
    if ($data->sorttype) {
        $args['sorttype'] = $data->sorttype;
    }
    if ($data->categories) {
        $routeParams['categories'] = is_array($data->categories) ? implode(',', $data->categories) : '';
    }
    if ($data->match_label && $data->match_label != 'none' && $data->labels) {
        $routeParams[$data->match_label == 'all' ? 'match_labels' : 'has_label'] = is_array($data->labels) ? implode(',', $data->labels) : '';
    }
    if ($data->match_tag && $data->match_tag != 'none' && $data->tags) {
        $routeParams[$data->match_tag == 'all' ? 'match_tags' : 'has_tag'] = is_array($data->tags) ? implode(',', $data->tags) : "";
    }
    $url = url_merge(route('web.products'), $routeParams, null, null, true);
}else{
    $url = $data->url;
}


?>
@if($data->show)
<div class="col-md-2 col-sm-6 col-6">
    <a href="{{$url}}" class="t-flex">
        <img src="{{$data->image}}" alt="{{$data->title}}">
        <div class="box-content">
            <span class="">{{$data->title}}</span>
        </div>
    </a>
</div>
@endif
