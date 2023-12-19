$(function () {
    $(document).on("click", ".btn-delete-my-style", function(event){
        event.preventDefault();
        var id = $(this).data("id");
        var name = $(this).data('name');
        App.Swal.confirm("Bạn có chắc chắn muốn xóa Style " + name + " không?", function(){
            App.api.post(style_urls.delete, {id: id}).then(function(rs){
                if(rs.status){
                    App.Swal.success("Đã xóa style " + name + " thành công!");
                    $('#personal-style-item-' + id).hide(300, function(){
                        $(this).remove();
                    })
                }else{
                    App.Swal.warning(rs.message?rs.message:"Không xóa được style. Vui lòng thử lại sau giây lát");
                }

            }).catch(function(e){
                App.Swal.warning("Không xóa được style. Vui lòng thử lại sau giây lát");
            });
        })
        
    })
});