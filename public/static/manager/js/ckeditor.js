// const { CKPlugins } = require("../../plugins/ckeditor5/plugins/ckplugins");

window.CKEDITOR_CLOUD_URL = '/static/plugins/ckeditor5/crack';
function getCkEditor(mode) {
    var list = {
        balloon: CKEDITOR.BalloonEditor,
        classic: CKEDITOR.ClassicEditor,
        clean: CKEDITOR.CleanEditor,
        edu: CKEDITOR.EduEditor,
        math: CKEDITOR.EduEditor,
        inline: CKEDITOR.InlineEditor,
        word: CKEDITOR.WordEditor
    };
    var m = App.str.replace(String(mode).toLowerCase(), "editor", "");
    if (!Object.prototype.hasOwnProperty.call(list, m)) m = 'classic';
    return list[m];
}

function addCkeditor(selector, onChange, onCheck, onError) {
    var $e = $(selector);

    var config = {};
    if ($e.hasClass('in-ckeditor-mode')) return false;
    $e.addClass('in-ckeditor-mode');
    var id = $e.attr('id');
    var $input = $e;
    var h = 200;
    if ($e.attr('height')) {
        h = $e.attr('height');
    }
    var editorMode = $e.attr('mode');
    if (['inline', 'balloon', 'word'].indexOf(editorMode) != -1) {
        $input = $('#' + $e.data('input-id'));
    }

    var onSetup = null;

    if (onChange && typeof onChange == "object") {
        config = onChange;
        if (typeof config.check == "function") onCheck = config.check;
        else if (typeof config.checked == "function") onCheck = config.checked;
        else if (typeof config.onCheck == "function") onCheck = config.onCheck;
        else if (typeof config.oncheck == "function") onCheck = config.oncheck;

        if (typeof config.change == "function") onChange = config.change;
        else if (typeof config.changed == "function") onChange = config.changed;
        else if (typeof config.onChange == "function") onChange = config.onChange;
        else if (typeof config.onchange == "function") onChange = config.onchange;

        if (typeof config.setup == "function") onSetup = config.setup;
        else if (typeof config.onSetup == "function") onChange = config.onSetup;
    }
    var _ck = getCkEditor(editorMode)
        .create($e[0], {
            // plugins: [CKPlugins.SimpleUploadAdapter],
            fontSize: {
                options: [
                    'default',
                    9,
                    11,
                    13,
                    14,
                    15,
                    16,
                    17,
                    18,
                    19,
                    20,
                    24,
                    27,
                    32,
                    36,
                    40,
                    48

                ]
            },
            simpleUpload: {
                // The URL that the images are uploaded to.
                uploadUrl: CKEDITOR_DATA.urls.upload,

                // Enable the XMLHttpRequest.withCredentials property.
                withCredentials: true,

                // Headers sent along with the XMLHttpRequest to the upload server.
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            htmlSupport: {
                allow: [
                    {
                        name: "img",
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ],
                disallow: [
                    {
                        name: /.*/,
                        attributes: {
                            size: true
                        }
                    }
                ]
            }
        }
        )
        .then(editor => {
            // console.log(editor)
            if (['inline', 'balloon', 'word'].indexOf(editorMode) != -1) {
                if (h && h != 'auto') {
                    $('#' + $e.data('wrapper-id') + ' .row-editor').css({ maxHeight: h + "px" });
                }
                // Set a custom container for the toolbar.
                document.querySelector('#' + $e.data('wrapper-id') + ' .document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
                document.querySelector('#' + $e.data('wrapper-id') + ' .ck-toolbar').classList.add('ck-reset_all');
            }
            var editor_changed = false;

            editor.model.document.on('change:data', () => {
                editor_changed = true;
                var data = editor.getData();
                // $input.val(data);
                if (typeof onChange == "function") {
                    onChange(data, editor);
                }
            });

            editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                if (!isFocused && editor_changed) {
                    editor_changed = false;
                    var data = editor.getData();
                    $input.val(data);
                    if (typeof onChange == "function") {
                        onChange(data, editor);
                    }
                }
            });
            setInterval(function () {
                var data = editor.getData();
                $input.val(data);
                if (typeof onCheck == "function") {
                    onCheck(data, editor);
                }

                if (typeof onSetup == "function") {
                    onSetup(editor);
                }
            }, 200);


            // editor.addCommand("mySimpleCommand", {
            //     exec(edt) {
            //         console.log(edt.getData());
            //     }
            // });

            // editor.ui.addButton('SuperButton', {
            //     label: "Click me",
            //     command: 'mySimpleCommand',
            //     toolbar: 'insert',
            //     icon: 'https://avatars1.githubusercontent.com/u/5500999?v=2&s=16'
            // });
        })
        .catch(error => {
            if (typeof onError == "function") {
                onError();
            }
            console.error(error);
        });

}

jQuery(window).on('load', function () {

    var $mini = $('textarea.crazy-ckeditor,ck-editor');
    if ($mini.length) {

        $mini.each((i, e) => {
            addCkeditor(e);
        });


    }
})
