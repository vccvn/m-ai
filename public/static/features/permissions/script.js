$(function () {
    function checkCheckedList($tr) {
        var checked = $tr.find(".check-one:checked").length;
        if ($tr.find(".check-one").length == checked && checked > 0) {
            $tr.find(".check-all").prop("checked", true);
        }
        else{
            $tr.find(".check-all").prop("checked", false);
        }
    }

    function checkCheckedGroup($table, group){
        var checked = $table.find('.check-one[data-cross-group="'+group+'"]:checked').length;
        if ($table.find('.check-one[data-cross-group="'+group+'"]').length == checked && checked > 0) {
            $table.find('.check-cross-group[data-group="'+group+'"]').prop("checked", true);
        }
        else{
            $table.find('.check-cross-group[data-group="'+group+'"]').prop("checked", false);
        }
    }
    $(document).on("click", '.module-check-item', function (e) {
        var $this = $(this);
        var $tr = $(this).closest('tr');
        if ($this.hasClass('check-all')) {
            if ($this.is(":checked")) {
                $tr.find(".check-one").prop("checked", true);
            } else {
                $tr.find(".check-one").prop("checked", false);
            }
        } else {
            checkCheckedList($tr);
            checkCheckedGroup($tr.closest('table'), $this.data('cross-group'));
        }
        $tr.find('.check-change').val("1");
        
    });

    $(".module-table tbody tr").each(function (i, e) {
        checkCheckedList($(e));
    });

    $(document).on("click", '.check-cross-all', function (e) {
        var $this = $(this);
        var $table = $(this).closest('table');
        if ($this.is(":checked")) {
            $table.find(".check-one").each(function(i, e){
                if(!$(e).is(":checked")){
                    $(e).prop("checked", true);
                    var $tr = $(e).closest('tr');
                    $tr.find('.check-change').val("1");
                }
                
            });
            $table.find("thead tr th input[type=checkbox]").prop("checked", true);
            $table.find(".check-all").prop("checked", true);
        } else {
            $table.find(".check-one").each(function(i, e){
                if($(e).is(":checked")){
                    $(e).prop("checked", false);
                    var $tr = $(e).closest('tr');
                    $tr.find('.check-change').val("1");
                }
            });
            $table.find("thead tr th input[type=checkbox]").prop("checked", false);
            $table.find(".check-all").prop("checked", false);
        }
        
        // $table.find('.check-change').val("1");
    });
    $(document).on("click", '.check-cross-group', function (e) {
        var $this = $(this);
        var $table = $(this).closest('table');
        var group = $this.data("group");
        if ($this.is(":checked")) {

            $table.find('.check-one[data-cross-group="'+group+'"]').each(function(i, e){
                if(!$(e).is(":checked")){
                    $(e).prop("checked", true);
                    var $tr = $(e).closest('tr');
                    $tr.find('.check-change').val("1");
                    checkCheckedList($tr);
                }
                
            });
            
        } else {
            $table.find('.check-one[data-cross-group="'+group+'"]').each(function(i, e){
                if($(e).is(":checked")){
                    $(e).prop("checked", false);
                    var $tr = $(e).closest('tr');
                    $tr.find('.check-change').val("1");
                    checkCheckedList($tr);
                }
                
            });
            
        }
        
        // $table.find('.check-change').val("1");
    });


    

    
});