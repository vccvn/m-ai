<?php
$id = $form->hidden_id->value;
?>
<div class="m-portlet mt-4">
<form method="GET" action="{{route('admin.exams.export-exams')}}" class="smart-form auto-validation" novalidate>
    @csrf
    <div class="m-portlet__body">
        <div class="form-inputs">
            <div class="row form-group m-form__group">
                <div class="col-md-5">
                    <p></p>
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="exams_export" value="" placeholder="Số mã đề cần tạo">
                    <input class="form-control" type="hidden" name="id" value="{{$id}}" placeholder="Số mã đề cần tạo">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary">Tạo đề thi</button>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success">Tải xuống</button>
                </div>
            </div>


        </div>
    </div>


</form>
</div>