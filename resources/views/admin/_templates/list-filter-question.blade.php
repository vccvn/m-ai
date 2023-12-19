<?php
$request = request();
$keyword = $request->search;
$per_page = $request->per_page;

$searchby = $request->searchby;
$orderby = $request->orderby;
$sorttype = $request->sorttype;
$sort_list = ['' => 'Kiểu sắp xếp', 'ASC' => 'Tăng dần', 'DESC' => 'Giảm dần'];

if ($sorttype && strtolower($sorttype) != 'desc') {
    $sorttype = 'ASC';
}
$per_pages = ['' => 'KQ / trang', 10 => 10, 25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000];

// tim kiem
if (!isset($searchable)) {
    $searchable = [];
} elseif (!is_array($searchable)) {
    $searchable = explode(',', str_replace(' ', '', $searchable));
}

$searchable = array_merge(['' => 'Tìm theo...'], $searchable);

// sap xep
if (!isset($sortable)) {
    $sortable = [];
} elseif (!is_array($sortable)) {
    $sortable = explode(',', str_replace(' ', '', $sortable));
}

$sortable = array_merge(['' => 'Sắp xếp theo...'], $sortable);
$subject_id = $request->subject_id;
$topic_id = $request->topic_id;
$difficult_level = $request->difficult_level;
$sort_list = ['' => 'Kiểu sắp xếp', 'ASC' => 'Tăng dần', 'DESC' => 'Giảm dần'];
$subject_options = get_subject_options();
$topic_map = get_subject_topic_map();
// them css
add_custom_css('.filter-block .filter-form div[class*="col-"]', [
    'margin-bottom' => '15px',
]);

?>

<div class="filter-block align-middle d-none d-sm-block">
    <form action="" method="get" class="filter-form question-filter">
        <div class="form-group row mb-0">
            <div class="col-sm-6 col-md-2 col-xl-2">
                <label for="filter-subject-id">Môn thi</label>
                <div class="input-group">
                    <select name="subject_id" id="filter-subject-id" class="form-control filter-select-subject">
                        <option value="">Tất cả</option>
                        @foreach ($subject_options as $key => $val)
                            <option value="{{ $key }}" {{ $subject_id == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-2 col-xl-3">
                <label for="filter-topic-id">Chuyên đề</label>
                <div class="input-group">
                    <select name="topic_id" id="filter-topic-id" class="form-control">
                        <option value="">Tất cả</option>
                        @if ($subject_id && array_key_exists($subject_id, $topic_map) && ($topic_options = $topic_map[$subject_id]))
                            @foreach ($topic_options as $tid => $tname)
                                <option value="{{ $tid }}" {{ $tid == $topic_id ? 'selected' : '' }}>{{ $tname }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

            </div>
            <div class="col-sm-3 col-md-2">
                <label for="filter-difficult-level">Độ khó</label>
                <div class="input-group">
                    <select name="difficult_level" id="filter-difficult-level" class="form-control">
                        <option value="">Tất cả</option>
                        @php
                            $d = [1, 2, 3, 4, 5, 6, 7,8,9, 10];
                        @endphp
                        @foreach ($d as $k)
                            <option value="{{ $k }}" {{ $difficult_level == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="col-sm-4 col-md-3">
                <label for="search-keyword">Từ khóa</label>
                <div class="input-group">
                    <input type="text" id="search-keyword" class="form-control " name="search" value="{{ $keyword }}" placeholder="Nhập từ khóa">

                </div>
            </div>
            <div class="col-sm-2 col-md-1">

                <label for="" class="text-white d-none d-sm-block" style="opacity: 0">s</label>
                <div>
                    <div class="m-dropdown m-dropdown--inline m-dropdown--arrow filter-menu-dropdown m-dropdown--align-right m-dropdown--align-push ml-auto" m-dropdown-toggle="hover" aria-expanded="true">
                        <a href="#" class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
                            <i class="la la-plus m--hide"></i>
                            <i class="la la-ellipsis-h"></i>
                        </a>
                        <div class="m-dropdown__wrapper">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <div class="form-group">
                                            <div>
                                                <label for="orderby-2">Sắp xếp theo</label>

                                            </div>
                                            <div>
                                                <select name="orderby" id="orderby-2" class="form-control">
                                                    @foreach ($sortable as $key => $val)
                                                        <?php
                                                        if (is_numeric($key)) {
                                                            $v = $val;
                                                        } else {
                                                            $v = $key;
                                                        }
                                                        ?>
                                                        <option value="{{ $v }}" {{ strtolower($orderby) == strtolower($v) ? 'selected' : '' }}>{{ $val }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div>
                                                <label for="orderby-2">Kiểu sắp xếp </label>

                                            </div>
                                            <div>
                                                <select name="sorttype" id="sortype-" class="form-control">
                                                    @foreach ($sort_list as $k => $vl)
                                                        <option value="{{ $k }}" {{ strtoupper($sorttype) == $k ? 'selected' : '' }}>{{ $vl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div>
                                                <label for="orderby-2">Kết quả trên trang</label>

                                            </div>
                                            <div>
                                                <select name="per_page" id="per_page" class="form-control">
                                                    @foreach ($per_pages as $val => $text)
                                                        <option value="{{ $val }}" {{ $val == $per_page ? 'selected' : '' }}>{{ $text }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-12 col-sm-3 col-md-2 col-xl-1">
                <label for="" class="text-white d-none d-sm-block" style="opacity: 0">s</label>
                <div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> <span class="d-md-none">Tìm kiếm</span> </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="filter-block align-middle d-sm-none">
    <form action="" method="get" class="filter-form question-filter">
        <!--begin::Section-->
        <div class="m-accordion m-accordion--bordered d-md-none" id="crazy-menu-detail" role="tablist">
            <div class="form-group row mb-0">
                <div class="col-sm-6 col-md-3">
                    <label for="search-keyword">Từ khóa</label>
                    <div class="input-group">
                        <input type="text" id="search-keyword" class="form-control " name="search" value="{{ $keyword }}" placeholder="Nhập từ khóa">

                    </div>
                </div>

            </div>

            <div class="m-accordion__item">
                <div class="m-accordion__item-head collapsed" role="tab" id="crazy-fillter_head" data-toggle="collapse" href="#crazy-fillter_body" aria-expanded="false">
                    <span class="m-accordion__item-icon">
                        <i class="fa fa-filter"></i>
                    </span>
                    <span class="m-accordion__item-title">Bộ lọc</span>
                    <span class="m-accordion__item-mode"></span>
                </div>
                <div class="m-accordion__item-body collapse" id="crazy-fillter_body" class=" " role="tabpanel" aria-labelledby="crazy-fillter_head" data-parent="#crazy-menu-detail">

                    <div class="form-group row mb-0">
                        <div class="col-sm-6 col-md-2 col-xl-2">
                            <label for="filter-subject-id">Môn thi</label>
                            <div class="input-group">
                                <select name="subject_id" id="filter-subject-id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach ($subject_options as $key => $val)
                                        <option value="{{ $key }}" {{ $subject_id == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2 col-xl-3">
                            <label for="filter-topic-id">Chuyên đề</label>
                            <div class="input-group">
                                <select name="topic_id" id="filter-topic-id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @if ($subject_id && array_key_exists($subject_id, $topic_map) && ($topic_options = $topic_map[$subject_id]))
                                        @foreach ($topic_options as $tid => $tname)
                                            <option value="{{ $tid }}" {{ $tid == $topic_id ? 'selected' : '' }}>{{ $tname }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                        </div>
                        <div class="col-sm-3 col-md-2">
                            <label for="filter-difficult-level">Độ khó</label>
                            <div class="input-group">
                                <select name="difficult_level" id="filter-difficult-level" class="form-control">
                                    <option value="">Tất cả</option>
                                    @php
                                        $d = [1, 2, 3, 4];
                                    @endphp
                                    @foreach ($d as $k)
                                        <option value="{{ $k }}" {{ $difficult_level == $k ? 'selected' : '' }}>{{ $k }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="orderby2">Sắp xếp theo</label>
                            <select name="orderby" id="orderby2" class="form-control">
                                @foreach ($sortable as $key => $val)
                                    <?php
                                    if (is_numeric($key)) {
                                        $v = $val;
                                    } else {
                                        $v = $key;
                                    }
                                    ?>
                                    <option value="{{ $v }}" {{ strtolower($orderby) == strtolower($v) ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="sortype2">Kiểu sắp xếp</label>
                            <select name="sorttype" id="sortype2" class="form-control">
                                @foreach ($sort_list as $k => $vl)
                                    <option value="{{ $k }}" {{ strtoupper($sorttype) == $k ? 'selected' : '' }}>{{ $vl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="per_page2">KQ / trang</label>
                            <select name="per_page" id="per_page2" class="form-control">
                                @foreach ($per_pages as $val => $text)
                                    <option value="{{ $val }}" {{ $val == $per_page ? 'selected' : '' }}>{{ $text }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row mb-0">
                <div class="col-12 col-sm-3 col-md-2 col-xl-1">
                    <label for="" class="text-white d-none d-sm-block" style="opacity: 0">s</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> <span class="d-md-none">Tìm kiếm</span> </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
