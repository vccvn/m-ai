@extends($_layout . 'chat')
@section('content')
    <!-- Left Sidebar Menu -->
    @include($_template . 'chat.sidebar-menu')
    <!-- /Left Sidebar Menu -->

    <!-- sidebar group -->
    @include($_template . 'chat.sidebar-chat')
    <!-- /Sidebar group -->

    <!-- Chat -->
    <div class="chat" id="middle">
        <div class="slimscroll">
            @include($_template . 'chat.chat-header')
            <div class="chat-body">
                <div class="messages chat-message-list">

                    <div class="chat-line">
                        <span class="chat-date">TBắt đầu cuộc trò chuyện</span>
                    </div>


                </div>
            </div>
        </div>
        @include($_template . 'chat.chat-footer')

    </div>
    <!-- /Chat -->
    <script type="text/template" id="message-block-template">
        <div class="message-block" id="message-block-{$uuid}">

            <div class="chat-line">
                <span class="chat-date chat-name">{$name}</span>
            </div>
        </div>
    </script>

    <script type="text/template" id="message-item-template">
        <div class="chats {$role}">
            <div class="chat-content">
                <div class="message-content">
                    {$message}
                    <!--
                    <div class="chat-time">
                        <div>
                            <div class="time"><i class="fas fa-clock"></i> 10:02</div>
                        </div>
                    </div>
                    -->
                </div>
                <!--
                <div class="chat-profile-name text-end">
                    <h6>Alexandr</h6>
                </div>-->
            </div>
            <!-- <div class="chat-avatar">
                <img src="assets/img/avatar/avatar-12.jpg" class="rounded-circle dreams_chat" alt="image">
            </div> -->
            <div class="chat-action-btns me-2">
                <div class="chat-action-col">
                    <a href="#" class="btn-copy-message"><span ><i class="far fa-copy"></i></span></a>
                </div>
            </div>
        </div>
    </script>
    <!-- Right sidebar -->
    <div class="right-sidebar right_sidebar_profile hide-right-sidebar" id="middle1">
        <div class="right-sidebar-wrap active">
            <div class="slimscroll">
                <div class="left-chat-title d-flex justify-content-between align-items-center p-3">
                    <div class="chat-title">
                        <h4>PROFILE</h4>
                    </div>
                    <div class="contact-close_call text-end">
                        <a href="#" class="close_profile close_profile4">
                            <span class="material-icons">close</span>
                        </a>
                    </div>
                </div>
                <div class="sidebar-body">
                    <div class="mt-0 right_sidebar_logo">
                        <div class="text-center mb-2 right-sidebar-profile">
                            <figure class="avatar avatar-xl mb-3">
                                <img src="{{ theme_asset('chat/ing/avatar/avatar-2.jpg') }}" class="rounded-circle" alt="image">
                            </figure>
                            <h5 class="profile-name">Scott Albright</h5>
                            <div class="online-profile">
                                <span>online</span>
                            </div>
                        </div>
                        <div>
                            <div class="about-media-tabs">
                                <nav>
                                    <div class="nav nav-tabs justify-content-center" id="nav-tab">
                                        <a class="nav-item nav-link active" id="nav-home-tab" data-bs-toggle="tab" href="#about">About</a>
                                        <a class="nav-item nav-link" id="nav-profile-tab" data-bs-toggle="tab" href="#media">Media</a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="about">
                                        <p>If several languages coalesce, the grammar of the resulting language is more simple and regular than that of the individual.</p>
                                        <div class="member-details">
                                            <ul>
                                                <li>
                                                    <h6>Phone</h6>
                                                    <span>555-555-21541</span>
                                                </li>
                                                <li>
                                                    <h6>Nick Name</h6>
                                                    <span>Alberywo</span>
                                                </li>
                                                <li>
                                                    <h6>Email</h6>
                                                    <span><a href="https://dreamschat.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="db9ab7b9bea9a2acb49bbcb6bab2b7f5b8b4b6">[email&#160;protected]</a></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="social-media-col">
                                            <h6>Social media accounts</h6>
                                            <ul>
                                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                                <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                                                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                                <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                            </ul>
                                        </div>
                                        <div class="settings-col">
                                            <h6>Settings</h6>
                                            <ul>
                                                <li class="d-flex align-items-center">
                                                    <label class="switch">
                                                        <input type="checkbox" checked>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <div>
                                                        <span>Block</span>
                                                    </div>
                                                </li>
                                                <li class="d-flex align-items-center">
                                                    <label class="switch">
                                                        <input type="checkbox">
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <div>
                                                        <span>Mute</span>
                                                    </div>
                                                </li>
                                                <li class="d-flex align-items-center">
                                                    <label class="switch">
                                                        <input type="checkbox">
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <div>
                                                        <span>Get notification</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="media">
                                        <div class="file-download-col">
                                            <ul>
                                                <li>
                                                    <div class="image-download-col">
                                                        <a href="{{ theme_asset('chat/ing/chat-download.jpg') }}" data-fancybox="gallery" class="fancybox">
                                                            <img src="{{ theme_asset('chat/ing/chat-download.jpg') }}" alt="">
                                                        </a>
                                                        <div class="download-action d-flex align-items-center">
                                                            <div><a href="#"><i class="fas fa-cloud-download-alt"></i></a></div>
                                                            <div><a href="#"><i class="fas fa-ellipsis-h"></i></a></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="image-download-col">
                                                        <a href="{{ theme_asset('chat/ing/chat-download.jpg') }}" data-fancybox="gallery" class="fancybox">
                                                            <img src="{{ theme_asset('chat/ing/chat-download.jpg') }}" alt="">
                                                        </a>
                                                        <div class="download-action d-flex align-items-center">
                                                            <div><a href="#"><i class="fas fa-cloud-download-alt"></i></a></div>
                                                            <div><a href="#"><i class="fas fa-ellipsis-h"></i></a></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="image-download-col">
                                                        <a href="{{ theme_asset('chat/ing/chat-download.jpg') }}" data-fancybox="gallery" class="fancybox">
                                                            <img src="{{ theme_asset('chat/ing/chat-download.jpg') }}" alt="">
                                                        </a>
                                                        <div class="download-action d-flex align-items-center">
                                                            <div><a href="#"><i class="fas fa-cloud-download-alt"></i></a></div>
                                                            <div><a href="#"><i class="fas fa-ellipsis-h"></i></a></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="image-download-col">
                                                        <a href="{{ theme_asset('chat/ing/chat-download.jpg') }}" data-fancybox="gallery" class="fancybox">
                                                            <img src="{{ theme_asset('chat/ing/chat-download.jpg') }}" alt="">
                                                        </a>
                                                        <div class="download-action d-flex align-items-center">
                                                            <div><a href="#"><i class="fas fa-cloud-download-alt"></i></a></div>
                                                            <div><a href="#"><i class="fas fa-ellipsis-h"></i></a></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="image-download-col">
                                                        <a href="{{ theme_asset('chat/ing/chat-download.jpg') }}" data-fancybox="gallery" class="fancybox">
                                                            <img src="{{ theme_asset('chat/ing/chat-download.jpg') }}" alt="">
                                                        </a>
                                                        <div class="download-action d-flex align-items-center">
                                                            <div><a href="#"><i class="fas fa-cloud-download-alt"></i></a></div>
                                                            <div><a href="#"><i class="fas fa-ellipsis-h"></i></a></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="image-download-col">
                                                        <a href="{{ theme_asset('chat/ing/chat-download.jpg') }}" data-fancybox="gallery" class="fancybox">
                                                            <img src="{{ theme_asset('chat/ing/chat-download.jpg') }}" alt="">
                                                        </a>
                                                        <div class="download-action d-flex align-items-center">
                                                            <div><a href="#"><i class="fas fa-cloud-download-alt"></i></a></div>
                                                            <div><a href="#"><i class="fas fa-ellipsis-h"></i></a></div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="full-width text-center">
                                                    <a href="#" class="load-more-btn">More 154 Files <i class="fas fa-sort-down"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-col">
                    <ul>
                        <li><a href="#"><span class="material-icons">report_problem</span> Report Chat</a></li>
                        <li><a href="#"><span><i class="far fa-trash-alt"></i></span> Delete Chat</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Right sidebar -->
@endsection
@section('js')
    <script src="{{ theme_asset('chat/js/chat.js') }}"></script>
@endsection
