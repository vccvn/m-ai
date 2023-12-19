@section('title', $model->title)
<!DOCTYPE html>
<html lang="en">

<head>

    @include($_lib . 'meta')
    @include($_lib . 'head')

    <link rel="stylesheet" href="{{ asset('static/app/css/model-preview.min.css') }}">

    <style>
        .pre-loading-image {
            background: url("{{ $model->getThumbnail() }}") no-repeat center center;
            background-size: cover;
        }
    </style>

    <style>
        #error {
            background-color: #ffffffdd;
            border-radius: 16px;
            padding: 16px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate3d(-50%, -50%, 0);
            transition: opacity 0.3s;
        }

        #error.hide {
            opacity: 0;
            visibility: hidden;
            transition: visibility 2s, opacity 1s 1s;
        }
    </style>

</head>

<body>
    @php
        $url = url('a');
        $p = $model->path;
        if(preg_match('/^https\:\/\//', $url) && !preg_match('/^https\:\/\//', $p)){
            $p = str_replace('http://', 'https://', $p);
        }

    @endphp
    <div class="fix-pos">
        <model-viewer id="model-viewer" src="{{$model->getModelFileUrl()}}" {{-- ios-src="{{$model->path.$model->file}}" --}} alt="A 3D model of an astronaut" ar auto-rotate camera-controls>
            <button slot="ar-button" class="ar-button"
                style="background-color: rgb(244, 1, 83);
              color: white;
              border-radius: 50px;
              border: none;
              padding: 10px 30px;
              font-size: 16px;
              cursor: pointer;
              z-index: 1;
              width: 220px;
              position: absolute;
              bottom: 30px;
              left: 50%;
              transform: translate(-50%, -50%);
              -webkit-transform: translate(-50%, -50%);
              -moz-transform: translate(-50%, -50%);
              -ms-transform: translate(-50%, -50%);
              -o-transform: translate(-50%, -50%);
              -webkit-border-radius: 50px;
              -moz-border-radius: 50px;
              -ms-border-radius: 50px;
              -o-border-radius: 50px;">
                Trải nghiệm AR
            </button>
        </model-viewer>

        <div id="error" class="hide">AR is not supported on this device</div>
        <button class="ar-button btn-show-ar" id="btn-show-ar" style="z-index:100;display: none;">
                Trải nghiệm AR
            </button>
        <div class="qr-wrapper">
            <div class="img-frame">
                <div class="img">
                    <img src="{{$qr_code_image_url}}" alt="">
                </div>
                <div class="label">
                    Quét mã QR để trải nghiệm AR
                    <br>
                    <br>
                    <a href="{{$u = $model->getViewUrl()}}">{{$u}}</a>
                </div>

                <div class="buttons">
                    <button type="button" class="btn btn-close" id="btn-close">Đóng</button>
                </div>
            </div>
        </div>
        <div class="pre-loading-image">

        </div>

        <div class="load-block">
            <div class="mb-3 text-center">
                {{ siteinfo('site_name') }}
            </div>
            <div class="progress m-progress--lg">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="preview-progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

    </div>
    <script>

        const modelViewer = document.querySelector("#model-viewer");

        const qrWrapper = document.querySelector('.qr-wrapper');
        const qrButton = document.getElementById('btn-close');
        const btnShowAr = document.getElementById('btn-show-ar');
        var prg = document.getElementById('preview-progress-bar');

        function detectOS() {
            let userAgent = window.navigator.userAgent,
                platform = window.navigator.platform,
                macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
                windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
                iosPlatforms = ['iPhone', 'iPad', 'iPod'],
                os = null;

            if (macosPlatforms.indexOf(platform) !== -1) {
                os = 'Mac OS';
            } else if (iosPlatforms.indexOf(platform) !== -1) {
                os = 'iOS';
            } else if (windowsPlatforms.indexOf(platform) !== -1) {
                os = 'Windows';
            } else if (/Android/.test(userAgent)) {
                os = 'Android';
            } else if (!os && /Linux/.test(platform)) {
                os = 'Linux';
            }

            return os;
        }

        function checkDeviceAndShowQRCode(){
            let os = detectOS();
            if(os != 'iOS' && os != 'Android')
                btnShowAr.style.display = 'block';

        }




        modelViewer.addEventListener('ar-status', (event) => {
            if (event.detail.status === 'failed') {
                const error = document.querySelector("#error");
                error.classList.remove('hide');
                error.addEventListener('transitionend', (event) => {
                    error.classList.add('hide');
                });
            }
        });
        modelViewer.addEventListener('progress', (event) => {
            prg.style.width = (event.detail.totalProgress * 100) + '%';
        });
        modelViewer.addEventListener('load', (event) => {
            // console.log(event.detail);
            setTimeout(() => {
                document.querySelector('.pre-loading-image').style.display = 'none';
                document.querySelector('.load-block').style.display = 'none';

                modelViewer.style.opacity = 1;

                checkDeviceAndShowQRCode();
            }, 1000);
        });
        btnShowAr.addEventListener("click", () => {qrWrapper.style.display = 'flex'; btnShowAr.style.display = 'none';});
        qrButton.addEventListener("click", () => {qrWrapper.style.display = 'none'; btnShowAr.style.display = 'block';});
    </script>
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    {{--

        <div id="app-root"></div>

    </div>
    <script>
        var model_data = @json($model->toArray());
    </script>
    <script src="{{asset('static/app/js/r3d.bundle.js')}}"></script>
    <script src="{{asset('static/app/js/model-preview.js')}}"></script> --}}
</body>

</html>
