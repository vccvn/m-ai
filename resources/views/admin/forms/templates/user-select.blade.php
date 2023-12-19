<?php
use App\Base\Constants\ParticipantConstant;
$status_list = ParticipantConstant::ALL_STATUS;
$status_labels = ParticipantConstant::STATUS_LABELS;
add_js_src('static/features/user-select/user-select.js');
add_css_link('static/features/user-select/user-select.css');
// $input->type = "text";
$modal = $input->modal?$input->modal:"user-select-modal";
set_admin_template_data('modals', $modal);
$baseName = $input->name;



$wrapper = $input->copy();
$wrapper->type = "user-select";
$wrapper->prepareCrazyInput();


$old = old($baseName);
$items = $old??$input->value;
$maxIndex = (is_array($items) && $items)?count($items):0;
$wrapper->data('max-index', $maxIndex);



$wrapper->removeClass();
$wrapper->addClass("user-select");
$wrapper->id.='-wrapper';
$wrapper->name.='-wrapper';
$data = $input->defval();
if(!is_array($data)) $data = [];
$wrapper->data('user-detail-url', route($route_name_prefix.'users.detail-data'));
$index = 0;
?>

<div {!! $wrapper->attrsToStr() !!}>
    <div class="user-select-list">
        @if ($data)
            @foreach ($data as $i => $participant)
                @if ($user = get_user($participant['user_id']))
                    
                <div class="user-select-item" id="user-select-item-{{$index}}" data-index="{{$index}}">
                    <input type="hidden" name="{{$baseName}}[{{$index}}][user_id]" value="{{$participant['user_id']}}" class="inp-user-id">
                    <input type="hidden" name="{{$baseName}}[{{$index}}][status]" value="{{$participant['status']}}" class="inp-status">
                    
                    <div class="info" class="inp-user-id">
                        <div class="avatar-wrapper">
                            <div class="avatar"><img src="{{$user->getAvatar()}}" alt="{{$user->name}}"></div>
                        </div>
                        <div class="name-wrapper">
                            <h5>{{$user->last_name . ' ' . $user->first_name}}</h5>
                            <p>
                                Quyền: <a href="javascript:void(0);" class="btn-quick-change-status" data-status="{{$participant['status']}}">{{$status_labels[$participant['status']]??''}}</a>
                            </p>
                        </div>
                        <div class="user-actions mb-xs-4">
                            <a href="javascript:void(0);" data-index="{{$index}}" data-toggle="m-tooltip" data-placement="top" data-original-title="Xóa user này" class="crazy-btn-delete-user text-danger btn btn-outline-danger btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                                <i class="flaticon-delete-1"></i>
                            </a>    
                        </div>
                        
                    </div>
                    @if ($errors->has($input->name.'.'.$i))
                        <div class="mt-3">
                            <span class="text-danger">{{$errors->first($input->name.'.'.$i)}}</span>
                        </div>
                    @endif    
                </div>
                @endif
                @php
                    $index++;
                @endphp
            @endforeach
        @endif
    </div>

    <div class="user-select-buttons mt-3">
        <div class="crazy-btn-add-user btn btn btn-sm btn-brand m-btn m-btn--icon m-btn--pill m-btn--wide" data-modal="{{$modal}}">
            <span>
                <i class="la la-plus"></i>
                <span>Thêm user</span>
            </span>
        </div>
    </div>
    <?php
        $index = '{$index}';
        $name = $input->name."[$index]";
    ?>
        
        <script type="text/html" id="user-select-template" data-max-index="{{$maxIndex}}">
    
            <div class="user-select-item" id="user-select-item-{{$index}}" data-index="{{$index}}">
                <input type="hidden" name="{{$baseName}}[{{$index}}][user_id]" value="{$id}" class="inp-user-id">
                <input type="hidden" name="{{$baseName}}[{{$index}}][status]" value="{$status}" class="inp-status">
                <div class="info">
                    <div class="avatar-wrapper">
                        <div class="avatar"><img src="{$avatar_url}" alt="{$first_name}"></div>
                    </div>
                    <div class="name-wrapper">
                        <h5>{$last_name} {$first_name}</h5>
                        <p>
                            Quyền: <a href="javascript:void(0);" class="btn-quick-change-status" data-status="{$status}">{$status_label}</a>
                        </p>
                    </div>
                    <div class="user-actions mb-xs-4">
                        <a href="javascript:void(0);" data-index="{{$index}}" data-toggle="m-tooltip" data-placement="top" data-original-title="Xóa user này" class="crazy-btn-delete-user text-danger btn btn-outline-danger btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">
                            <i class="flaticon-delete-1"></i>
                        </a>    
                    </div>
                </div>
            </div>
        </script>
        <script id="user-select-status-label">
            var participant_status_labels = @json($status_labels);
        </script>
    

    
</div>