

    <section class="breadcrumb-section section @yield('breadcrumb.section_class', 'section-small') py-10 py-md-15 py-xl-20 py-dx-25">
        {{-- <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul> --}}
        <div class="container @yield('breadcrumb.container_class')">
            <div class="row">
                <div class="col-12">
                    @if ($_title = isset($title) && $title ? $title : $__env->yieldContent('breadcrumb.title'))
                        <h3>{!! $_title !!}</h3>
                    @endif
        
                    <nav>
                        <ol class="breadcrumb">
                            @if ($breadcrumbs = $helper->getBreadcrumbs())
                                @foreach ($breadcrumbs as $item)
                                    @if ($loop->last)
                                        <li class="breadcrumb-item active" aria-current="page">
                                            @if ($__env->yieldContent('breadcrumb.disable_last_url'))
                                            {{ $item->text }}    
                                            @else
                                            <a title="{{ $item->text }}" href="{{ $item->url }}">{{ $item->text }}</a>
                                            @endif
                                            
                                        </li>
                                    @else
                                        <li class="breadcrumb-item"><span><a title="{{ $item->text }}" href="{{ $item->url }}">{{ $item->text }}</a></span></li>
                                    @endif
                    
                                @endforeach
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
