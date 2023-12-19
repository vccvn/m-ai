<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông báo bị từ chối</title>
</head>
<body>
    <div class="cw-wrapper">
        <p>Xin chào {{$user?$user->name:($user_name??'')}}</p>
        <p>
            Tài khoản của bạn tại website : <a href="https://tongdaituyensinhcand.vn/">https://tongdaituyensinhcand.vn/</a> đang bị tạm khóa do thông tin cá nhân không trùng khớp với căn cước công dân hoặc bạn đã quá độ tuổi quy định. Vui lòng cập nhập lại thông tin hoặc liên hệ tổng đài <a href="tel:1900.0146">1900.0146</a>
        </p>

        <p>
            Trân trọng !
        </p>


    </div>
</body>
</html>
