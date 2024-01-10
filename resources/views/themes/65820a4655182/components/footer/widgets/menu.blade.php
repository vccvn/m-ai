
<div class="col-lg-{{$data->col_lg(2)}} col-sm-{{$data->col_sm(6)}}">
    <div class="footer-widget">
        <h3>{{$data->title('Liên kết')}}</h3>
        {!!
            $helper->getCustomMenu(['id' => $data->menu_id], 1, [
                'class' => $data->menu_class . ' footer-list'
            ])
        !!}
    </div>
</div>
