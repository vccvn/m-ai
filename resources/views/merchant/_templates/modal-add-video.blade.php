<?php

add_css_link('static/features/video/style.min.css');
?>

<div class="modal fade" tabindex="-1" role="dialog" id="add-video-modal">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Cập nhật Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-block">
                    <form method="POST" action="{{route('merchant.courses.videos.upload')}}" id="save-video-form">
                        @csrf
                        <div class="video-form-body"></div>
                    </form>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-save-Video">Cập nhật</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('video-form-template')
    <input type="hidden" name="id" value="{$id}">
    <div class="row">
        <div class="col-md-5">
            <div class="video-file-select">
                <div class="thumbnail">
                    <div class="ratio"></div>
                    <img id="video-thumbnail-image" src="{$thumbnail_url}" alt="{$name}">
                </div>
                <div class="video-play-area">
                    <video id="video-player" src="{$url}" type="{$type}" controls></video>
                    <canvas id="video-canvas"></canvas>

                </div>
                <input type="file" class="thumbnail-file-input" id="thumbnail-file-input" name="thumbnail" accept="image/*">
                <div class="filename" id="video-thumbnail">{$thumbnail}</div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group">
                <label for="video-title">Tiêu đề</label>
                <div class="input-group">
                    <input type="text" name="title" id="video-title" class="form-control m-input" value="{$title}">
                </div>
            </div>
            <div class="form-group">
                <label for="video-description">Mô tả</label>
                <div class="input-group">
                    <textarea name="description" id="video-description" class="form-control m-input">{$description}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="video-file">Video</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="video" id="video-file" class="custom-file-input video-file-input" accept="video/*" data-on-change="onChangeCourseFile">
                        <label class="custom-file-label" after-label="Chọn file" for="video-file">{$original_filename}</label>
                    </div>
                </div>
            </div>
            
            
        </div>

    </div>
    
@endsection

@section('video-player-body')
<video id="video-player" src="{$src}" type="{$type}" controls></video>
<canvas id="video-canvas"></canvas>
@endsection
@php
    add_js_data('course_video_data', [
        'templates' => [
            'video_form_body' => $__env->yieldContent('video-form-template'),
            'video_player_body' => $__env->yieldContent('video-player-body'),
        ],
    ])
@endphp