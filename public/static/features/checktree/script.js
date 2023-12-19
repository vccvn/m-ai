$(function () {
    function checkParent(el) {
        var $checkblockitem = $(el).closest('.check-block-item');
        // return ;

        if (!$checkblockitem.length) {
            return false;
        }
        var $treeblock = $checkblockitem.closest('.check-tree-block');
        if (!$treeblock.length) return false;

        var $parent = $treeblock.closest('.check-block-item');
        if (!$parent || !$parent.hasClass('check-block-item')) return false;
        var $block = $parent.children(".parent-checkbox");
        if (!$block.length) return false;

        var $checkbox = $($block[0]).find("input[type=checkbox]");
        $checkbox.prop("checked", true);
        checkParent($checkbox[0]);

    }

    var canUnCheck = true;
    function unCheckChildren(el) {
        if (canUnCheck) {
            canUnCheck = false;
            $(el).closest('.check-block-item')
                .children('.check-tree-block')
                .find("input[type=checkbox]")
                .prop("checked", false);
            canUnCheck = true;
        }
    }
    $(document).on("change", '.check-tree input[type=checkbox]', function (e) {
        var $tree = $(e.target).closest('.check-tree');
        var ref = $tree.data('ref');

        if(!ref || ref == 0 || ref == '0' || ref == 'false'){
            return;
        }
        var checked = $(this).is(":checked");
        if (checked) {
            checkParent(this);
        } else {
            unCheckChildren(this);
        }

    })
})