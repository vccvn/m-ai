<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông báo</title>
</head>
<body>
    <div class="cw-wrapper">
        <p>
            Xin chào,
            @if (isset($name))
                {{$name}}
            @endif
        </p>
        <p>

            {{siteinfo('site_name')}} xin thông báo:
            <br>
        </p>
        <p>
            Tài khoản của bạn đang bị tạm khóa do không tuân thủ 1 trong các điều khoản sử dụng
        </p>
    </div>
</body>
</html>
