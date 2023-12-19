@php
use \App\Models\PermissionModule;
$groupNames = PermissionModule::GROUP_NAMES;
$scopes = $input->hiddenData('modules');
$input->order = 1;
$actionCount = count($groupNames);
$columnCount = 4 + $actionCount;
$__id__ = $input->hiddenData('__id__');
$render = function ($render, $modules, $prefixName = '') use($input, $actionCount, $columnCount, $groupNames, $__id__){
    $html = '';
    foreach($modules as $module){
        $html .= '
            <tr id="crazy-item-'.$module->id.'" data-name="'.htmlentities($module->name).'">
                <td class="check-col text-center">
                    <input type="hidden" name="'.$input->name.'['.$module->id.'][change]" class="check-change" value="" />
                    <span>'.$input->order.'</span>
                </td>
                <td class="module-name max-150 text-center">
                    <span>'.htmlentities($prefixName . $module->name).'</span>
                </td>
                <td class="module-description max-150 text-center">
                    <span>'.htmlentities($module->description).'</span>
                </td>
                <td class="max-150">
                    <label class="m-checkbox m-checkbox--solid m-checkbox--success">
                        <input type="checkbox" name="'.$input->name.'['.$module->id.'][all]" class="module-check-item check-all"> 
                        <span></span> <i class="d-inline-block ml-2"> Tất cả </i>
                    </label>
                </td>
        ';

        if($module->module_action_groups){
            foreach ($module->module_action_groups as $slug => $group) {
                $old = old($group?$input->name . '.' . $module->id . '.groups': "-------no--------" );
                $checked = $old && is_array($old && in_array($group->id, $old))?true:(
                    $group && $group->role_id_list && in_array($__id__, $group->role_id_list)?true:false
                );
            
                $html .= '<td class="max-150">
                            '.($group?'<label class="m-checkbox m-checkbox--solid m-checkbox--success">
                                <input type="checkbox" name="'.$input->name.'['.$module->id.'][groups][]" value="'.$group->id.'" data-id="'.$group->id.'" data-cross-group="'.$slug.'" data-name="'.htmlentities($group->name).'" '.($checked?'checked':'').' class="module-check-item check-one"> 
                                <span></span>  <i class="d-inline-block ml-2"> '.$groupNames[$slug].' </i>
                            </label>':'').'
                        </td>';
            }
        }else{
            $html .= '<td colspan="'.$acctionCount.'" class="text-center"></td>';
        }

        $html.='
                </tr>';
        $input->order++;
        if($module->modules && count($module->modules)){
            // $html .= '<tr><td colspan="'.$columnCount.'" class="text-center">'.htmlentities($prefixName . $module->name . ' > Sub-Modules').'</td></tr>';
            $html .= $render($render, $module->modules, $prefixName . $module->name . ' > ');
            // $html .= '<tr><td colspan="'.$columnCount.'" class="text-center">'.htmlentities('--- End ---').'</td></tr>';
        }
    }
    


    return $html;

};
$input->addClass('module-table');
$wrapper = $input->copy();
$wrapper->tagName = 'div';
$wrapper->type='wrapper';
$wrapper->name='wrapper';
$wrapper->id.='-wrapper';
$wrapper->removeClass('form-control', 'm-input');
add_css_link('static/features/permissions/style.css');
add_js_src('static/features/permissions/script.js');


@endphp

<div {!! $wrapper->attrsToStr() !!} >
@if ($scopes && count($scopes))
    @foreach ($scopes as $scope)
        <div class="scope">
            <h4 class="scope-title">{{$scope->name}}</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="check-col text-center">
                                <span>STT</span>
                            </th>
                            <th class="module-name max-150 text-center">
                                <span>Tên Module</span>
                            </th>
            
                            <th class="module-description max-200 text-center">
                                <span>Mô tả</span>
                            </th>
                            <th class="max-150">
                                <label class="m-checkbox m-checkbox--solid m-checkbox--success">
                                    <input type="checkbox" name="checkall[{{$scope->id}}][all]" class="check-cross-all"> 
                                    <span></span>  <i class="d-inline-block ml-2"> Tất cả </i>
                                </label>
                            </th>
                            @foreach ($groupNames as $slug => $name)
                            <th class="max-150">
                                <label class="m-checkbox m-checkbox--solid m-checkbox--success">
                                    <input type="checkbox" name="checkall[{{$scope->id}}][{{$slug}}]" data-group="{{$slug}}" class="check-cross-group"> 
                                    <span></span>  <i class="d-inline-block ml-2"> {{$name}} </i>
                                </label>
                            </th>
                            @endforeach
            
                        </tr>
                    </thead>
                    <tbody>
                        {!! $render($render, $scope->modules??[]) !!}
            
                    </tbody>
                </table>
            </div>
            
        </div>
    @endforeach
@endif

</div>