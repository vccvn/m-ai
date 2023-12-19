$(function(){
    function downloadItems(url, ids) {
        App.Swal.showLoading();
        App.api.post(url, {ids}).then(rs => {

            if(rs.status){
                var a = document.createElement('a');
                a.setAttribute('target', '_blank');
                a.setAttribute('href', rs.data.url);
                a.click();
                App.Swal.hideLoading()
            }else{

                App.Swal.warning(rs.message);
            }
        }).catch(e => {
            App.Swal.error("Lỗi hệ thống");
        })
    }
    function downloadForm(url) {
        var formData = new FormData(document.getElementById('pt-desktop-form'))
        App.Swal.showLoading();
        App.api.post(url,formData).then(rs => {

            if(rs.status){
                var a = document.createElement('a');
                a.setAttribute('target', '_blank');
                a.setAttribute('href', rs.data.url);
                a.click();
                App.Swal.hideLoading()
            }else{

                App.Swal.warning(rs.message);
            }
        }).catch(e => {
            App.Swal.error("Lỗi hệ thống");
        })
    }
    $(document).on("click", ".btn-export", function(e){
        e.preventDefault();
        try {


        var check_selector = '.crazy-list input[type="checkbox"].crazy-check-';
            var list = $(check_selector + 'item:checked');
            var ids = [];
            if (list.length == 0) {
                return downloadForm($(this).attr('href'));
            }
            for (var i = 0; i < list.length; i++) {
                ids[ids.length] = $(list[i]).val();
            }
            downloadItems($(this).attr('href'), ids);
        } catch (error) {

        }
        return false;
    })
})
