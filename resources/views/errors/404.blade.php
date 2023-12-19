<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Không tìm thấy trang | {{siteinfo('site_name')}}</title>
    <link rel="stylesheet" href="{{asset('static/system/errors/404/style.min.css')}}">
</head>
<body>
    <?php
    $content = (isset($message) && $message)?$message:(($msg = session('message'))?$msg:'Dường như bạn đang cố truy cập một đường dẫn không được phép!<br>Vui lòng kiểm tra lại!');
    ?>
    <div class="not-found parallax">
        <div class="sky-bg"></div>
        <div class="wave-7"></div>
        <div class="wave-6"></div>
        <a class="wave-island" href="{{request()->is('admin/*')?route('admin.dashboard'):route('home')}}">
                <img src="http://res.cloudinary.com/andrewhani/image/upload/v1524501929/404/island.svg" alt="Island">
            </a>
        <div class="wave-5"></div>
        <div class="wave-lost wrp">
            <span>4</span>
            <span>0</span>
            <span>4</span>
        </div>
        <div class="wave-4"></div>
        <div class="wave-boat">
            <img class="boat" src="http://res.cloudinary.com/andrewhani/image/upload/v1524501894/404/boat.svg" alt="Boat">
        </div>
        <div class="wave-3"></div>
        <div class="wave-2"></div>
        <div class="wave-1"></div>
        <div class="wave-message">
            <p>Bạn đi lạc rồi!</p>
            <p>Hãy tìm đến hòm đảo để quay trở về!</p>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="{{asset('static/system/errors/404/script.js')}}"></script>
</body>
</html>