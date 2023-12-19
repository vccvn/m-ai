

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
