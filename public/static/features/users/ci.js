$('.btn-show-ci-card').on("click", function (e) {
    e.preventDefault();
    var ciFront = $(this).data('front-scan');
    var ciBack = $(this).data('back-scan');

    $('#ci-card-front-image').attr('src', ciFront);

    $('#ci-card-back-image').attr('src', ciBack);
    App.modal.show('ci-card-modal');
})
$('.user-item-status-select').on('change', function () {
    let $this = $(this);
    let data = {
        id: $this.data('id'),
        status: $this.val()
    };
    App.api.post(users.urls.changeStatus, data).then(function (rs) {
        if (!rs.status) App.Swal.error(rs.message);
    });
});



$('.btn-approve-ci').on('click', function () {
    let $this = $(this);
    let data = {
        id: $this.data('id'),
        ci_status: 1
    };
    App.api.post(users.urls.approve, data).then(function (rs) {
        if (!rs.status) App.Swal.error(rs.message);
        else {
            $this.parent().find('.ci-status-text').removeClass('d-none');
            $this.parent().find('.hide-if-approve').hide();
            $this.hide();

        }
    });
});
$('.btn-decline-ci').on('click', function () {
    let $this = $(this);
    // let data = {
    //     id: $this.data('id'),
    //     ci_status: 0
    // };
    App.modal.popup({
        title: "Từ chối duyệt",
        size: "md",
        inputs: {
            subject: {
                type: "text",
                label: "Subject",
                value: "Thông tin CCCD bạn gửi bị từ chối",
                placeholder: "Nhập tiêu đề"
            },

            message: {
                type: "textarea",
                label: "Message (Lý do từ chối)",
                placeholder: "Ví gì đó..."
            }
        },
        done: function (data) {
            data.id = $this.data('id');
            App.api.post(users.urls.decline, data).then(function (rs) {
                if (!rs.status) App.Swal.error(rs.message);
                else {
                    App.Swal.success("Đã gửi thông báo từ chối thành công");
                    $this.parent().find('.ci-status-text').removeClass('d-none').html('Từ chối');
                    $this.parent().find('.hide-if-approve').hide();
                    $this.hide();

                }
            });

        }
    })
});
