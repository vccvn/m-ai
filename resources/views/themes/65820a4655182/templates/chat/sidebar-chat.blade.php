<div class="sidebar-group left-sidebar chat_sidebar">
    <!-- Chats sidebar -->
    <div id="chats" class="left-sidebar-wrap sidebar active slimscroll">
        <div class="slimscroll">

            <!-- Left Chat Title -->
            <div class="left-chat-title d-flex justify-content-between align-items-center">
                <div class="chat-title">
                    <h4>Trợ lý AI</h4>
                </div>
            </div>
            <!-- /Left Chat Title -->

            <div class="search_chat has-search mb-0">
                <span class="fas fa-search form-control-feedback"></span>
                <input class="form-control chat_input prompt-search" id="search-contact1" type="text" placeholder="Search Contacts">
            </div>
            <div class="sidebar-body" id="chatsidebar">

                <!-- Left Chat Title -->
                <div class="left-chat-title d-flex justify-content-between align-items-center ps-0 pe-0">
                    <div class="chat-title">
                        <h4>Danh sách</h4>
                    </div>
                    {{-- <div class="add-section">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add-group-call"><i class="fas fa-phone-alt"></i></a>
                    </div> --}}
                </div>
                <!-- /Left Chat Title -->

                <nav>
                    <div class="nav nav-tabs chat-scroll side_bar" id="nav-tab">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-bs-toggle="tab" href="#all-prompt">Tất cả</a>
                        @if (count($topics))
                            @foreach ($topics as $topic)
                                <a class="nav-item nav-link" id="nav-profile-tab{{ $topic->id }}" data-bs-toggle="tab" href="#topic-tab-{{ $topic->id }}">{{ $topic->name }}</a>
                            @endforeach
                        @endif
                    </div>
                </nav>
                <div class="tab-content settings-form">

                    <!-- Tab Contents -->
                    <div class="tab-pane fade show active" id="all-prompt">
                        <ul class="user-list prompt-list mt-2">
                            @foreach ($allPrompt as $prompt)
                                <li class="user-list-item prompt-list-item" data-id="{{ $prompt->id }}">
                                    <div>
                                        <div class="avatar avatar-online">
                                            <div class="letter-avatar">
                                                {{ substr($prompt->name, 0, 1) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="users-list-body align-items-center">
                                        <div>
                                            <h5>{{ $prompt->name }}</h5>
                                            <p>{{ $prompt->description }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- /Tab Contents -->

                    @foreach ($topics as $topic)
                        <!-- Tab Contents -->
                        <div class="tab-pane fade" id="topic-tab-{{ $topic->id }}">
                            <ul class="user-list prompt-list mt-2">
                                @if ($prompts = $topic->prompts)
                                    @foreach ($prompts as $prompt)
                                        <li class="user-list-item prompt-list-item" data-id="{{ $prompt->id }}">
                                            <div>
                                                <div class="avatar avatar-online">
                                                    <div class="letter-avatar">
                                                        {{ substr($prompt->name, 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="users-list-body align-items-center">
                                                <div>
                                                    <h5>{{ $prompt->name }}</h5>
                                                    <p>{{ $prompt->description }}</p>
                                                    <span class="placeholder d-none">{{$prompt->placeholder}}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <!-- /Tab Contents -->
                    @endforeach


                </div>
            </div>
        </div>
    </div>
    <!-- / Chats sidebar -->
    <script type="text/template" id="prompt-item-template">
        <li class="user-list-item prompt-list-item"  data-id="{$id}">
            <div>
                <div class="avatar avatar-online">
                    <div class="letter-avatar">
                        {$firstChart}
                    </div>
                </div>
            </div>
            <div class="users-list-body align-items-center">
                <div>
                    <h5>{$name}</h5>
                    <p>{$description}</p>
                    <span class="placeholder d-none">{$placeholder}</span>
                </div>
            </div>
        </li>
    </script>
</div>
