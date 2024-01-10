@php
    $footer = $options->theme->footer;
@endphp
{!! $html->footer_before->components !!}
<footer class="footer-area footer-bg2 pt-100">
    <div class="container">
        <div class="footer-midal pb-70">
            <div class="row">
                {!! $html->footer_widgets->components !!}
            </div>
        </div>
        <div class="copy-right-area">
            <div class="row">
                <div class="col-lg-8">
                    <div class="copy-right-text text-left">
                        @if ($footer->copyright)
                            {!! $footer->copyright !!}
                        @else
                            <p>
                                Copyright @

                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{$siteinfo->site_name}}. All Rights Reserved by
                                <a href="https://mjumedia.vn" target="_blank">MjuMedia.vn</a>
                            </p>
                        @endif

                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="copy-right-list">
                        {!!
                            $helper->getCustomMenu(['id' => $footer->menu_id], 1, [
                                'class' => ' '
                            ])
                        !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
