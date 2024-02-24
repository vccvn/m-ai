<?php
use Gomee\Html\ColumnItem;
use Gomee\Helpers\Arr;
$list_group = isset($list_group) ? strtolower($list_group) : 'default';
extract(get_result_blade_vars($config->name, $list_group));
$btn_icon_class = 'btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air';
$btn_text_class = 'btn-sm m-btn m-btn--pill m-btn--air btn-text';
$columns = $config->get('table.columns');
$resources = new Arr($config->resources ?? ($config->assets ?? []));
// $routeParams = is_array()

if ($resources->js_data) {
    ColumnItem::show($resources, $config, [], $route_name_prefix . $config->package, $_base);

    add_js_data('list_data', ColumnItem::parseTemplateData($resources->js_data));
}
if ($resources->js && is_array($resources->js)) {
    foreach ($resources->js as $js) {
        add_js_src($js);
    }
}
if ($resources->css && is_array($resources->css)) {
    foreach ($resources->css as $css) {
        add_css_link($css);
    }
}

$mod_title = $list_group == 'trash' ? $config->titles['trash'] ?? 'Danh sach ' . $module_name . ' dã xóa' : $config->titles[$list_group] ?? ($config->titles['default'] ?? 'DAnh sach ' . $module_name);

if ($config->use_trash && $list_group != 'trash') {
    admin_action_menu([
        [
            'url' => route($route_name_prefix . $config->package . '.trash'),
            'text' => ($config->name ?? $module_name) . ' đã xóa',
            'icon' => 'fa fa-trash',
        ],
    ]);
}
if (!$config->use_trash) {
    $btn_class = 'btn-delete';
    $btn_tooltip = 'Xóa';
}
$can_edit = $config->has('can_edit') ? $config->can_edit : true;
$can_edit = $can_edit && check_current_user_permission($route_name_prefix . $config->package . '.update');

$can_delete = $config->can_delete !== false && check_current_user_permission($route_name_prefix . $config->package . '.' . ($list_group == 'trash' ? 'delete' : (!$config->use_trash ? 'delete' : 'move-to-trash')));
$can_restore = $config->use_trash && $list_group == 'trash' && check_current_user_permission($route_name_prefix . $config->package . '.restore');

$show_ext_btn = false;
$buttons = [];
$showChecklist = !($config->hide_checklist === true || $config->show_checklist === false);
if ($btns = $config->get('buttons')) {
    $show_ext_btn = true;
    // $buttons = $btns;
    foreach ($btns as $key => $button) {
        if (array_key_exists('route', $button)) {
            $rr = $button['route'];
            if ($rr && ($r = substr($rr, 0, 1) == '.' ? $route_name_prefix . $config->package . $rr : $rr)) {
                if (check_current_user_permission($r)) {
                    $buttons[] = $button;
                }
            }
        } else {
            $buttons[] = $button;
        }
    }
}
$tools = [];

if ($btns = $config->get('tools')) {
    // $show_ext_btn = true;
    // $buttons = $btns;
    foreach ($btns as $key => $button) {
        // if(array_key_exists('route', $button)){
        //     $rr = $button['route'];
        //     if($rr && ($r = substr($rr, 0, 1) == '.' ?$route_name_prefix.$config->package.$rr:$rr)){
        //         if(check_current_user_permission($r)){
        //             $tools[] = $button;
        //         }
        //     }
        // }else{
        $tools[] = $button;
        // }
    }
}

$filterForm = $config->filter['form'] ?? null;

$general_columns = $config->filter['general_columns'] ?? [];
$search_columns = array_merge($general_columns, $config->filter['search_columns'] ?? []);
$sort_columns = array_merge($general_columns, $config->filter['sort_columns'] ?? []);

if (is_array($config->data)) {
    $_d = [];
    foreach ($config->data as $_key => $_value) {
        if (substr($_key, 0, 1) == '@') {
            if (is_string($_value) && is_callable($_value)) {
                $a = call_user_func_array($_value, []);
                if (is_array($a)) {
                    $_d[substr($_key, 1)] = $a;
                }
            } elseif (is_array($_value) && array_key_exists('call', $_value) && is_callable($_value['call'])) {
                $a = call_user_func_array($_value['call'], array_key_exists('params', $_value) && is_array($_value['params']) ? $_value['params'] : (array_key_exists('args', $_value) && is_array($_value['args']) ? $_value['args'] : []));
                if (is_array($a)) {
                    $_d[substr($_key, 1)] = $a;
                }
            }
        } else {
            $_d[$_key] = $_value;
        }
    }
    $config->data = $_d;
}

if ($config->data && is_array($config->data)) {
    $config->parseData = ColumnItem::parseAttributeData($config->data);
} else {
    $config->parseData = [];
}
$request = request();
$per_page = $request->per_page ?? 10;
$page = $request->page ?? 0;
if (isset($results) && $results) {
    if (method_exists($results, 'perPage')) {
        $per_page = $results->perPage();
    }
    if (method_exists($results, 'currentPage')) {
        $page = $results->currentPage();
    }
}
if ($page < 1) {
    $page = 1;
}
if ($per_page < 1) {
    $per_page = 10;
}
$itemStart = ($page - 1) * $per_page;

$filterConfig = $resources = new Arr($config->filter);

$routeParams = isset($module_route_params) ? $module_route_params : [];

$currentUrl = url()->full();

?>

@extends($_layout . 'main')

{{-- khai báo title --}}
@section('title', $title = $mod_title)

{{-- tên modul xuất hiện trong sub header --}}
@section('module.name', get_web_data('module_name') ?? ($config->name ?? $module_name))



@section('content')

    @if ($filterConfig->type == 'panel' && !$filterConfig->disabled)
        <div class="row">
            <div class="col-md-4 col-xl-3">
                @if ($filterConfig->include)
                    @include($_base . $filterConfig->include)
                @else
                @endif
            </div>

            <div class="col-md-8 col-xl-9">

    @endif
    <div class="m-portlet" id="list-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ isset($list_title) && $list_title ? $list_title : $title }}
                    </h3>
                </div>
            </div>
            @if ($list_group != 'trash' && $config->can_create !== false)
                <div class="m-portlet__head-tools">
                    <ul class="m-portlet__nav">
                        <li class="m-portlet__nav-item">
                            <a href="{{ route($route_name_prefix . $config->package . '.create', $routeParams) }}" data-toggle="m-tooltip" data-placement="left" title data-original-title="Thêm {{ $config->name ?? $module_name }}" class="ml-3 btn btn-outline-primary btn-add-item m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill m-btn--air"><i class="fa fa-plus"></i></a>
                        </li>
                    </ul>
                </div>
            @endif

        </div>

        <div class="m-portlet__body">
            @if ($filterConfig->type != 'panel' && !$filterConfig->disabled)
                <div class="m-section filter-section">
                    <div class="m-section__sub">
                        @include($_template . 'list-filter' . ($filterForm ? '-' . $filterForm : ''), [
                            'sortable' => $sort_columns,
                            'searchable' => $search_columns,
                        ])
                    </div>
                </div>
            @endif

            @if (isset($results) && count($results))
                <!--begin::Section-->
                <div class="m-section">
                    <div class="m-section__content crazy-list {{ str_slug($config->package, '-') }}">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped {{ $config->get('table.class') }}" data-order-start="{{ $itemStart }}">
                                <thead>
                                    <tr>
                                        @if ($showChecklist)
                                            <th class="check-col">
                                                <label class="m-checkbox m-checkbox--solid m-checkbox--success">
                                                    <input type="checkbox" class="crazy-check-all">
                                                    <span></span>
                                                </label>
                                            </th>
                                        @endif
                                        @if ($columns)
                                            @foreach ($columns as $column)
                                                @php
                                                    $sortKey = $column['sortKey'] ?? ($column['sort_key'] ?? ($column['sortkey'] ?? ''));
                                                @endphp
                                                <th class="{{ $column['header_class'] ?? ($column['class'] ?? '') }}">

                                                    @if ($sortKey)
                                                        <a
                                                            href="{{ url_merge(
                                                                $currentUrl,
                                                                [
                                                                    'orderby' => $sortKey,
                                                                    'sorttype' => $sortKey == $request->orderby ? ($request->sorttype != 'ASC' ? 'ASC' : 'DESC') : 'ASC',
                                                                ],
                                                                null,
                                                                'page',
                                                            ) }}">
                                                            {{ $column['title'] ?? 'Column' }}

                                                            @if ($sortKey == $request->orderby)
                                                                <i class="fa fa-sort-{{ $request->sorttype == 'DESC' ? 'down' : 'up' }} ml-2"></i>
                                                            @else
                                                                {{-- <i class="fa fa-sort ml-2"></i> --}}
                                                            @endif
                                                        </a>
                                                    @else
                                                        {{ $column['title'] ?? 'Column' }}
                                                    @endif
                                                </th>
                                            @endforeach

                                        @endif
                                        @if (!$show_ext_btn && !($can_edit !== false) && !($can_delete !== false))
                                        @else
                                            <th class="min-100 actions">Thao tác</th>
                                        @endif
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach ($results as $item)
                                        @php
                                            $index = $itemStart + $loop->index;
                                        @endphp
                                        <tr id="crazy-item-{{ $item->id ?? $item->id }}" data-name="{{ $item->name ?? $item->title }}">
                                            @if ($showChecklist)
                                                <td class="check-col">
                                                    <label class="m-checkbox m-checkbox--solid m-checkbox--success">
                                                        <input type="checkbox" name="ids[]" value="{{ $item->id ?? $item->id }}" data-id="{{ $item->id ?? $item->id }}" data-id="{{ $item->id }}" class="crazy-check-item">
                                                        <span></span>
                                                    </label>
                                                </td>
                                            @endif
                                            @if ($columns)
                                                @foreach ($columns as $column)
                                                    {!! ColumnItem::show($item, $config, $column, $route_name_prefix . $config->package, $_base, 'td', $index) !!}
                                                @endforeach
                                            @endif
                                            @if (!$show_ext_btn && !($can_edit !== false) && !($can_delete !== false))
                                            @else
                                                <td class="min-120 actions">
                                                    @if ($buttons && $show_ext_btn)
                                                        @foreach ($buttons as $key => $button)
                                                            @php
                                                                $btnCfg = new Arr(ColumnItem::parseTemplateData($button));
                                                                $show = true;
                                                                if ($btnCfg->show === false) {
                                                                    $show = false;
                                                                } elseif (is_array($btnCfg->show)) {
                                                                    foreach ($btnCfg->show as $showConditionKey => $showConditionvalue) {
                                                                        $operator = '=';
                                                                        $a = preg_match_all('/\s*(<=|>=|<|>|!=|!|==|=)\s*$/', $key, $match);
                                                                        if ($a) {
                                                                            $operator = $match[1][0];
                                                                            $showConditionKey = trim(str_replace($operator, '', $showConditionKey));
                                                                        }

                                                                        if (!smart_compare($item->{$showConditionKey}, $showConditionvalue, $operator)) {
                                                                            $show = false;
                                                                        }
                                                                    }
                                                                }
                                                                $text = $btnCfg->text;
                                                                if ($btnCfg->textByProp && is_array($btnCfg->textByProp)) {
                                                                    $prop = $btnCfg->get('textByProp.prop');
                                                                    if ($t = $btnCfg->get('textByProp.texts.' . $item->{$prop})) {
                                                                        $text = $t;
                                                                    }
                                                                }
                                                                $type = $btnCfg->type;
                                                                if ($btnCfg->typeByProp && is_array($btnCfg->typeByProp)) {
                                                                    $prop = $btnCfg->get('typeByProp.prop');
                                                                    if ($t = $btnCfg->get('typeByProp.types.' . $item->{$prop})) {
                                                                        $type = $t;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if ($show)
                                                                <a href="{{ $btnCfg->route ? route(substr($btnCfg->route, 0, 1) == '.' ? $route_name_prefix . $config->package . $btnCfg->route : $btnCfg->route, $btnCfg->params ?? []) : 'javascript:void(0);' }}" data-original-title="{{ $btnCfg->title }}" data-id="{{ $item->id ?? $item->id }}" data-toggle="m-tooltip" data-placement="left"
                                                                    @foreach ($btnCfg->all() as $btnKey => $btnVal)
                                                                        @if (!in_array($btnKey, ['class', 'className', 'href', 'route', 'text', 'title', 'icon', 'type', 'show', 'params', 'data-id']))
                                                                            {{ $btnKey }}="{{ $btnVal }}"{{ ' ' }}
                                                                        @endif @endforeach
                                                                    class="{{ $btnCfg->class }} {{ $btnCfg->className }} btn btn-{{ $type }} {{ $btnCfg->text ? $btn_text_class : $btn_icon_class }}">
                                                                    @if ($btnCfg->icon)
                                                                        <i class="{{ $btnCfg->icon }} mr-1"></i>
                                                                    @endif {{ $text }}
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    @if ($can_edit !== false)
                                                        <a href="{{ route($route_name_prefix . $config->package . '.update', array_merge([$item->getKeyName() => $item->{$item->getKeyName()}], $routeParams)) }}" data-id="{{ $item->id ?? $item->id }}" data-original-title="Sửa" data-toggle="m-tooltip" data-placement="left" title class="text-accent btn-edit-item btn btn-outline-accent {{ $btn_icon_class }}">
                                                            <i class="flaticon-edit-1"></i>
                                                        </a>
                                                    @endif
                                                    @if ($list_group == 'trash' && $can_restore)
                                                        <a href="javascript:void(0);" data-id="{{ $item->id }}" data-toggle="m-tooltip" data-placement="left" data-original-title="Khôi phục" class="btn-restore text-info btn btn-outline-info {{ $btn_icon_class }}">
                                                            <i class="fa fa-undo"></i>
                                                        </a>
                                                    @endif
                                                    @if ($can_delete !== false)
                                                        <a href="javascript:void(0);" data-id="{{ $item->id ?? $item->id }}" data-toggle="m-tooltip" data-placement="left" data-original-title="{{ $btn_tooltip }}" class="{{ $btn_class }} text-danger btn btn-outline-danger {{ $btn_icon_class }}">
                                                            <i class="flaticon-delete-1"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6"></div>
                        <div class="col-6 text-right">Hiển thị từ {{ $results->firstItem() }} đến {{ $results->lastItem() }} trên {{ $results->total() }} kết quả</div>
                    </div>
                </div>
                {{-- nút phân trang --}}
                <div class="list-toolbar">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4">
                            @if ($showChecklist)
                                <a href="javascript:void(0);" data-toggle="m-tooltip" data-placement="top" data-original-title="Chọn tất cả" class="crazy-btn-check-all text-success btn btn-outline-success {{ $btn_icon_class }}">
                                    <i class="fa fa-check"></i>
                                </a>
                            @endif
                            @if ($config->use_trash)
                                @if ($list_group == 'trash')
                                    @if ($can_restore)
                                        <a href="javascript:void(0);" data-toggle="m-tooltip" data-placement="top" data-original-title="Khôi phục tất cả" class="crazy-btn-restore-all text-info btn btn-outline-info {{ $btn_icon_class }}">
                                            <i class="fa fa-undo"></i>
                                        </a>
                                    @endif
                                    @if ($can_delete)
                                        <a href="javascript:void(0);" data-toggle="m-tooltip" data-placement="top" data-original-title="Xóa tất cả" class="crazy-btn-delete-all text-danger btn btn-outline-danger {{ $btn_icon_class }}">
                                            <i class="flaticon-delete-1"></i>
                                        </a>
                                    @endif
                                @else
                                    @if ($can_delete)
                                        <a href="javascript:void(0);" data-toggle="m-tooltip" data-placement="top" data-original-title="Chuyển tất cả vào thùng rác" class="crazy-btn-move-to-trash-all text-danger btn btn-outline-danger {{ $btn_icon_class }}">
                                            <i class="flaticon-delete-1"></i>
                                        </a>
                                    @endif
                                @endif
                            @else
                                @if ($can_delete)
                                    <a href="javascript:void(0);" data-toggle="m-tooltip" data-placement="top" data-original-title="Xóa tất cả" class="crazy-btn-delete-all text-danger btn btn-outline-danger {{ $btn_icon_class }}">
                                        <i class="flaticon-delete-1"></i>
                                    </a>
                                @endif
                            @endif

                            @if ($tools)
                                @foreach ($tools as $key => $button)
                                    @php
                                        $btnCfg = new Arr($button);
                                        $show = true;
                                        if ($btnCfg->show === false) {
                                            $show = false;
                                        }
                                        $text = $btnCfg->text;
                                        $type = $btnCfg->type;
                                        $href = $btnCfg->route ? route(substr($btnCfg->route, 0, 1) == '.' ? $route_name_prefix . $config->package . $btnCfg->route : $btnCfg->route, $btnCfg->params ?? []) : 'javascript:void(0);';
                                    @endphp
                                    @if ($show)
                                        <a href="{{ $href }}" data-toggle="m-tooltip" data-placement="top" data-original-title="{{ $btnCfg->title }}" class="btn btn-outline-{{ $type }} text-{{ $type }} {{ $btnCfg->className }}  {{ $btn_icon_class }}">
                                            @if ($btnCfg->icon)
                                                <i class="{{ $btnCfg->icon }}"></i>
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <div class="col-12 col-md-6 col-lg-8">
                            {{ $results->links($_pagination . 'default') }}
                        </div>
                    </div>
                </div>
                <!--end::Section-->
            @else
                <div class="alert alert-default text-center">Danh sách trống</div>
            @endif

        </div>

        <!--end::Form-->
    </div>
    @if ($filterConfig->type == 'panel')
        </div>
        </div>
    @endif

    <?php
    $extra = [
        'components' => $_component,
        'templates' => $_template,
        'modals' => $_base . '_modals.',
        'views' => $_base,
    ];
    ?>
    @foreach ($extra as $item => $path)
        @if ($tpl = $config->get('includes.' . $item))
            @if (!is_array($tpl))
                @include($path . $tpl)
            @else
                @foreach ($tpl as $blade)
                    @include($path . $blade)
                @endforeach
            @endif
        @endif
    @endforeach



@endsection

{{-- thiết lập biến cho js để sử dụng --}}
@section('jsinit')
    @if ($list_group != 'trash')
        <script>
            window.crazyItemsInit = function() {
                App.items.init({
                    module: "{{ $config->package }}",
                    title: "{{ $config->name }}",
                    urls: {
                        {{ $config->use_trash ? 'move_to_trash' : 'delete' }}_url: @json(route($route_name_prefix . $config->package . '.' . ($config->use_trash ? 'move-to-trash' : 'delete')))
                    }
                })
            };
            // khai báo ở dây
        </script>
    @else
        <script>
            window.crazyItemsInit = function() {
                App.items.init({
                    module: "{{ $config->package }}",
                    title: "{{ $config->name }}",

                    urls: {
                        delete_url: @json(route($route_name_prefix . $config->package . '.delete')),
                        restore_url: @json(route($route_name_prefix . $config->package . '.restore'))
                    }
                })
            };
            // khai báo ở dây
        </script>
    @endif

@endsection

{{-- Nhúng js --}}

@section('js')
    <script src="{{ asset('static/crazy/js/items.js') }}"></script>
@endsection
