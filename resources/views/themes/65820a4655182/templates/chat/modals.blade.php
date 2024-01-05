
        <!-- Add User -->
        <div class="modal fade" id="add-user">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
							<span class="material-icons">person_add</span>Add Friends
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Card -->
                        <form action="https://dreamschat.dreamstechnologies.com/template-html/template2/new-friends">
                            <div class="form-group">
                                <label>User Name</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                        </form>
                        <!-- Card -->
                        <div class="form-row profile_form text-end float-end m-0 align-items-center">
                            <!-- Button -->
                            <div class="cancel-btn me-4">
                                <a href="#" data-bs-dismiss="modal" aria-label="Close">Cancel</a>
                            </div>
                            <div class="">
                                <button type="button" class="btn btn-block newgroup_create mb-0" data-bs-dismiss="modal" aria-label="Close">
                                    Add User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add User -->

        <!-- Add Contact -->
        <div class="modal fade" id="add-contact">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
							<span class="material-icons">person_add</span>Add Friends
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Card -->
                        <form action="https://dreamschat.dreamstechnologies.com/template-html/template2/new-friends">
                            <div class="form-group">
                                <label>Name</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                            <div class="form-group">
                                <label>Nickname</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="email">
                            </div>
                        </form>
                        <!-- Card -->
                        <div class="form-row profile_form text-end float-end m-0 align-items-center">
                            <!-- Button -->
                            <div class="cancel-btn me-4">
                                <a href="#" data-bs-dismiss="modal" aria-label="Close">Cancel</a>
                            </div>
                            <div class="">
                                <button type="button" class="btn btn-block newgroup_create mb-0" data-bs-dismiss="modal" aria-label="Close">
                                    Add to contacts
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Contact -->

        <!-- Add New Group -->
        <div class="modal fade" id="add-new-group">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span class="material-icons group-add-btn">group_add</span>Create a New Group
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Card -->
                        <form action="https://dreamschat.dreamstechnologies.com/template-html/template2/new-friends">
                            <div class="form-group">
                                <label>Group Name</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                            <div class="form-group">
                                <label>Choose profile picture</label>
                                <div class="custom-input-file form-control form-control-lg group_formcontrol">
                                    <input type="file" class="">
                                    <span class="browse-btn">Browse File</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Topic (optional)</label>
                                <input class="form-control form-control-lg group_formcontrol" name="new-chat-title" type="text">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control form-control-lg group_formcontrol"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="d-flex align-items-center">
                                    <label class="custom-radio me-3">Private Group
                                        <input type="radio" checked="checked" name="radio">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="custom-radio">Public Group
                                      <input type="radio" name="radio">
                                      <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </form>
                        <!-- Card -->
                        <div class="form-row profile_form text-end float-end m-0 align-items-center">
                            <!-- Button -->
                            <div class="cancel-btn me-4">
                                <a href="#" data-bs-dismiss="modal" aria-label="Close">Cancel</a>
                            </div>
                            <div class="">
                                <button type="button" class="btn btn-block newgroup_create mb-0" data-bs-toggle="modal" data-bs-target="#add-group-member" data-bs-dismiss="modal" aria-label="Close">
                                    Add Participants
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add New Group -->

        <!-- Add Group Members -->
        <div class="modal fade custom-border-modal" id="add-group-member">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span class="material-icons group-add-btn">group_add</span>Add Group Members
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="search_chat has-search me-0 ms-0">
                            <span class="fas fa-search form-control-feedback"></span>
                            <input class="form-control chat_input" id="search-contact1" type="text" placeholder="Search Contacts">
                        </div>
                        <div class="sidebar">
                            <span class="contact-name-letter">A</span>
                            <ul class="user-list mt-2">
                                <li class="user-list-item">
                                    <div class="avatar avatar-online">
                                        <img src="{{ai_startup_asset('ai/chat/assets/img/avatar/avatar-1.jpg')}}" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body align-items-center">
                                        <div>
                                            <h5>Albert Rodarte</h5>
                                        </div>
                                        <div class="last-chat-time">
                                            <label class="custom-check">
                                                <input type="checkbox" checked="checked">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </li>
                                <li class="user-list-item">
                                    <div class="avatar avatar-online">
                                        <img src="{{ai_startup_asset('ai/chat/assets/img/avatar/avatar-2.jpg')}}" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body align-items-center">
                                        <div>
                                            <h5>Allison Etter</h5>
                                        </div>
                                        <div class="last-chat-time">
                                            <label class="custom-check">
                                                <input type="checkbox">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <span class="contact-name-letter mt-3">C</span>
                            <ul class="user-list mt-2">
                                <li class="user-list-item">
                                    <div class="avatar avatar-online">
                                        <img src="{{ai_startup_asset('ai/chat/assets/img/avatar/avatar-3.jpg')}}" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body align-items-center">
                                        <div>
                                            <h5>Craig Smiley</h5>
                                        </div>
                                        <div class="last-chat-time">
                                            <label class="custom-check">
                                                <input type="checkbox">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </li>
                                <li class="user-list-item">
                                    <div class="avatar avatar-online">
                                        <img src="{{ai_startup_asset('ai/chat/assets/img/avatar/avatar-4.jpg')}}" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body align-items-center">
                                        <div>
                                            <h5>Caniel Clay</h5>
                                        </div>
                                        <div class="last-chat-time">
                                            <label class="custom-check">
                                                <input type="checkbox">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="form-row profile_form text-end float-end m-0 mt-3 align-items-center">
                            <!-- Button -->
                            <div class="cancel-btn me-3">
                                <a href="#" data-bs-dismiss="modal" aria-label="Close">Cancel</a>
                            </div>
                            <div class="">
                                <button type="button" class="btn newgroup_create previous mb-0 me-3" data-bs-toggle="modal" data-bs-target="#add-new-group" data-bs-dismiss="modal" aria-label="Close">
                                    Previous
                                </button>
                            </div>
                            <div class="">
                                <button type="button" class="btn btn-block newgroup_create mb-0" data-bs-dismiss="modal" aria-label="Close">
                                    Create Group
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Group Members -->

        <!-- Video Call -->
        <div class="modal fade" id="video_call" role="document">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content voice_content">
                    <div class="modal-body voice_body">
                        <div class="call-box incoming-box">
                            <div class="call-wrapper">
                                <div class="call-inner">
                                    <div class="call-user">
                                        <img alt="User Image" src="{{ai_startup_asset('ai/chat/assets/img/avatar/avatar-8.jpg')}}" class="call-avatar">
                                        <h4>Brietta Blogg <span>video calling</span>
                                        </h4>
                                    </div>
                                    <div class="call-items">
                                        <a href="#" class="btn call-item call-end" data-bs-dismiss="modal">
                                            <span class="material-icons">close</span>
                                        </a>
                                        <a href="#" class="btn call-item call-start" data-bs-dismiss="modal">
                                            <i class="fas fa-video"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Video Call -->

        <!-- Voice Call -->
        <div class="modal fade" id="voice_call" role="document">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content voice_content">
                    <div class="modal-body voice_body">
                        <div class="call-box incoming-box">
                            <div class="call-wrapper">
                                <div class="call-inner">
                                    <div class="call-user">
                                        <img alt="User Image" src="{{ai_startup_asset('ai/chat/assets/img/avatar/avatar-8.jpg')}}" class="call-avatar">
                                        <h4>Brietta Blogg <span>voice calling</span>
                                        </h4>
                                    </div>
                                    <div class="call-items">
                                        <a href="#" class="btn call-item call-end" data-bs-dismiss="modal">
                                            <span class="material-icons">close</span>
                                        </a>
                                        <a href="#" class="btn call-item call-start" data-bs-dismiss="modal">
                                            <i class="fas fa-video"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Voice Call -->
