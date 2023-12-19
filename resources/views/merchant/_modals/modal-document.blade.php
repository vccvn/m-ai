<?php
add_css_link('static/features/video/style.min.css');
add_js_src('static/features/document/modal.document.js?v=' . time());
?>

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
                    <form method="POST" action="{{route('merchant.documents.create-document')}}" id="save-document-form">
                        @csrf
                        <div class="document-form-body">
                            <input type="hidden" id="document-url" value="/merchant/documents/" name="id">
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
                                        <label for="video-title">Tiêu đề</label>
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
                                                <option selected="selected" value="1">Listening</option>
                                                <option value="2">Speaking</option>
                                                <option value="3">Reading</option>
                                                <option value="4">Writing</option>
{{--                                                <option value="5">Video</option>--}}
{{--                                                <option value="6">Radio</option>--}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p>Chọn file tài liệu</p>
                            <section>
                                <div id="dropzone">
                                    <div id="demo-upload" class="custom-dropzone" action="{{route('merchant.documents.upload-file-document')}}">
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