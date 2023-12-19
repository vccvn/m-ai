

        <!-- Page Banner Start -->
        <div class="section page-banner-section bg-color-2">
{{--
            <img class="shape-1" src="{{frontend_asset('images/shape/shape-5.png')}}" alt="shape">
            <img class="shape-2" src="{{frontend_asset('images/shape/shape-6.png')}}" alt="shape">
            <img class="shape-3" src="{{frontend_asset('images/shape/shape-7.png')}}" alt="shape">

            <img class="shape-4" src="{{frontend_asset('images/shape/shape-21.png')}}" alt="shape">
            <img class="shape-5" src="{{frontend_asset('images/shape/shape-21.png')}}" alt="shape"> --}}

            <div class="container">
                <!-- Page Banner Content Start -->
                <div class="page-banner-content">
                    {{-- <h2 class="title">Làm bài thi</h2> --}}
                    <ul class="breadcrumb">
                        @if ($breadcrumbs = $helper->getBreadcrumbs())
                            @foreach ($breadcrumbs as $item)
                                @continue($loop->first)
                                @if ($loop->last)
                                    <li class="breadcrumb-item active" aria-current="page">{{ $item->text }}</li>
                                @else
                                    <li class="breadcrumb-item"><a title="{{ $item->text }}" href="{{ $item->url }}">{{ $item->text }}</a></li>
                                @endif

                            @endforeach
                        @endif
                    </ul>
                </div>
                <!-- Page Banner Content End -->
            </div>
        </div>
        <!-- Page Banner End -->


