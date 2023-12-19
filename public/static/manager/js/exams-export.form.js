const AttributeValue = function () {
    this.ajaxExportItem = function () {
        var id = $("input[name='id']").val();
        var export_number = $("input[name='exams_export']").val();
        ajaxRequest("/admin/exams/export-exams", "GET", {id: id, export_number: export_number}, function (rs) {

        }, function (e) {
            self.isGetting = false;
            App.Swal.error("Lỗi không xác định");
        });
    }
};

$(function () {
    $("#export-data").click(function () {
        let $select = new AttributeValue();
        $select.ajaxExportItem();
    });
});
