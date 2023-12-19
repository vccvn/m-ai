<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chúc mừng bạn đã nhận mã khuyến mãi</title>
</head>
<body>
    <div class="cw-wrapper">
        <h3>Xin chào, {{$user?$user->name:($user_name??'')}}</h3>
        <p>
            {!! nl2br($voucher->description) !!}
        </p>

        <p>
            Mã khuyến mãi của bạn là: <strong>{{$voucher->code}}</strong>
        </p>


    </div>
</body>
</html>
