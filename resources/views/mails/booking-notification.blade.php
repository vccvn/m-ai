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
        <h3>Xin chào, {{$merchant->full_name}}</h3>
        <p>{{$user->full_name}} vừa đặt lịch với thông tin :</p>
        <p>&nbsp;</p>
        <p>
        </p>
        <h3>

            Thông tin người đặt
        </h3>
        <p>
            - Họ và tên: {{$user->full_name}}
        </p>
        <p>
            - Giới tính: {{$user->getGenderLabel()}}
        </p>
        <p>
            - Số điện thoại: {{$user->phone}}
        </p>
        <p>
            - Số người: {{$booking->total_people}}
        </p>
        <p>
            - Thời gian đặt lịch: {{$booking->booking_at}}
        </p>
        <p>&nbsp;</p>
        <h3>
            Thông tin voucher (tóm tắt)
        </h3>
        <p>
        </p>
        <p>
            - Tên chiến dịch: {{$voucher->title}} - {{$voucher->campaign_name}}
        </p>
        <p>
            - Mã voucher: {{$voucher->code}}
        </p>
        <p>
            - Giá trị voucher: {{get_price_format($voucher->original_price, $voucher->currency)}}
        </p>
        <p>
            - Giá trị đã thanh toán: {{get_price_format($voucher->payment_amount, $voucher->currency)}}
        </p>
        <p>
            - Số người trên voucher: {{$voucher->total_people}}
        </p>
        <p>
            - Thời gian (durration): {{$voucher->getDurationTitle()}}
        </p>


    </div>
</body>
</html>
