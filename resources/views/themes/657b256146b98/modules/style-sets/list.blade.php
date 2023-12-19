
@extends($_layout.'master')

@section('content')
<div class="ovxh">
    <a href="#"><img src="{{theme_asset('images/style-set.png')}}" class="w-100" alt="Tạo style mới của riêng mình" style="max-width: 100%"></a>

</div>
<div class="section section-personal-styles ovxh section-default section-small" style="background: #fff">
    <div class="container container-max">
        <div class="section-header">
            <h3 class="section-title text-center">Style Cá nhân</h3>

        </div>
        <div class="section-content">
            <div class="style-list">
                <div class="row g-sm-4 g-3 row-cols-lg-4 row-cols-md-2 row-cols-2 mt-1 ratio_asos">
                    @foreach ($styleSets as $style)
                        @php
                            $args = [];
                            $routeParams = [];

                            $u = route('web.style-sets.update', ['id' => $style->id])


                        @endphp
                        <div class="set-item style-set-item personal-style-item" id="personal-style-item-{{$style->id}}">

                            <div class="thumbnail">
                                <a href="{{$u}}" class="btn-view-products">
                                    <img src="{{$style->thumbnail_url}}" class="bg-img blur-up lazyload" alt="{{$style->name?$style->name:$style->title}}">
                                </a>
                            </div>
                            <div class="mt-10">

                                    <div class="item-name flex-center">
                                        <a href="{{$u}}" class="btn-view-products d-block">
                                            <h5>{{$style->name?$style->name:$style->title}}</h5>

                                        </a>
                                        <div class="right-icon ml-auto">
                                            <a href="javascript:void(0);" class="btn-delete-my-style" data-id="{{$style->id}}" data-name="{{$style->name?$style->name:$style->title}}"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="set-item style-set-item personal-style-item">

                        <div class="thumbnail">
                            <a href="{{$u = route('web.style-sets.create')}}" class="btn-add-new-style">
                                <img src="{{theme_asset('images/add-bg.png')}}" class="bg-img blur-up lazyload" alt="Tạo style mới của riêng mình">
                            </a>
                        </div>
                        <div class="mt-10">
                            <a href="{{$u}}" class="btn-view-products d-block btn-add-new-style">
                                <div class="item-name flex-center">
                                    <h5>Tạo style mới của riêng mình</h5>
                                    <div class="right-icon ml-auto">
                                        <img src="{{theme_asset('images/union.png')}}" alt="">
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            {{$styleSets->links($_template.'pagination')}}
        </div>

    </div>
</div>
<div class="advertise-section ovxh section-small">
    <div class="container container-max">
        <div class="advertise-list">
            @if ($t = count($banners = get_banners(['position'=>'top_page','@limit'=>3])))
                @foreach($banners as $banner)
                    <div class="add-item {{$t%2 == 1 && $loop->last ? 'df-sm-none': ''}}">
                        @if ($banner->type=='embed_code')
                            {!! $banner->embed_code !!}
                        @else
                            <a href="{{$banner->url}}" class="ads-img">
                                <img src="{{$banner->image}}" alt="{{$banner->alt}}" class="rounded">
                            </a>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

{{-- thêm js mà layout chua co --}}
@php
    add_js_src(theme_asset('js/style-list.js'));
    add_js_data('style_urls', ['delete' => route('web.style-sets.delete')])

@endphp
@section('js')


    <script>
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        });
    </script>

@endsection

