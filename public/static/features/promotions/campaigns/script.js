$(function () {
    $(document).on("click", ".btn-campaign-action", function (event) {
        event.preventDefault();
        const RUNNING = 'running';
        const STOP = 'stopped';
        var $this = $(this);
        var status = $this.attr('campaign-status');
        var url = status == STOP ? campaign_data.urls.run : campaign_data.urls.stop;
        // console.log(this, status);
        App.api.post(url, { id: $this.data('id') })
            .then(rs => {
                if (rs.status) {
                    if (rs.data.status == STOP) {
                        $this.removeClass('btn-stop-campaign')
                            .removeClass('btn-danger')
                            .addClass('btn-run-campaign')
                            .addClass('btn-info')
                        $this.find('i')
                            .removeClass('fa-stop')
                            .addClass('fa-bolt');
                        $this[0].setAttribute('data-original-title', "Chạy chiến dịch");
                        $this.data('original-title', "Chạy chiến dịch");
                    } else {
                        $this.removeClass('btn-run-campaign')
                            .removeClass('btn-info')
                            .addClass('btn-stop-campaign')
                            .addClass('btn-danger')
                        $this.find('i')
                            .removeClass('fa-bolt')
                            .addClass('fa-stop');
                            $this.data('original-title', "Chạy chiến dịch");
                            $this[0].setAttribute('data-original-title', "Chạy chiến dịch");
                    }
                    $this.attr('campaign-status', rs.data.status);
                    $('#crazy-item-' + rs.data.id + ' .status-column').html(rs.data.status_label);
                    $('#crazy-item-' + rs.data.id + ' .btn-edit-campaign').attr("status",rs.data.status);
                }
                else {
                    App.Swal.warning(rs.message ? rs.message : "Lỗi không xác định");
                }
            })
            .catch(e => {
                App.Swal.error(e.message || "Lỗi không xác dịnh");
            })

    })
})
