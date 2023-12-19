<div class="modal fade" tabindex="-1" role="dialog" id="group-course-enroll-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="group-course-enroll-modalLongTitle">Yêu cầu tham gia Nhóm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-block">
                    <form method="POST" action="{{route('merchant.enrollments.group.approve')}}" id="add-user-to-group-form">
                        @csrf
                        <div class="form-body"></div>
                    </form>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-create-payment-url">Tạo URL thanh toán</button>
                <button type="button" class="btn btn-primary btn-group-approve">Xác nhận</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="one-on-one-course-enroll-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="one-on-one-course-enroll-modalLongTitle">Yêu cầu tham gia Khóa 1 - 1</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-block">
                    <form method="POST" action="{{route('merchant.enrollments.1-on-1.approve')}}" id="add-user-to-one-on-one-form">
                        @csrf
                        <div class="form-body"></div>
                    </form>
                </div>
                
            </div>
            <div class="modal-footer text-center footer-center">
                <button type="button" class="btn btn-info btn-create-payment-url">Tạo URL thanh toán</button>
                <button type="button" class="btn btn-primary btn-one-on-one-approve">Xác nhận</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="create-payment-url-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-payment-url-modalLongTitle">Tạo url Thanh toán khóa học <span class="course-name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-block">
                    <form method="POST" action="{{route('merchant.enrollments.create-payment-url')}}" id="create-payment-form">
                        @csrf

                        <div class="form-body">
                            
                        </div>
                    </form>
                </div>
                
            </div>
            <div class="modal-footer text-center footer-center">
                <button type="button" class="btn btn-primary btn-submit-payment-url">Xác nhận</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


@section('group-form-template')
    <input type="hidden" name="id" value="{$id}">
    <div class="form-group">
        <label for="course-class-id">Lớp tham gia</label>
        <div class="input-group">
            <select name="class_id" id="course-class-id" class="form-control m-input">
                <option value="">Chọn lớp</option>
                ${class_options}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="group-student-id">ID Học Viên</label>
        <div class="input-group">
            <input type="text" name="_student_id" id="group-student-id" class="form-control m-input" value="${user_id}" readonly disabled>
        </div>
    </div>

    <div class="form-group">
        <label for="group-student-name">Họ và tên</label>
        <div class="input-group">
            <input type="text" name="name" id="group-student-name" class="form-control m-input" value="${name}">
        </div>
    </div>
    <div class="form-group">
        <label for="group-student-birthday">Ngày sinh</label>
        <div class="input-group">
            <input type="text" name="birthday" id="group-student-birthday" class="form-control m-input inp-date" value="${birthday}" autocomplete="off" data-format="yyyy-mm-dd">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="la la-calendar"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="group-student-level">Level</label>
        <div class="input-group">
            <input type="text" name="level" id="group-student-level" class="form-control m-input" value="${level}" readonly>
        </div>
    </div>
    <div class="form-group">
        <label for="group-student-phone-number">SĐT</label>
        <div class="input-group">
            <input type="text" name="phone_number" id="group-student-phone-number" class="form-control m-input" value="${phone_number}">
        </div>
    </div>
    <div class="form-group">
        <label for="group-student-email">Email</label>
        <div class="input-group">
            <input type="email" name="email" id="group-student-email" class="form-control m-input" value="${email}">
        </div>
    </div>

@endsection


@section('one-on-one-form-template')
    <input type="hidden" name="id" value="{$id}">
    <div class="form-group">
        <label for="course-teacher-id">Giáo viên phụ trách</label>
        <div class="input-group">
            @include('merchant.forms.templates.crazyselect', [
                'input' => html_input([
                    "type" => "crazyselect",
                    'name' => 'teacher_id',
                    'id' => 'teacher_id',
                    "label" => "Giáo viên phụ trách",
                    "@select-type" => "search",
                    "@search-route" => "merchant.teachers.options",
                    "data" => get_user_options(['type' => 'teacher', '@limit' => 10], "Giáo viên phụ trách")
                ])
            ])
        </div>
    </div>
    <div class="form-group">
        <label for="one-on-one-student-id">ID Học Viên</label>
        <div class="input-group">
            <input type="text" name="_student_id" id="one-on-one-student-id" class="form-control m-input" value="${user_id}" readonly disabled>
        </div>
    </div>

    <div class="form-group">
        <label for="one-on-one-student-name">Họ và tên</label>
        <div class="input-group">
            <input type="text" name="name" id="one-on-one-student-name" class="form-control m-input" value="${name}">
        </div>
    </div>
    <div class="form-group">
        <label for="one-on-one-student-phone-number">SĐT</label>
        <div class="input-group">
            <input type="text" name="phone_number" id="one-on-one-student-phone-number" class="form-control m-input" value="${phone_number}">
        </div>
    </div>
    <div class="form-group">
        <label for="one-on-one-student-birthday">Ngày sinh</label>
        <div class="input-group">
            <input type="text" name="birthday" id="one-on-one-student-birthday" class="form-control m-input inp-date" value="${birthday}" autocomplete="off" data-format="yyyy-mm-dd">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="la la-calendar"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="group-student-level">Level</label>
        <div class="input-group">
            <input type="text" name="level" id="group-student-level" class="form-control m-input" value="${level}" readonly>
        </div>
    </div>
    <div class="form-group">
        <label for="one-on-one-student-email">Email</label>
        <div class="input-group">
            <input type="email" name="email" id="one-on-one-student-email" class="form-control m-input" value="${email}">
        </div>
    </div>
    <div class="mt-4">
        <div for="one-on-one-schedules text-center d-block">Thời khóa biểu</div>
        <div class="input-group-schedules">
        </div>
        <div class="btns text-center mt-3">
            <a href="javascript:;"data-original-title="Thêm" data-toggle="m-tooltip" data-placement="left" title="" class="text-accent btn-add-schedule btn btn-outline-accent btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                <i class="fa fa-plus"></i>
            </a>
        </div>
    </div>

@endsection


@section('create-payment-form-template')
    <input type="hidden" name="enrollment_id" value="{$id}">
    <div class="form-group">
        <label for="one-on-one-student-id">ID Học Viên</label>
        <div class="input-group">
            <input type="text" name="user_id" id="one-on-one-student-id" class="form-control m-input disabled" value="${user_id}" readonly disabled>
        </div>
    </div>

    <div class="form-group">
        <label for="one-on-one-student-email">Email</label>
        <div class="input-group">
            <input type="email" name="email" id="one-on-one-student-email" class="form-control m-input disabled" value="${email}" readonly disabled>
        </div>
    </div>
    
    <div class="form-group">
        <label for="amount-input">Giá</label>
        <div class="input-group">
            <input type="number" name="price" id="amount-input" class="form-control m-input" placeholder="Nhập giá" min="0" step="0.01" value="${price}">
            <div class="input-group-append">
                <select name="currency" class="form-control m-input">
                    ${currency_options}
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="language-input">Ngôn ngữ</label>
        <div class="input-group">
            <select name="locale" id="language-input" class="form-control m-input">
                <option value="en">Tiếng Anh</option>
                <option value="de">Tiếng Đức</option>
                <option value="vi">Tiếng Việt</option>
            </select>
        </div>
    </div>

@endsection


@section('schedule-item')
    <div class="row mt-3" id="one-on-one-schedule-${index}">
        <div class="col-12 col-lg-1 text-right"></div>
        <div class="col-12 col-lg-10">
            <div class="row">
                <div class="col-6 col-sm-4">
                    <label for="one-on-one-schedule-${index}-day">Thứ</label>
                    <div class="input-group">
                        <select name="schedules[${index}][day]" id="one-on-one-schedule-${index}-day" class="form-control m-input">
                            <option value="Mon">Thứ 2</option>
                            <option value="Tue">Thứ 3</option>
                            <option value="Wed">Thứ 4</option>
                            <option value="Thu">Thứ 5</option>
                            <option value="Fri">Thứ 6</option>
                            <option value="Sat">Thứ 7</option>
                            <option value="Sun">Chủ nhật</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-3 col-sm-4">
                    <label for="one-on-one-schedule-${index}-start-time">Giờ bắt đầu</label>
                    <div class="input-group">
                        <input type="text" name="schedules[${index}][start_time]" class="form-control m-input inp-time" id="one-on-one-schedule-${index}-start-time">
                    </div>
                </div>
                
                <div class="col-3 col-sm-4">
                    <label for="one-on-one-schedule-${index}-end-time">Giờ kết thúc</label>
                    <div class="input-group">
                        <input type="text" name="schedules[${index}][end_time]" class="form-control m-input inp-time" id="one-on-one-schedule-${index}-start-time">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-1"></div>
    </div>
@endsection

@php
    add_js_src('static/crazy/js/select.js');
    add_js_src('static/features/enrollments/script.js');
    // add_css_link('static/features/enrollments/style.min.css');
    
    add_js_data('enrollment_data', [
        'templates' => [
            'group_form_body' => $__env->yieldContent('group-form-template'),
            'one_on_one_form_body' => $__env->yieldContent('one-on-one-form-template'),
            'create_payment_form_body' => $__env->yieldContent('create-payment-form-template'),
            'schedule_item' => $__env->yieldContent('schedule-item'),
        ],
        'config' => get_enroll_config_data(),
        'urls' => [
            'approve_video' => route('merchant.enrollments.video.approve')
        ]
    ])
@endphp