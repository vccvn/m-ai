<?php

return [
    'modules' => [
        'home' => [
            'pageTitle' => 'Trang chủ'
        ],
        'alert' => [
            'pageTitle' => 'Thông báo',
            'defaultMessage' => 'Đây là trang thông báo',
            'buttonText' => 'Về trang chủ'
        ],
        'contacts' => [
            'pageTitle' => 'Liên hệ'
        ],
        'errors' => [
            404 => [
                'pageTitle' => '404 - Không tìm thấy',
                'alertTitle' => 'Rất tiếc! Trang bạn đang truy cập hiện không được tìm thấy hoặc đã bị xóa',
                'message' => 'Bạn có thể sử dụng mục tìm kiếm hoặc về trang chủ',
                'button' => 'về trang chủ'
            ]
        ],
        'examTests' => [
            'pageTitle' => 'Làm bài thi',
            'doing' => [
                'pageTitle' => 'Làm bài thi'
            ],
            'result' => [
                'pageTitle' => 'Kết quả thi'
            ]
        ],
        'exams' => [
            'myExams' => [
                'pageTitle' => 'Đề đã thi',
                'noteTitle' => 'Lưu ý',
                'noteContent' => 'Với mỗi đề thi, hệ thống chỉ lưu kết quả trong lần làm bài thi đầu tiên của thí sinh',
                'history' => 'Lịch sử làm bài',

            ],

            'types' => [
                'protected' => 'Bí mật',
                'public' => 'Công khai'
            ],
            'texts' => [
                'redo' => 'Thi lại',
                'result' => 'Kết quả',
                'viewResult' => 'Xem kết quả',
                'viewHistory' => 'Xem lịch sử',
                'noResult' => 'Chưa có bài thi nào'
            ]
        ],
        'payment' => [
            'title' => 'Thanh toán'
        ]
    ],
    'common' => [
        'email' => 'Email',
        'phoneNumber' => "Số điện thoại",
        'hotline' => "Hotline",
        'address' => 'Địa chỉ',
        'age'                   => 'tuổi',
        'date'                  => 'ngày',
        'day'                   => 'ngày',
        'birthday'              => 'ngày sinh',
        'hour'                  => 'giờ',
        'minute'                => 'phút',
        'month'                 => 'tháng',
        'name'                  => 'tên',
        'second'                => 'giây',
        'time'                  => 'thời gian',
        'year'                  => 'năm',

        'inputs'                => [
            'name'              => 'Tên',
            'email'             => 'Địa chỉ email',
            'phoneNumber'       => "Số điện thoại",
            'hotline'           => "Hotline",
            'address'           => 'Địa chỉ',
            'contactContent'    => 'Nội dung liên hệ',
            'subject'           => 'Chủ đề'
        ]
    ]
];
