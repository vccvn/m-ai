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
        <h3>Xin chào {{$merchant->full_name}}
        </h3>
        <p>{{$user->full_name}} vừa gửi liên hệ cho bạn vs những thông tin sau:</p>

        <br>
        @if ($m = $contact->email??$user->rmail)
            <p>Email: {{$m}}</p>
        @endif
        @if ($p = $contact->phone??$user->phone)
            <p>Số điện thoại: {{$p}}</p>
        @endif
        <br>



        @if ($contact->subject)
            <h4>Tiêu đề {{$contact->subject}}</h4>
        @endif
        @if ($contact->message)
            <p>Nội dung:</p>
            <p>{!! nl2br($contact->message) !!}</p>
        @endif


    </div>
</body>
</html>
