@if($data->show)
    <div class="container-fluid-lg">
        <div class="title title-2 text-left">
            <h2>Câu chuyện về</h2>
            <!-- <h2>WHO</h2> -->
            <img src="{{$data->logo}}" class="img-fluid blur-up lazyloaded" alt="logo">
        </div>
        <div class="row position-relative">
            <div class="title-top" style="max-width: 500px; opacity: .5;">
                <p class="m-0">{{$data->desc}}</p>
            </div>
            <div class="col-lg-6 col-sm-12">
                <img class="w-100 about-custom" src="{{$data->image}}" alt="">
            </div>
            <div class="col-lg-6 col-sm-12 d-flex align-items-end">
                <div class="title title1 title-effect mb-1 title-left">
                    <p class="m-0 w-100"><?php echo (html_entity_decode($data->content)); ?></p>
                </div>

            </div>
        </div>
    </div>
    <style>
    </style>
@endif