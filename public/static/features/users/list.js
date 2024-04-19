
$(function () {
    $('.btn-reset2fa').click(function (e) {
        e.preventDefault();
        let id = $(this).data('id'), reseturl = $(this).attr('href');

        App.api.post(reseturl, { id: id }).then(function (rs) {
            if (rs.status) {
                App.Swal.success(rs.message);
            } else {
                App.Swal.warning(rs.message);
            }
        }).catch(function (e) {
            App.Swal.error("Lỗi không xác định! Vui lòng thử lại sau giây lát!");
        })
    });

    function addMonth(id, url) {

        Swal.fire({
            title: "Nhập số tháng",
            input: "number",
            inputAttributes: {
                autocapitalize: "off",
                min: 1,
                step: 1,
                value: 1
            },
            // showCancelButton: true,
            confirmButtonText: "Thêm tháng",
            showLoaderOnConfirm: true,
            preConfirm: async (month) => {


            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value) {
                let m = parseInt(result.value);
                if (isNaN(m) || m < 1) {
                    App.Swal.warning("Số tháng không hợp lệ", function () { }, function () {
                        addMonth(id, url);
                    })
                } else {
                    App.api.post(url, { id: id, month: m }).then(function (rs) {
                        if (rs.status) {
                            App.Swal.success(rs.message);
                        } else {
                            App.Swal.warning(rs.message);
                        }
                    }).catch(function (e) {
                        App.Swal.error("Lỗi không xác định! Vui lòng thử lại sau giây lát!");
                    })
                }
            } else if(!result.dismiss && !result.value){
                App.Swal.warning("Số tháng không hợp lệ", function () { }, function () {
                    addMonth(id, url);
                })
            }
        });
    }

    $('.btn-add-month').click(function (e) {
        e.preventDefault();
        let id = $(this).data('id'), reseturl = $(this).attr('href');

        addMonth(id, reseturl);
        // App.api.post(reseturl, { id: id }).then(function (rs) {
        //     if (rs.status) {
        //         App.Swal.success(rs.message);
        //     } else {
        //         App.Swal.warning(rs.message);
        //     }
        // }).catch(function (e) {
        //     App.Swal.error("Lỗi không xác định! Vui lòng thử lại sau giây lát!");
        // })
    });
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
});
