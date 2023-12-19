
@php
$contacts = $options->theme->contacts;
$page_title = $contacts->page_title('Liên hệ');
$socials = $options->theme->socials;
$list = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin'];
$layout = 'master';

@endphp
@section('title', $page_title = 'Liên hệ')
@include($_lib.'register-meta')
@extends($_layout.$layout)
@section('page.header.show', 'breadcrumb')

@section('content')


    @if ($contacts->show_content)
    <div class="row g-4">
        <div class="col-12">
            <div class="blog-details">
                <div class="blog-detail-contain">
                    {{-- <span class="font-light">{{$article->dateFormat('d/m/Y')}}</span> --}}
                    <h1 class="card-title">{{$page_title}}</h1>
                    <div class="article-content">

                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif

    @if ($contacts->show_form)

    <!-- Contact Section Start -->
    <section class="contact-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="materialContainer">
                        <div class="material-details">
                            <div class="title title1 title-effect mb-1 title-left">
                                <h2>{{$contacts->form_title('Gửi liên hệ')}}</h2>
                                <p class="ms-0 w-100">{{$contacts->form_description}}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('web.contacts.ajax-send') }}" data-ajax-url="{{ route('web.contacts.ajax-send') }}" class=" {{ parse_classname('contact-form') }}">
                            @csrf
                            <div class="row g-4 mt-md-1 mt-2">
                                <div class="col-md-12">
                                    <label for="contact-name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control"  name="name" id="contact-name" placeholder="Họ tên"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="contact-email" class="form-label">Email</label>
                                    <input type="email" class="form-control"  name="email" id="contact-email" placeholder="Email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="contact-phone" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" name="phone_number" id="contact-phone" placeholder="Số điện thoại" required>
                                </div>

                                <div class="col-12">
                                    <label for="contact-message" class="form-label">Nội dung liên hệ</label>
                                    <textarea class="form-control" id="contact-message" name="message" rows="5" placeholder="Viết gì đó" required></textarea>
                                </div>

                                <div class="col-auto">
                                    <button class="btn btn-colored-default" type="submit">{{ $contacts->button_text('Gửi liên hệ') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="contact-details">
                        <div>
                            <h2>{{$contacts->page_title('Liên hệ chúng tôi')}}</h2>
                            <h5 class="font-light">{{$contacts->page_note}}</h5>
                            <div class="contact-box">
                                <div class="contact-icon">
                                    <i data-feather="map-pin"></i>
                                </div>
                                <div class="contact-title">
                                    <h4>Địa chỉ :</h4>
                                    <p>{{$contacts->address?$contacts->address:$siteinfo->address}}</p>
                                </div>
                            </div>

                            <div class="contact-box">
                                <div class="contact-icon">
                                    <i data-feather="phone"></i>
                                </div>
                                <div class="contact-title">
                                    <h4>Số điện thoại :</h4>
                                    <p>{{$contacts->phone_number?$contacts->phone_number:$siteinfo->phone_number}}</p>
                                </div>
                            </div>

                            <div class="contact-box">
                                <div class="contact-icon">
                                    <i data-feather="mail"></i>
                                </div>
                                <div class="contact-title">
                                    <h4>Email :</h4>
                                    <p>{{$contacts->email?$contacts->email:$siteinfo->email}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    @endif

    @if ($contacts->show_map && $contacts->map_code)

    <!-- Map Section start -->
    <section class="contact-section">
        <div class="container-fluid">
            <div class="row gy-4">
                <div class="col-12 p-0">
                    <div class="location-map">
                        {!! $contacts->map_code !!}
                        {{-- <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7227.225249699896!2d55.17263937326456!3d25.081115462415855!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43496ad9c645%3A0xbde66e5084295162!2sDubai%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2sin!4v1632538854272!5m2!1sen!2sin"
                            loading="lazy"></iframe> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Map Section End -->

    @endif
@endsection
