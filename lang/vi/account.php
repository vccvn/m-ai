<?php

return [
    'auth' => [
        'register' => [
            'validation' => [
                'full_name-max'                        => 'Họ tên dài vựa quá số ký tự',
                'full_name-required'                   => 'Bạn chưa nhập họ và tên',

                'email-required'                       => 'Bạn chưa nhập email',
                'email-email'                          => 'Email không hợp lệ',
                'email-unique_attr'                    => 'Do vấn đề bảo mật, Bạn không thể sử dụng email',

                'password-required'                    => 'Bạn chưa nhập mật khẩu',
                'password-password_safe'               => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
                'password-min'                         => 'Mật khẩu cần phải có ít nhất 8 ký tự, bao gồm in hoa, in thường, ký tự đặc biệt',
                'password-confirmed'                   => 'Mật khẩu không khớp',

                'phone-required'                       => 'Số điện thoại không được bỏ trống',
                'phone-phone_number'                   => 'Số điện thoại không hơp lệ',
                'phone-phone_exists'                   => 'Số điện thoại Đã được sử dụng',

                'status-required'                      => 'Trạng thái không hợp lệ',


                'avatar-required'                      => 'Ảnh đại diện không được bỏ trống',
                'avatar-mimes'                         => 'Ảnh đại diện không đúng dịnh dạng',

            ]
        ]
    ],
            
    'sign-in'               => 'ĐĂNG NHẬP',
    'sign-in-now'           => 'ĐĂNG NHẬP',
    'sign-up-now'           => 'ĐĂNG KÝ',
    'login'                 => 'ĐĂNG NHẬP',
    'username'              => 'Tên đăng nhập *',
    'password'              => 'Mật khẩu *',
    'your_user'             => 'Điền tên đăng nhập của bạn',
    'your_password'         => 'Điền mật khẩu của bạn',
    'not_robot'             => 'Tôi không phải là robot',
    'forget_password'       => 'Quên mật khẩu?',
    'click_here'            => 'Click vào đây',
    'not_an_account'        => 'Không có tài khoản?',
    'an_account'            => 'Đã có tài khoản?',
    'register'              => 'ĐĂNG KÝ',
    'email'                 => 'Email *',
    'phone'                 => 'Số điện thoại',
    'confirm_password'      => 'Xác nhận mật khẩu *',
    'reset_password'        => 'Đặt lại mật khẩu',
    'sign_up'               => 'ĐĂNG KÝ',
    'update_account'        => 'Lưu thông tin',
    'your_email'            => 'Điền email của bạn',
    'your_phone'            => 'Điền số điện thoại của bạn',
    'your_name'             => 'Điền họ tên của bạn',
    'your_level'            => 'Điền level',
    'name'                  => 'Họ và tên *',
    'level'                 => 'Level',
    'logout'                => 'Đăng xuất',
    'current_password'      => 'Mật khẩu hiện tại *',
    'your_current_password' => 'Điền mật khẩu hiện tại của bạn',
    'birthday'              => 'Ngày sinh *',
    'your_birthday'         => 'Điền sinh nhật của bạn',
    'gender'                => 'Giới tính (required)',
    'your_gender'           => 'Điền giới tính của bạn',
    'verify'                      => 'Xác minh',
    'verify_account'              => 'Xác minh tài khoản',
    'forgot_message'              => "Nếu email của bạn chính xác bạn sẽ nhân được email với link đặt lại mật khẩu trong thời giam sớm nhất",
    'verify_message'              => "Nếu email của bạn chính xác bạn sẽ nhân được email với link xác minh tài khoản trong thời giam sớm nhất",
    'not_verify_message'          => "Tài khoản xác minh",
    'verify_success'              => "Xác minh tài khoản thành công!",
    'get_active_account_request'  => 'Yêu cầu xác minh tài khoản',
    'deactive'                    => 'Tài khoản này đã bị vô hiệu hoá',
    'reset_password_mail_subject' => 'Đặt lại mật khẩu trên :site',
    'reset-note'                  => 'Vui lòng nhập email để chúng tôi gửi mật khâu cho bạn',
    'action_error'                => 'Có lỗi xảy ra. Vui lòng thử lại sau',
    'genders'                     => [
        'male'   => 'Nam',
        'female' => 'Nữ',
        'other'  => 'Khác'
    ],
    'update_success'        => 'Chỉnh sửa thông tin thành công',
    'update_password'       => 'Thay đổi mật khẩu thành công',
    'change-avatar' => 'Đổi ảnh',
    'name-of-user' => 'Tên người dùng',
    'date-of-birth' => 'Ngày sinh',
    'id-number' => 'Mã ID',
    'change-the-password' => 'Dởi mật khẩu',
    'study-now' => 'Vào học',
    'class-id' => 'Mã lớp',
    'teacher' => 'Giáo viên',
    'zoom-link' => 'Link Zoom',
    'schedule' => 'Lịch học',
    'note-title' => 'Chú ý',
    'note-content' => 'Bạn tuân thủ các thỏa thuận về thời gian theo giờ—trang phục chuyên nghiệp, đặc biệt là cho khóa đào tạo của công ty. Nếu bạn không thể tham dự, bạn phải thông báo cho nhà trường ít nhất 24 giờ trước khi bắt đầu buổi học. Nếu bạn báo muộn với nhà trường, chúng tôi sẽ tính tiền học đầy đủ của ngày bạn không tham gia.',
    'sign-up-success' => 'Xin chúc mừng bạn đã đăng ký thành công!',
    'sign-up-verify' => 'Bạn đã đăng ký tài khoản thành công! <br /> Vui lòng chờ trong khi chúng tôi xác minh thông tin',
];
