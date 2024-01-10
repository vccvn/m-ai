<div class="col-lg-{{$data->col_lg(4)}} col-sm-{{$data->col_sm(7)}}">
    <div class="footer-widget">
        <div class="footer-img">
            <img src="{{ $data->light_logo ?? theme_asset('img/logo/logo1.png') }}" class="footer-img1" alt="Images">
            <img src="{{ $data->dark_logo ?? ($data->light_logo ?? theme_asset('img/logo/logo2.png')) }}" class="footer-img2" alt="Images">
        </div>
        <p>
            {{ $data->description }}
        </p>
        @if ($data->show_socials && ($socials = $options->theme->socials))
            <div class="footer-social-icon">
                <ul class="social-link">
                    @if ($socials->facebook)
                <li>
                    <a href="{{ $socials->facebook }}" target="_blank"><i class="bx bxl-facebook"></i></a>
                </li>
            @endif
            @if ($socials->twitter)
                <li>
                    <a href="{{ $socials->twitter }}" target="_blank"><i class="bx bxl-twitter"></i></a>
                </li>
            @endif
            @if ($socials->pinterest)
                <li>
                    <a href="{{ $socials->pinterest }}" target="_blank"><i class="bx bxl-pinterest-alt"></i></a>
                </li>
            @endif
            @if ($socials->youtube)
                <li>
                    <a href="{{ $socials->youtube }}" target="_blank"><i class="bx bxl-youtube"></i></a>
                </li>
            @endif
                </ul>
            </div>
        @endif
    </div>
</div>
