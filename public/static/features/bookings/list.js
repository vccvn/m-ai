$(function(){
    $(document).on("change", ".booking-item-status-select", function(){
        App.api.post(booking_data.urls.change_status, {id: $(this).data('id'), status: $(this).val()})
        .then(rs => rs.status?false:App.Swal.warning(rs.message))
        .catch(e => App.Swal.error(e.message?e.message:"Lỗi không xác định"))
    })
})
