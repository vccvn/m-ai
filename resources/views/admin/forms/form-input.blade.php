
{{-- nếu có tempplate và (kieu input phai bang hoac nam trong danh sach cua template do) --}}
@if($is_template)
    @include($_base.'forms.templates.'.$input->template)
@elseif ($input->type == 'file')
    <div class="custom-file">
        <?php $input->addClass('custom-file-input'); ?>
        {!! $input !!}
        <label class="custom-file-label" for="{{$input->id }}"  data-label="Chọn file">{{$input->val()?$input->val():($input->choose_label??'Chưa có file nào được chọn')}}</label>
    </div>

@elseif(in_array($type, ['checkbox', 'radio', 'checklist']))

    <div class="checkbox-radio {{$input->data('display') == 'list'?'display-list':"display-inline"}}">
        {!! $input !!}
    </div>
@else
    {!! $input !!}
@endif
