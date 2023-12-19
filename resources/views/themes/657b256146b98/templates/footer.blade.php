<footer>
    <div class="main-footer t-footer">
        <div class="container-fluid-lg">
            <div class="row gy-3">
                {!! $html->footer_widgets->components !!}
            </div>
        </div>

        <div class="container">
            {!! $html->footer_logos->components !!}
        </div>
    </div>


    <div class="t-sub-footer">
        <div class="container">
            <div class="row gy-3">
                <div class="col-md-12 box-footer">
                    <p class="mb-0 font-dark t-text-footer">
                        
                        @if ($options->theme->footer->copyright)
                            {!! strip_tags($options->theme->footer->copyright) !!}
                        @else
                            {!! '&copy;' . date('Y') . ' ' . siteinfo('site_name', 'Gomee') . '. Design by Gomee' !!}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>