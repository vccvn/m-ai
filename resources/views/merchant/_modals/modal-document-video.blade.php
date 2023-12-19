<?php
add_css_link('static/features/video/style.min.css');
add_js_src('static/features/document/modal.document.js?v=' . time());
?>
<style>
    .type-video-hide {
        display: none;
    }
</style>
<div class="modal fade" tabindex="-1" role="dialog" id="add-document-modal">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Thêm file</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-block">
                    <form method="POST" action="{{route('merchant.document-videos.create-document')}}" id="save-document-form">
                        @csrf
                        <div class="document-form-body">
                            <input type="hidden" id="document-url" value="/merchant/document-videos/" name="id">
                            <input type="hidden" id="document-id" value="0" name="id">
                            <input type='hidden' id="document-id-new" name='document-id-new' value="0">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="video-file-select">
                                        <label for="video-title">Ảnh</label>
                                        <div class="thumbnail">
                                            <div class="ratio"></div>
                                            <img id="document-thumbnail-image" alt="">
                                        </div>
                                        <input type="file" class="thumbnail-file-input" id="thumbnail-file-input"
                                               name="image" accept="image/*" required>
                                        <div class="filename" id="video-thumbnail"></div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="video-title">Tiêu đề </label>
                                        <div class="input-group">
{{--                                            <input type="text" name="title" id="title-document"--}}
{{--                                                   class="form-control m-input" required>--}}
                                            <textarea type="text" name="title" id="title-document"
                                                      class="form-control m-input" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="video-title">Mô tả</label>
                                        <div class="input-group">
                                            <textarea type="text" name="description" id="description-document"
                                                      class="form-control m-input" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="video-description">Danh mục tài liệu</label>
                                        <div class="input-group">
                                            <select name="category" id="category-document" required>
                                                <option value="5" selected="selected">Video</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{--                                    <div class="form-group">--}}
                                    {{--                                        <label for="video-description">Loại video</label>--}}
                                    {{--                                        <div class="input-group">--}}
                                    {{--                                            <select name="type_video" id="type_video" onchange="changeTypeVideo()"--}}
                                    {{--                                                    required>--}}
                                    {{--                                                <option value="file" selected="selected">Tập tin</option>--}}
                                    {{--                                                <option value="link">Đường dẫn</option>--}}
                                    {{--                                            </select>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    <div class="form-group" id="video-link">
                                        <label for="video-description">Link video</label>
                                        <div class="" id="link-item">
                                            <div class="input-group mb-2 link-item">
                                                <input type="text" class="form-control m-input" name="link_video[]">
                                            </div>
                                        </div>
                                    </div>
{{--                                    <div class="form-group" id="video-link">--}}
{{--                                        <div class="col-12 m-auto button-add" style="text-align: center;">--}}
{{--                                            <p class="mt-3 ml-3 btn btn-outline-primary m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill m-btn--air"--}}
{{--                                               id="add-link-video-item"><i class="fa fa-plus"></i></p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 " id="video-file">
                            <p>Chọn file tài liệu</p>
                            <section>
                                <div id="dropzone">
                                    <div id="demo-upload" class="custom-dropzone"
                                         action="{{route('merchant.document-videos.upload-file-document')}}">
                                        <div class="dz-message needsclick">
                                            Chọn file<br>
                                        </div>
                                        <input type="hidden" name="author" value="" id="file-author">
                                    </div>
                                </div>
                            </section>
                            <br/>
                            <hr size="3" noshade color="#F00000">
                            <div id="preview-template" style="display: none;">
                                <div class="dz-preview dz-file-preview">
                                    <div class="dz-image"><img data-dz-thumbnail=""></div>
                                    <div class="dz-details">
                                        <div class="dz-size"><span data-dz-size=""></span></div>
                                        <div class="dz-filename"><span data-dz-name=""></span></div>
                                    </div>
                                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span>
                                    </div>
                                    <div class="dz-error-message"><span data-dz-errormessage=""></span></div>
                                    <div class="dz-success-mark">
                                        <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                                            <title>Check</title>
                                            <desc>Created with Sketch.</desc>
                                            <defs></defs>
                                            <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                               fill-rule="evenodd"
                                               sketch:type="MSPage">
                                                <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                      id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                      fill-opacity="0.816519475" fill="#FFFFFF"
                                                      sketch:type="MSShapeGroup"></path>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-save-document">Cập nhật</button>
                <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var dropzone = new Dropzone('#demo-upload', {
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            parallelUploads: 2,
            thumbnailHeight: 120,
            thumbnailWidth: 120,
            maxFilesize: 3,
            filesizeBase: 1000,
            thumbnail: function (file, dataUrl) {
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    for (var i = 0; i < images.length; i++) {
                        var thumbnailElement = images[i];
                        thumbnailElement.alt = file.name;
                        thumbnailElement.src = dataUrl;
                    }
                    setTimeout(function () {
                        file.previewElement.classList.add("dz-image-preview");
                    }, 1);
                }
            },
        });
        //
        //
        var minSteps = 6,
            maxSteps = 60,
            timeBetweenSteps = 100,
            bytesPerStep = 100000;

        dropzone.uploadFiles = function (files) {
            var self = this;

            for (var i = 0; i < files.length; i++) {

                var file = files[i];
                totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

                for (var step = 0; step < totalSteps; step++) {
                    var duration = timeBetweenSteps * (step + 1);
                    setTimeout(function (file, totalSteps, step) {
                        return function () {
                            file.upload = {
                                progress: 100 * (step + 1) / totalSteps,
                                total: file.size,
                                bytesSent: (step + 1) * file.size / totalSteps
                            };

                            self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                            if (file.upload.progress == 100) {
                                file.status = Dropzone.SUCCESS;
                                self.emit("success", file, 'success', null);
                                self.emit("complete", file);
                                self.processQueue();
                                //document.getElementsByClassName("dz-success-mark").style.opacity = "1";
                            }
                        };
                    }(file, totalSteps, step), duration);
                }
            }
        }
    });

    function changeTypeVideo() {
        let type = document.getElementById("type_video").value;
        if (type === 'link') {
            document.getElementById('video-link').classList.remove("type-video-hide");
            document.getElementById('video-file').classList.add("type-video-hide");
        } else {
            document.getElementById('video-link').classList.add("type-video-hide");
            document.getElementById('video-file').classList.remove("type-video-hide");
        }
    }

    // function myFunction() {
    //     document.getElementById("link-item").innerHTML += "<div class='input-group' ><input type='text' class='form-control m-input' name='link_video[]'></div>";
    // }

</script>
