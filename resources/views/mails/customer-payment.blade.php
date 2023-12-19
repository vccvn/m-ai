<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông báo Thanh toán</title>
</head>
<body>
    <div class="cw-wrapper">
        <h3>Xin chào, {{$user->full_name}}</h3>
        <p>
            Một khách hàng của bạn vừa tiến hành thanh toán
        </p>
        <p>
            Mã KH: {{$customer_uuid}}
        </p>
        <p>
            Số tiền: {{get_price_format($total, $currency)}}
        </p>
        <p>
            Số tiền chiết khấu: {{get_price_format($amount, $currency)}}
        </p>

        <p></p>
    </div>
</body>
</html>
