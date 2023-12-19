

$(function () {

    $('.require-item-status-select').on('change', function () {
        let $this = $(this);
        let data = {
            id: $this.data('id'),
            status: $this.val()
        };
        App.api.post(requires.urls.changeStatus, data).then(function (rs) {
            if (!rs.status) App.Swal.error(rs.message);
        });
    });
});
