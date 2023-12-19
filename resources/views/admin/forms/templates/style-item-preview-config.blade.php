@php
    $pc = new \Gomee\Helpers\Arr($input->defVal());
    if(!$pc->width || $pc->width <= 0) $pc->width = null;
    if(!$pc->height || $pc->height <= 0) $pc->height = null;
    
@endphp

<div class="style-item-preview-group" data-config='@json($pc->all())'>
    <div class="row mb-3">
        <div class="col-6">
            <label for="{{$input->name.'-width'}}" class="mb-1">Chiều rộng</label>
            <input type="number" name="{{$input->name.'[width]'}}" id="{{$input->name.'-width'}}" data-name="width" value="{{$pc->width}}" placeholder="auto" class="form-control width">
        </div>
        <div class="col-6">
            <label for="{{$input->name.'-height'}}" class="mb-1">Chiều cao</label>
            <input type="number" name="{{$input->name.'[height]'}}" id="{{$input->name.'-height'}}" data-name="height" value="{{$pc->height}}" placeholder="auto" class="form-control height">
        </div>
        
    </div>
    <div class="row">
        <div class="col-6">
            <label for="{{$input->name.'-top'}}" class="mb-1">Lề trên</label>
            <input type="number" name="{{$input->name.'[top]'}}" id="{{$input->name.'-top'}}" data-name="top" value="{{$pc->top}}" placeholder="0px" class="form-control top">
        </div>
        <div class="col-6">
            <label for="{{$input->name.'-left'}}" class="mb-1">Lề trái</label>
            <input type="number" name="{{$input->name.'[left]'}}" id="{{$input->name.'-left'}}" data-name="left" value="{{$pc->left}}" placeholder="0px" class="form-control left">
        </div>
    </div>
</div>