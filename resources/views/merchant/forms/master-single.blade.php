

{{-- @section('header_fixed','false') --}}
			<div class="m-portlet m-portlet--last m-portlet--head-md m-portlet--responsive-mobile2" id="main_portlet">
				<div class="m-portlet__head pr-0">
					<div class="m-portlet__head-progress">

						<!-- here can place a progress bar-->
					</div>
					<div class="m-portlet__head-wrapper">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									{{$cfg->title}}
								</h3>
							</div>
						</div>
						<!--
                        <div class="m-portlet__head-tools">
							<a href="{{$cfg->cancel_button_url}}" class="btn btn-secondary m-btn m-btn--icon m-btn--wide m-btn--md m--margin-right-10">
								<span>
									<i class="la la-arrow-left"></i>
									<span>Quay lại</span>
								</span>
							</a>


							<div class="btn-group">
								<a href="javascript:void('Lưu')" class="btn btn-info m-btn m-btn--icon m-btn--wide m-btn--md sticky-btn-submit-form">
									<span>
										<i class="la la-save"></i>
										<span>{{$cfg->save_button_text}}</span>

									</span>

								</a>


								<button type="button" class="btn btn-info  dropdown-toggle dropdown-toggle-split m-btn m-btn--md m--margin-right-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
								<div class="dropdown-menu dropdown-menu-right">
									<a class="dropdown-item btn-create-new-model" href="javascript:void('Lưu mới')">
										<i class="la la-plus"></i> Tạo mới</a>
									<a class="dropdown-item btn-save-copy" href="javascript:void('Lưu mới')">
										<i class="la la-copy"></i> Lưu thành một bãn sao</a>
									<a class="dropdown-item btn-save-and-exit" href="javascript:void('Lưu mới')">
										<i class="la la-undo"></i> Lưu và thoát</a>
									{{-- <div class="dropdown-divider"></div>
									<a class="dropdown-item btn-cancel" href="#">
										<i class="la la-close"></i> Hủy</a> --}}
								</div>
							</div>
						</div>
                        -->


					</div>
				</div>
				<?php $form->addClass('m-form m-form--fit m-form--label-align-left crazy-form');?>
				<!--begin::Form-->
				<form {!! $form->attrsToStr() !!}>
					@csrf
					{!! $form->hidden_id !!}
					<div class="m-portlet__body">
						@if (($successSession = session('success')) || ($errorSession = session('error')) || ($validateError = $errors->first()))

								<!-- {{$errors->first()}} -->
						@endif
						<div class="form-inputs">
							<div class="row form-group m-form__group">
								@if ($layout_column && count($cfg->form_groups))
									@foreach ($cfg->form_groups as $column)
										@php $group = crazy_arr($column); @endphp
										<div class="col-12 {{$group->class}}">
											@include($_base.'forms.master-inputs', [
												'list'=>$form->notInGroup($group->inputs),
												'group' => $group,
												'group_title' => $group->title,
												'layout_type' => $cfg->layout_type
											])
										</div>
									@endforeach
								@else
									<div class="col-12">
										@include($_base.'forms.master-inputs', [
											'list'=>$form->notInGroup(),
											'group' => crazy_arr(),
											'layout_type' => $cfg->layout_type
										])
									</div>
								@endif
							</div>


						</div>
					</div>
					<div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                        <div class="m-form__actions m-form__actions--solid">
                            <div class="row">
								<div class="col-lg-12 text-center">
									<button type="submit" class="btn btn-info btn-submit-form">
										{{$cfg->save_button_text}}
									</button>
									<a href="{{$cfg->cancel_button_url}}" class="btn btn-secondary">
										{{$cfg->cancel_button_text}}
									</a>
								</div>


                            </div>
                        </div>
                    </div>
				</form>

				<!--end::Form-->
			</div>
