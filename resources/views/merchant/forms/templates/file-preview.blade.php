<div class="custom-file preview">
    <?php $input->addClass('custom-file-input'); ?>
    <img src="{{$input->value?route('')}}" alt="">
    {!! $input !!}
    <label class="custom-file-label" for="{{$input->id }}">{{$input->val()?$input->val():($input->choose_label??'Chưa có file nào được chọn')}}</label>
</div>
