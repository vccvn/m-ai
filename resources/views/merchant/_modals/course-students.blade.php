<div class="modal fade course-students-modal" id="course-students-modal" tabindex="-1" role="dialog" aria-labelledby="course-students-modal-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header custom-style bg-info">
                <h5 class="modal-title" id="course-students-modal-title">
                    <i class="fa fa-user-friends"></i>
                    <span>Danh sách học viên</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-form">
                    <form method="GET" action="{{route('merchant.courses.student-list')}}" id="course-students-search-form">
                        <input type="hidden" name="course_id" id="hidden-course-id">
                        <div class="header row mb-2">
                            <div class="col-sm-9 col-md-10 col-xl-11">
                                <div class="form-group row">
                                    <div class="col-6 col-md-4 mb-3">
                                        <input type="text" name="name" id="student-name" class="form-control m-input" placeholder="Họ tên">
                                    </div>
                                    <div class="col-6 col-md-4 mb-3">
                                        {!! html_input(['type' => 'select', 'name' => 'gender', 'data' => get_gender_options('Giới tính'), 'id' => 'student-gender', 'class' => 'form-control m-input']) !!}
                                    </div>
                                    <div class="col-6 col-md-4 mb-3">
                                        <input type="text" name="id" id="student-id" class="form-control m-input" placeholder="ID Học viên">
                                    </div>
                                    <div class="col-6 col-md-4 mb-3">
                                        <input type="text" name="email" id="student-email" class="form-control m-input" placeholder="Email">
                                    </div>
                                    <div class="col-6 col-md-4 mb-3"><input type="text" name="phone_number" id="student-phone-number" class="form-control m-input" placeholder="SĐT"></div>
                                    <div class="col-6 col-md-4 mb-3"><input type="text" name="class_id" id="student-class-id" class="form-control m-input" placeholder="ID Lớp tham gia"></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-2 col-xl-1">
                                <div class="d-sm-none">
                                    <button type="submit" class="btn btn-secondaey"><i class="fa fa-search"></i> Tìm kiếm</button>
                                </div>
                                <div class="d-none d-sm-block text-right">
                                    <button type="submit" class="btn btn-secondaey"><i class="fa fa-search"></i></button>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div class="results student-list">
                    <h3 class="list-title">
                        Danh sách học viên
                    </h3>
                    <div class="list-content">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>ID học viên</th>
                                        <th>Họ tên</th>
                                        <th>Ngày sinh</th>
                                        <th>Giới tính</th>
                                        <th>SĐT</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody id="course-students-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('student-item')
    <tr id="student-item-${id}">
        <td>${id}</td>
        <td>${name}</td>
        <td>${birthday_format}</td>
        <td>${gender_text}</td>
        <td>${phone_number}</td>
        <td>${email}</td>
    </tr>
@endsection
@section('empty-item')
    <tr>
        <td colspan="6" class="empry-item">${message}</td>
    </tr>
@endsection
<?php
    add_js_data('course_students_data', [
        'urls' => [
            'students' => route('merchant.courses.student-list'),
            'updateCR' =>route('merchant.courses.update-cr'),
        ],
        'templates' => [
            'studentItem' => $__env->yieldContent('student-item'),
            'emptyItem' => $__env->yieldContent('empty-item'),
        ],
        'data' => [
            'genders' => get_gender_options()
        ]
    ]);
?>
