<!DOCTYPE html>
<html lang="vi">
<head>
    <?php
    $site_name = 'Mangala';
    $web_title = ($full_title = $__env->yieldContent('full_title'))?$full_title:(
        ($short_title = $__env->yieldContent('title'))?$short_title.' | '.$site_name : 'Trang chủ'.' | '.$site_name
    );



?>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">


    @if ($__env->yieldContent('meta.robots') == 'noindex')
    <meta name="robots" content="noindex,follow"/>
    <meta name="googlebot" content="noindex" />
    @endif



    <title>@yield('meta_title', $web_title)</title>
    <meta property="og:site_name" content="{{$site_name}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- http-equive -->
    <meta http-equiv="Content-Language" content=”vi”>
    <meta http-equiv="Content-Type" content=”text/html; charset=utf-8″>
    <!-- /http-equive -->


    <meta name="title" content="@yield('meta_title', $web_title)">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <!-- Font Icon -->
    <link rel="stylesheet" href="{{asset('static/system/forms/colorlib-regform-7/fonts/material-icon/css/material-design-iconic-font.min.css')}}">

    <!-- Main css -->
    <link rel="stylesheet" href="{{asset('static/system/forms/colorlib-regform-7/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('static/system/forms/colorlib-regform-7/css/custom.css')}}">

    <style>
        .form-group.has-error{
            margin-bottom: 5px;
        }
        .error-message{
            /* margin-bottom: 20px; */
            color: orangered;
            position: relative;
        }
    </style>
</head>
<body>

    <div class="main">
        @yield('content')
    </div>

    <!-- JS -->
    <script src="{{asset('static/system/forms/colorlib-regform-7/vendor/jquery/jquery.min.js')}}"></script>

    <script src="{{asset('static/plugins/axios/axios.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

    <script src="{{asset("static/app/js/app.min.js")}}"></script>

    <script src="{{asset('static/system/forms/colorlib-regform-7/js/main.js')}}"></script>

    <script>


        window.csrfTokenInit = function () {
            App.csrf.init({
                urls: {!!
                    json_encode([
                        'token' => route("account.token")
                    ])
                !!}
            });
        };

    </script>


    <script src="{{asset("static/app/js/app.modules.min.js")}}"></script>

    @php
        $messKeys = ['warning', 'success', 'error', 'info', 'alert'];
    @endphp
    <script>

        App.onModuleLoaded(function(){
            App.task(function(){

        // @foreach ($messKeys as $key)
        //  @if ($_message = session($key.'_message'))

                App.Swal.{{$key}}({!!json_encode($_message)!!});

            // @endif

        // @endforeach

            })

        })
    </script>

    @yield('js')
</body>

</html>
