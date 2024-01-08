

$(function () {

    $('.cover-letter-status-select').on('change', function () {
        let $this = $(this);
        let data = {
            id: $this.data('id'),
            status: $this.val()
        };
        App.api.post(COVER_LETTER.urls.changeStatus, data).then(function (rs) {
            if (!rs.status) App.Swal.error(rs.message);
        });
    });
});
