@if($data->show)
<div class="col-xl-{{$data->col_xl(2)}} col-lg-{{$data->col_lg(3)}} col-md-{{$data->col_md(4)}} col-sm-{{$data->col_sm(6)}} col-sm-{{$data->col_xs(12)}}  {{$data->class}} ">
    <div class="footer-links">
        <div class="footer-title">
            <h3>{{$data->title}}</h3>
        </div>
        <div class="footer-content">
            {!!
              $helper->getCustomMenu(['id' => $data->menu_id], 1, [
                  'class' => 'footer-content-menu'
              ])->addAction(function($item, $link){
                $link->rel='nofollow';
              })
          !!}
        </div>
    </div>
</div>
@endif