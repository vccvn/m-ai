
<div class="col-lg-{{$data->col_lg(2)}} col-sm-{{$data->col_sm(6)}}">
    <div class="footer-widget">
        <h3>{{$data->title('Liên kết')}}</h3>
        <ul class="footer-list">
            <li>
                <a href="#">Our Scientists</a>
            </li>
            <li>
                <a href="#">Our Services</a>
            </li>
            <li>
                <a href="#">Testimonials</a>
            </li>
            <li>
                <a href="#">SaaS Solutions</a>
            </li>
            <li>
                <a href="#">eCommerce</a>
            </li>
        </ul>
        {!!
            $helper->getCustomMenu(['id' => $data->menu_id], 1, [
                'class' => $data->menu_class . ' footer-list'
            ])
        !!}
    </div>
</div>
