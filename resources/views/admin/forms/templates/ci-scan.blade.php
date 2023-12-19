<div class="custom-file preview">
    <?php $input->addClass('custom-file-input'); $input->type='file'; ?>
    <img src="{{$input->value?route('admin.users.ci.image', ['user_id' => $input->parent?$input->parent->hidden_id->val():0, 'face' => $input->hidden('face')]): asset('static/images/default.png')}}" alt="" class="img-preview">
    {!! $input !!}
    <label class="custom-file-label" for="{{$input->id }}" data-label="Chọn file">{{$input->val()?$input->val():($input->choose_label??'Chưa có file nào được chọn')}}</label>
</div>

