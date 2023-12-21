<div class="chat-footer chat-message-form">
    <form method="POST" action="?send=true" enctype="multipart/form-data" id="chat-message-form">
        {{-- <textarea name="message" id="chat-message-input" class="form-control chat_form" placeholder="Nhập nội dung..."></textarea> --}}
        {{-- <input type="text" class="form-control chat_form" placeholder="Enter Message....."> --}}
        <div class="chat_form message-wrapper">
            <div name="message" id="chat-message-input" class="form-control message-input" placeholder="Nhập nội dung..." contenteditable="true"></div>

        </div>
        <div class="form-buttons">
            <button class="btn send-btn" type="submit">
                <span class="material-icons">send</span>
            </button>
        </div>
        <div class="specker-col">
            <a href="#"><span class="material-icons">settings_voice</span></a>
        </div>
    </form>
</div>
<script type="text/template" id="prompt-label-template">
    <div class="prompt-label">{$name} <a href="#">X</a></div>
</script>
