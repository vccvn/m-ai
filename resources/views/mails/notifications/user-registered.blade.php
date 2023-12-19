<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Notification') }}</title>
    <style>
        img{
            max-width: 600px;
            width: ;
        }
    </style>
</head>

<body>
    <div class="cw-wrapper">
        <h3>{{ __('Người dùng mới đăng ký tài khoản dưới tên :name cần xác minh CCCD', ['name' => $user->name]) }}
        </h3>
        <p>
            <span for="first-name" class="col-span">{{ __('Họ và tên') }}:</span> {{ $user->name }}
        </p>
        <span for="last-name" class="col-span">{{ __('Email') }}:</span> {{ $email }}
        </p>
        <p>
            <span for="date-of-birth" class="col-span">{{ __('Số CCCD / CMT') }}:</span> {{ $user->ci_card_number }}
        </p>
        <p>
            Mặt trước:
            <br>
            <img src="{{$user->getCIFrontScanURL()}}" alt="">
        </p>

        <p>
            Mặt sau:
            <br>
            <img src="{{$user->getCIBackScanURL()}}" alt="">
        </p>

    </div>
</body>

</html>

