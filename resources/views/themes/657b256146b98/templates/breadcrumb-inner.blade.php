
    <div class="breadcrumb-section breadcrumb-inner">
        
        @if ($_title = isset($title) && $title ? $title : $__env->yieldContent('breadcrumb.title'))
            <h1 class="d-none">{!! $_title !!}</h1>
        @endif

        <nav>
            <ol class="breadcrumb">
                @if ($breadcrumbs = $helper->getBreadcrumbs())
                    @foreach ($breadcrumbs as $item)
                        @if ($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $item->text }}</li>
                        @else
                            <li class="breadcrumb-item"><span><a title="{{ $item->text }}" href="{{ $item->url }}">{{ $item->text }}</a></span></li>
                        @endif
        
                    @endforeach
                @endif
            </ol>
        </nav>
    </div>
