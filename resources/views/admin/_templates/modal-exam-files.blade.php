
<div class="modal fade exam-create-file" id="exam-create-file" tabindex="-1" role="dialog" aria-labelledby="exam-create-file-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header custom-style bg-info">
                <h5 class="modal-title" id="exam-create-file-title">
                    <i class="fa fa-question-circle"></i>
                    File đề thi <span id="portlet-exam-name"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.exams.create-files')}}" method="POST" id="exam-create-file-forn">
                    @csrf
                    <div class="create-file-form-body">

                    </div>

        		</form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-info btn-update-question">Cập nhật</button> --}}
                <button type="button" class="btn btn-secondary btn-cancel" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>





@section('file-table')
    <div class="row">
        <div class="col-6 col-md-7">
            <h3>${name}</h3>
        </div>
        <div class="col-6 col-md-5 text-right">
            <div class="input-group">
                <div class="onput-group-prepend">
                    <div class="input-group-text">
                        Số file
                    </div>

                </div>
                <input type="number" name="quantity" class="form-control m-input" min="1" step="1" value="1">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-info">Tạo đề thi</button>
                </div>
            </div>
        </div>
    </div>

	<div class="exam-file-table mt-3 crazy-list">
		<input type="hidden" name="id" value="${id}">
        <div class="table-responsive">
            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
                <thead>
                    <tr>
                        <th class="id-col">Mã đề</th>
                        <th>Câu hỏi</th>
                        <th>Đáp án</th>
                        <th class="max-80">#</th>
                    </tr>
                </thead>
                <tbody>
                    ${filelist}
                </tbody>
                ${tableFooter}
            </table>
        </div>
	</div>
@endsection
@section('file-item')
    <tr id="${id}">
        <td class="id-col">${code}</td>
        <td>${downloadItems}</td>
        <td>${downloadAnswers}</td>
        <td class="max-80 text-right"><a href="javascript:;" class="btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air text-danger btn btn-outline-danger btn-delete-file" data-id="${id}"><i class="flaticon-delete-1"></i></a></td>
    </tr>
@endsection
@section('download-item')
<a data-toggle="m-tooltip" data-placement="left" title data-original-title="Tải về file ${type}" href="${url}" target="_blank" class="text-${btnType} btn btn-outline-${btnType} btn-sm m-btn m-btn--pill m-btn--air">
    <i class="flaticon-download"></i> ${type}
</a>
@endsection

@section('empty-list')
<tr id="${id}">
    <td colspan="4" class="text-center">${message}</td>
</tr>
@endsection

@section('tfoot')
<tfoot>
    <tr>
        <td class="id-col">#</td>
        <td colspan="2">
            <a data-toggle="m-tooltip" data-placement="left" title data-original-title="Tải về Tất cả" href="${download_url}" target="_blank" class="btn btn-info btn-sm m-btn m-btn--pill m-btn--air">
                <i class="flaticon-download"></i> Tải về tất cả
            </a>
        </td>
        <td class="max-80 text-right"><a href="javascript:;" class="btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air text-danger btn btn-outline-danger btn-delete-all" data-id="${id}"><i class="flaticon-delete-1"></i></a></td>
    </tr>
</tfoot>
@endsection
@section('progress')
<tr id="progress">
    <td colspan="4" class="text-center">
        <div class="row">
            <div class="col-6 text-left">
                File: ${count} / ${total}
            </div>
            <div class="col-6 text-right">
                ${percent}%
            </div>

        </div>
        <div class="progress m-progress--sm">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100" style="width: ${percent}%"></div>
        </div>
    </td>
</tr>

@endsection
@php

	add_js_data('exam_data', [
		'urls' => [
			'detail' => route('admin.exams.exam-detail'),
            'deleteFile' => route('admin.exams.delete-file'),
		],
		'templates' => [
			'fileTable' => $__env->yieldContent('file-table'),
            'fileItem' => $__env->yieldContent('file-item'),
            'downloadItem' => $__env->yieldContent('download-item'),
            'emptyList' => $__env->yieldContent('empty-list'),
            'tableFooter' => $__env->yieldContent('tfoot'),
            'progress' => $__env->yieldContent('progress')
		]

	])


@endphp
