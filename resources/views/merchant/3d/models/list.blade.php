@php
	add_css_link('static/manager/css/model-form.min.css');
@endphp
@extends($_layout.'main')

{{-- khai báo title --}}
@section('title', 'Danh sách model')

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', $title = 'Model')

@section('content')
	<?php
	merchant_action_menu([
		[
			'url' => route($route_name_prefix.'3d.models.trash'),
			'text' =>  'Model đã xoa',
			'icon' => 'fa fa-trash'
		]
	]);
	?>
    @include($_current.'results', ['type' => 'default'])

<div class="m-portlet m-portlet--skin-dark m-portlet--bordered-semi preview d-none" id="editor-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon">
                    <i class="flaticon-statistics"></i>
                </span>
                <h3 class="m-portlet__head-text">
                    Dark Skin
                </h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item">
                    <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon btn-close-preview">
                        <i class="la la-close"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="m-portlet__body" id="editor-portlet-body">
        <div class="btns-block">
            <button type="button" class="btn btn-sm btn-danger btn-close-editor"><i class="fa  fa-check"></i> Xong</button>
        </div>
        <div class="frame">

        </div>
    </div>
</div>

<div style="position: relative; overflow: hidden; width: 10px; height: 10px; opacity: 0;">
<textarea name="cfc" id="copyable-content" cols="30" rows="10"></textarea>
</div>


<div class="qr-wrapper">
    <div class="img-frame">
        <div class="img">
            <img src="" alt="" id="qr-image">
        </div>
        <div class="label mt-3">
            Quét mã QR để trải nghiệm AR

        </div>

        <div class="buttons mt-3 text-center">

            <button type="button" class="btn btn-sm btn-success" id="btn-save-qr">Tải về</button>
            <button type="button" class="btn btn-sm btn-danger" id="btn-close-qr">Đóng</button>

        </div>
    </div>
</div>


<div class="tracking-wrapper">
    <div class="img-frame">
        <div class="img">
            <img src="" alt="" id="tracking-image">
        </div>
        <div class="label mt-3">
            Quét ảnh để trải nghiệm

        </div>

        <div class="buttons mt-3 text-center">

            <a class="btn btn-sm btn-success" id="btn-save-tracking" href="">Tải về</a>
            <button type="button" class="btn btn-sm btn-danger" id="btn-close-tracking">Đóng</button>

        </div>
    </div>
</div>
@endsection

{{-- thiết lập biến cho js để sử dụng --}}
@section('jsinit')
	<script>
		window.crazyItemsInit = function () {
			App.items.init({
				module:"models",
				title:"{{$title}}",
				urls:{
					move_to_trash_url: @json(route($route_name_prefix.'3d.models.move-to-trash'))
				}
			})
		};
		// khai báo ở dây
	</script>
@endsection

{{-- Nhúng js --}}

@section('js')
	<script src="{{asset('static/crazy/js/items.js')}}"></script>
	<script>

$(function () {
    const qrWrapper = document.querySelector('.qr-wrapper');
    var editorportlet = new mPortlet("editor-portlet");
	var url = "{{route('admin.3d.items.edit',['secret_id' => 'DoanHaha'])}}";
    $(document).on("click", ".btn-edit-3d", function (e) {

        e.preventDefault();
        var id = $(this).data('id');
		var u = App.str.replace(url, 'DoanHaha', id);
		// $('#preview-portlet').removeClass('d-none');
        // previewportlet.fullscreen();
        $('#editor-portlet-body .frame').html(
            '<iframe width="560" height="315" src="'+u+'" title="Crazy 3D" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        );
        $('#editor-portlet').removeClass('d-none');
        editorportlet.fullscreen();
    })
    $(document).on("click", ".btn-close-editor", function (e) {
        e.preventDefault();
        $('#editor-portlet-body .frame').html(
            ''
        );
        editorportlet.unFullscreen();
        $('#editor-portlet').addClass('d-none');

    })

    const ipnElement = document.querySelector('#copyable-content');
    $(document).on("click", ".btn-copy-this-content", function(e){
        let $this = $(this);
        e.preventDefault();
        // step 1

        let content = $(this).data('copy-content');

        ipnElement.innerText = content;

        ipnElement.select()              // step 4

        document.execCommand('copy')     // step 5

        let btnClass = 'm-btn--icon m-btn--icon-only btn-outline-warning';
        $this.removeClass(btnClass);
        $this.html('Copied');
        $this.addClass('btn-success');

        setTimeout(() => {
            $this.removeClass('btn-success');
            $this.html('<i class="fa fa-copy"></i>');
            $this.addClass(btnClass);
        }, 3000);


    });

    $(document).on("click", '.btn-show-qr-code', function(e){
        let $this = $(this);
        e.preventDefault();
        $('#qr-image').attr('src', $this.data('qr-image'));
        qrWrapper.style.display = 'flex';
    })


    $(document).on("click", '#btn-close-qr', function(e){
        let $this = $(this);
        e.preventDefault();
        qrWrapper.style.display = 'none';

        $('#qr-image').attr('src', null);
    })

    const trackingWrapper = document.querySelector('.tracking-wrapper');

    var secret_id = '';
    var download_url = "{{route($route_name_prefix.'3d.models.download-tracking-image')}}";

    $(document).on('click', '.btn-show-tracking-image', function(e){
        e.preventDefault();
        let $this = $(this);
        secret_id = $this.data('secret');
        $('#tracking-image').attr('src', $this.data('tracking-image'));
        trackingWrapper.style.display = 'flex';
        $('#btn-save-tracking').attr('href', download_url + '/' + secret_id);
    });
    $(document).on("click", "#btn-close-tracking", function(e){
        //
        $('#tracking-image').attr('src', null);
        trackingWrapper.style.display = 'none';
    });


});
	</script>
@endsection
