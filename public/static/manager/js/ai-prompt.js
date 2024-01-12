
function addAiPromptEditor($content) {
    var tinyEditor;
    var h = 300;
    // var $content = $('textarea.ai-prompt');
    if ($content.attr('height')) {
        h = $content.attr('height');
    }

    var options = {
        selector: '#' + $content.attr('id'),
        branding: false,
        language: "vi",
        height: h,
        plugins: [
            'autosave save directionality ',
            'visualblocks visualchars ',
            'textpattern noneditable'
            // "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            // "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
            // "table contextmenu directionality emoticons paste textcolor code openGallery" advcode
        ],
        mobile: {
            plugins: 'autosave save directionality noneditable'
        },
        image_advtab: true,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,

        menubar: false,
        // menubar: 'file edit insert format tools view help',
        // toolbar: 'undo redo | bold italic underline strikethrough |
        // fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify |
        // outdent indent |  numlist bullist  | forecolor backcolor casechange formatpainter removeformat |
        // pagebreak | charmap emoticons | preview save print | insertfile image media  template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
        // styleselect

        // contextmenu: 'bold italic underline | alignleft aligncenter alignright alignjustify | link',
        content_css: [
            '//fonts.googleapis.com/css?family=Source+Sans+Pro',
            '/static/plugins/tinymce/style.css',
            '/static/plugins/tinymce/bs4/bootstrap-grid.min.css',
            '/static/plugins/ai-prompt/style.css'

        ],
        // toolbar: 'formatselect | fontsizeselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist
        // |  forecolor backcolor | image | link | table | fullscreen | outdent indent |removeformat',
        toolbar: false,

        // extended_valid_elements : 'i[*],span[*]',
    };
    tinymce.init(options);

};

if ($('textarea.ai-prompt').length) {
    var $content = $('textarea.ai-prompt');
    for (let index = 0; index < $content.length; index++) {
        const element = $content[index];
        addAiPromptEditor($(element));
    }

}
window.addNonEditableBlock = function (id, text) {
    var $editor = tinymce.activeEditor;
    var content = '<span class="mceNonEditable criteria-tag" role="criteria" data-id="' + id + '">[' + text + ']</span>';
    $editor.execCommand('mceInsertContent', false, content);
}

$(function () {
    $(document).on("click", ".criteria-list .criteria-item", function (e) {
        e.preventDefault();
        window.addNonEditableBlock($(this).data('id'), $(this).data('label'));
    })
})
