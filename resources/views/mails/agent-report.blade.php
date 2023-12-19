<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Báo cáo Doanh thu</title>
</head>
<body>
    <h3>Báo Cáo doanh thu từ ngày {{$from_date}} đến ngày {{$to_date}}</h3>
    <br>
    <br>


    <p>
        Số giao dịch: {{$payment_count}}
    </p>
    <p>
        Tổng số tiền giao dịch: {{$payment_total}}
    </p>
    <p>
        Tổng số tiền chiết khấu: {{$discount_total}}
    </p>

    <br>
    <br>
    <p>
        Xem báo cáo chi tiết: <a href="{{$report_url}}">{{$report_url}}</a>
    </p>
</body>
</html>
