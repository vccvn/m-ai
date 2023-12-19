@php
    $template = get_style_template_detail();
    $template_id = $template?$template->id:0;
@endphp
<!-- Modal -->
<div class="modal fade style-modal style-create-modal" id="createStyleFormModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createStyleFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="createStyleFormModalLabel">Tạo phong cách cá nhân</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="description">
                    <p>
                        Trải nghiệm tiện lợi hơn, khám phá gu thời trang phù hợp với bạn một cách dễ dàng.
                        <br />
                        Hãy bắt đầu tạo gu thời trang của bạn chỉ với vài bước đơn giản dưới đây
                    </p>
                </div>
                <div class="form-section step-one">
                    <form action="{{route('web.style-sets.ajax-save')}}" method="post" id="create-style-modal-form-step-one" data-create-style-url="{{route('web.style-sets.create')}}">
                        @csrf
                        <div class="row g-42">
                            <div class="col-sm-6 option-item">
                                <label class="create-style-auto">
                                    <input type="radio" name="mode" value="auto">
                                    <div class="thumbnail">
                                        <img src="{{theme_asset('images/create-style-auto.png')}}" alt="">
                                        <div class="cleafix"></div>
                                    </div>
                                    <h3>Tạo Style Filter tự động</h3>
                                    <p class="desc">
                                        Thông qua việc trả lời một vài câu hỏi. Dựa vào đó hệ thống sẽ xác định sở thích tương đối của bạn
                                    </p>
                                </label>
                            </div>
                            <div class="col-sm-6 option-item">
                                <label class="create-style-manual">
                                    <input type="radio" name="mode" value="manual">
                                    <div class="thumbnail">
                                        <img src="{{theme_asset('images/create-style-manual.png')}}" alt="">
                                        <div class="cleafix"></div>
                                    </div>
                                    <h3>Tạo Style Filter thủ công</h3>
                                    <p class="desc">
                                        Trải nghiệm phối đồ ảo thông minh với đa dạng trang phục để tạo nên phong cách riêng của bạn
                                    </p>
                                </label>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control" name="name" id="step-1-input-style-name" value="" placeholder="Tên phong cách mới của bạn ...">
                            <button class="btn btn-theme btn-outline-default" type="submit">Tếp tục (1/3)</button>
                        </div>
                    </form>
                </div>
                <div class="form-section step-two">
                    <form action="{{route('web.style-sets.ajax-save')}}" method="post" id="create-style-modal-form-step-two">
                        @csrf
                        <input type="hidden" name="template_id" value="{{$template_id}}">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="name" id="step-2-input-style-name" value="" placeholder="Tên phong cách mới của bạn ...">
                        </div>
                        <div class="form-group sizes">
                            <h3 class="title">Bước 1 : chọn kích thước</h3>
                            <div class="inputs row mt-2">
                                <div class="col-md-6 col-input">
                                    <div class="info mb-2 flexable">
                                        <label for="create-style-input-weight">Cân nặng</label>
                                        <div class="output">
                                            <span id="create-style-output-weight">40</span>kg
                                        </div>
                                    </div>
                                    <div class="input-range">
                                        <input type="range" name="weight" id="create-style-input-weight" min="30" max="160" value="65">
                                    </div>
                                </div>
                                <div class="col-md-6 col-input">
                                    <div class="info mb-2 flexable">
                                        <label for="create-style-input-height">Chiều cao</label>
                                        <div class="output">
                                            <span id="create-style-output-height">150</span>cm
                                        </div>
                                    </div>
                                    <div class="input-range">
                                        <input type="range" name="height" id="create-style-input-height" min="50" max="220" value="165">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group shapes">
                            <h3 class="title">Bước 2 : chọn kiểu dáng</h3>
                            <div class="body-shape-groups">
                                <div class="body-shape-sliders">
                                    @if (count($shapes = get_body_shapes()))
                                        @foreach ($shapes as $shape)
                                            <div class="body-shape-item">
                                                <label for="style-modal-shape-item-{{$shape->id}}">
                                                    <input type="radio" name="body_shape_id" id="style-modal-shape-item-{{$shape->id}}" value="{{$shape->id}}">
                                                    <div class="item-info">
                                                        <div class="flexable">
                                                            <div class="thumbnail">
                                                                <img src="{{$shape->getThumbnail()}}" alt="">
                                                            </div>
                                                            <div class="name">
                                                                <h4>{{$shape->name}}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="body-shape-item">
                                        <div class="empty">
                                            End
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group samples">
                            <h3 class="title">Bước 3 : chọn kiểu mẫu</h3>
                            <div class="sample-style-groups {{is_mobile()?'is-mobile':'is-desktop'}}">
                                @if (count($samples = get_sample_styles()))
                                    @if (is_mobile())
                                        <div class="sample-item-slides">
                                            <div class="slides">
                                                @foreach ($samples as $sample)
                                                    <div class="sample-item">
                                                        <input type="radio" name="sample_id" value="{{$sample->id}}" id="sample-item-{{$sample->id}}">
                                                        <label for="sample-item-{{$sample->id}}">
                                                            <div class="img-ratio">
                                                                <div class="ratio" style="padding-top: 133.33333334%"></div>
                                                                <img src="{{$sample->thumbnail_url}}" alt="">
                                                                {{-- <div class="checked"></div> --}}
                                                            </div>
                                                            <h3>{{$sample->name}}</h3>
                                                        </label>

                                                    </div>
                                                @endforeach
                                                <div class="sample-item">
                                                    <div class="flexable text-center" style="opacity: 0.01">
                                                        End
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row row-cols-2 row-cols-md-3 row-cols-xxl-3">
                                            @foreach ($samples as $sample)
                                                <div class="sample-item">
                                                    <input type="radio" name="sample_id" value="{{$sample->id}}" id="sample-item-{{$sample->id}}">
                                                    <label for="sample-item-{{$sample->id}}">
                                                        <div class="img-ratio">
                                                            <div class="ratio" style="padding-top: 133.33333334%"></div>
                                                            <img src="{{$sample->thumbnail_url}}" alt="">
                                                            {{-- <div class="checked"></div> --}}
                                                        </div>
                                                        <h3>{{$sample->name}}</h3>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <div class="d-inline-block">
                                <button class="btn btn-theme btn-outline-default" type="submit">Tếp tục (2/3)</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
