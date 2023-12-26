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
        <div class="slimscroll chat-scrollable">
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

    <script type="text/template" id="reply-writting-template">
        <div class="chats reply-writting" id="reply-writing-{uuid}">
            <div class="chat-content">
                <div class="chat-profile-name text-end">
                    <h6>Đang trả lời...</h6>
                </div>
            </div>
        </div>
    </script>
    @include($_template.'chat.sidebar-right')
@endsection
@section('js')
    <script>
        window.__prompt_input_url__ = '{{route('web.ai.chat.prompt-inputs')}}';
    </script>
    <script src="{{ theme_asset('chat/js/chat.js') }}"></script>
@endsection
