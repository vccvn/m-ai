<div class="chat-footer chat-message-form">
    <form method="POST" action="{{ route('web.ai.chat.send-message') }}" enctype="multipart/form-data" id="chat-message-form">
        {{-- <textarea name="message" id="chat-message-input" class="form-control chat_form" placeholder="Nhập nội dung..."></textarea> --}}
        {{-- <input type="text" class="form-control chat_form" placeholder="Enter Message....."> --}}
        <input type="hidden" name="id" id="input-chat-id">
        <input type="hidden" name="prompt_id" id="input-chat-prompt-id">
        <div class="chat_form message-wrapper">
            <div class="chat-message-block">
                <div class="chat-message-header">

                </div>
                <div class="chat-message-body">

                    <div class="criteria-wrapper">

                    </div>

                    <div class="chat-input-wrapper">
                        <div name="message" id="chat-message-input" class="form-control message-input" placeholder="Nhập nội dung..." contenteditable="true"></div>
                        {{-- <textarea name="message" id="chat-message-input" class="chat-message-input" placeholder="Nhập nội dung..."></textarea> --}}
                    </div>
                </div>
            </div>
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
    <div class="prompt-label"><span>{$name}</span> <a href="#">X</a></div>
</script>

<script type="text/template" id="prompt-top-label-template">
    <div class="prompt-top-label">
        <span class="label-box">
            {$name} <a href="#">X</a>
        </span>
        {$button}
    </div>
</script>

<script type="text/template" id="prompt-top-toggle-buuton-template">
    <span class="toggle-button criteria-toggle-button">
        <span class="material-icons">
            arrow_downward
        </span>
    </span>
</script>

<script type="text/template" id="prompt-criteria-textarea-template">
    <textarea name="criteria[{$name}]" id="prompt-{$name}" class="inp-criteria" data-name="{$name}" placeholder="{$placeholder}"></textarea>
</script>
<script type="text/template" id="prompt-criteria-input-template">
    <input type="{$type}" name="criteria[{$name}]" id="prompt-{$name}" class="inp-criteria" data-name="{$name}" placeholder="{$placeholder}">
</script>
<script type="text/template" id="prompt-criteria-input-wrapper-template">
    <div class="prompt-criteria-wrapper" id="prompt-criteria-wrapper-{$id}">
        <label for="prompt-{$name}">{$label}: </label>
        {$htmlInput}
    </div>
</script>
