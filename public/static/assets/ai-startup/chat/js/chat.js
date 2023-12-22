$(function () {
    const chatData = {
        type: 'new',
        prompt_id: 0,
        id: null,
        message: '',
        name: App.date('H:i', +7),
        uuid: App.str.rand(32)
    };

    let chats = {};

    const pendingList = [];

    const htmlTemplates = {
        promptItem: '',
        promptLabel: $('#prompt-label-template').html(),
        messageBlock: $('#message-block-template').html(),
        messageItem: $('#message-item-template').html(),
    };
    const tempElement = document.createElement('div');
    const $form = $('#chat-message-form');
    const $inputMessage = $('#chat-message-input');
    const $messageWrapper = $('.message-wrapper');

    const $chatList = $('.chat-message-list');
    const SEND_URL = $form.attr('action');

    let currentChatData = {
        uuid: chatData.uuid,
        id: chatData.id,
        prompt_id: chatData.prompt_id
    }

    let isSending = false;
    let sendingID = null;

    const checkTextareaContentHeight = function checkTextareaContentHeight(el) {
        el.setAttribute('style', 'height:' + (el.scrollHeight) + 'px;overflow-y:hidden;');
    }
    const addElementToCheckHeight = function addElementToCheckHeight(els) {
        $(els).each(function () {
            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
        }).on('input keyup keydown change', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }


    const strpTags = str => {
        tempElement.innerHTML = str;
        // console.log(tempElement);
        const stringWithoutTags = tempElement.textContent || tempElement.innerText || '';
        // console.log(stringWithoutTags);
        const ss = stringWithoutTags.replace(/<[^>]*>/g, '');
        // console.log(ss);
        return ss;
    }


    const getMesssageContent = () => {
        let content = '';
        content = $inputMessage.html();
        return content;
    }
    const setMessageContent = (content) => {
        $inputMessage.html(content);
        if (content == '') {
            $inputMessage[0].innerHTML = null;
            // $inputMessage.chldren().remove();
            $inputMessage.empty();

        }
    }
    const clearMessageContent = () => {
        $inputMessage[0].innerHTML = null;
        // $inputMessage.chldren().remove();
        $inputMessage.empty();
    }

    const setMessagePlaceholder = placeholder => $inputMessage.attr('placeholder', placeholder);

    const checkMessageInputScroll = () => {
        let windowHeight = window.innerHeight;
        let messageInputHeight = $inputMessage.height();
        let r = 60 / 100 * windowHeight - 40;
        if (messageInputHeight > r) {
            $inputMessage.addClass('scroll-height');
        }
        else {
            $inputMessage.removeClass('scroll-height');

        }

    }

    const newChat = (prompt_id) => {
        let pid = 0;
        if (prompt_id) {
            let n = Number(prompt_id);
            if (n && n > 0 && parseInt(prompt_id) == n) {
                pid = n;
            }
        }
        $messageWrapper.find('.prompt-label').remove();
        chatData.type = 'new';
        chatData.id = null;
        chatData.prompt_id = pid;
        chatData.message = '';
        chatData.name = App.date('H:i', +7);

        chatData.uuid = App.str.rand();
        return true;
    }

    const newChatWithPrompt = (id, name) => {
        newChat(id);
        $messageWrapper.prepend(App.str.eval(htmlTemplates.promptLabel, { id, name }));
        chatData.name = name;
        setMessagePlaceholder('Nhập thêm chi tiết...');
    }

    const createChatBlock = (uuid, name, prepend) => {
        let chatHtml = App.str.eval(htmlTemplates.messageBlock, {uuid, name});
        if(prepend){
            $chatList.prepend(chatHtml);
        }else{
            $chatList.append(chatHtml);
        }
        return $chatList.find('#message-block-' + uuid);
    }

    const pushChatMessage = ($chatBlock, role, message) => {
        if(!$chatBlock) return false;
        if(App.isString($chatBlock))
            $chatBlock = $('#message-block-' + $chatBlock);
        if(!App.isObject($chatBlock))
            return false;
        $chatBlock.append(App.str.eval(htmlTemplates.messageItem, {role, message}));
    }
    const pushChatMessageList = ($chatBlock, messages) => {
        if(!$chatBlock || !App.isArray(messages)) return false;
        if(App.isString($chatBlock))
            $chatBlock = $('#message-block-' + $chatBlock);
        if(!App.isObject($chatBlock))
            return false;
        messages.map(message => App.isObject(message) && $chatBlock.append(App.str.eval(htmlTemplates.messageItem, {role: message.role, message: message.message})));
    }

    function sendMessage (data){
        isSending = true;
        App.api.post(SEND_URL, data)
        .then(rs => {
            if(rs.status){
                pushChatMessage(data.uuid, rs.data.role, rs.data.message);
                if(data.uuid = chatData.uuid){
                    chatData.id = rs.data.chat_id;
                    chatData.type = 'continue';
                }
            }
            else{
                App.Swal.warning(rs.message);
            }
            isSending = false;
            checkSendingProccess();


        }).catch(e => {
            isSending = false;
            checkSendingProccess();
        })
    }

    function checkSendingProccess() {
        if(isSending || pendingList.length == 0) return false;
        // isSending = true;

        let data = pendingList.shift();

        sendMessage(data);

    };

    const createChat = data => {
        const currentData = {
            type: data.type,
            prompt_id: data.prompt_id,
            id: data.id,
            message: data.message,
            name: data.name,
            uuid: data.uuid
        }
        currentChatData = {
            id: data.id,
            prompt_id: data.prompt_id,
            uuid: data.uuid
        };

        chats[data.uuid] = true;

        let $chatBlock = createChatBlock(currentData.uuid, currentData.name);
        pushChatMessage($chatBlock, 'user', currentData.message);
        sendingID = data.uuid;

        pendingList.push(currentData);

        chatData.type = 'continue';
        checkSendingProccess();
    }
    const updateChat = data => {
        const currentData = {
            type: data.type,
            prompt_id: data.prompt_id,
            id: data.id,
            message: data.message,
            name: data.name,
            uuid: data.uuid
        }
        currentChatData = {
            id: data.id,
            prompt_id: data.prompt_id,
            uuid: data.uuid
        };


        let $chatBlock = $('#message-block-' + data.uuid);
        pushChatMessage($chatBlock, 'user', currentData.message);
        sendingID = data.uuid;

        pendingList.push(currentData);

        chatData.type = 'continue';
        checkSendingProccess();
    };

    const submitForm = (clean) => {
        let message = getMesssageContent();
        let cleanContent = strpTags(message).trim();
        // console.log(cleanContent);
        if ((!cleanContent || cleanContent == '') && (chatData.type != 'new' || (chatData.type == 'new' && chatData.prompt_id == 0))) {
            // console.log('không làm gì');
            if (clean) {
                setMessageContent('');
                setTimeout(() => {
                    setMessageContent();
                }, 20);
            }
        } else {
            chatData.message = message;
            if(typeof chats[chatData.uuid] == "undefined" || chatData.type !="continue" ){
                createChat(chatData);
            }else{
                updateChat(chatData);
            }

            setMessageContent('');
            clearMessageContent();
            setMessagePlaceholder('Viết gì đó...');
            // App.Swal.showLoading();
            // setTimeout(() => App.Swal.error('Lỗi server. Vui lòng thử lại sau giây lát'), 3000);

        }
    }

    let clearAfterKeyUp = false;

    $inputMessage.on('keydown', evt => {
        checkMessageInputScroll();
        if (evt.keyCode == 13 && !evt.shiftKey) {
            evt.preventDefault();
            submitForm();
            clearAfterKeyUp = true;
        }
    });

    $inputMessage.on('keyup', evt => {
        if (clearAfterKeyUp) {
            clearAfterKeyUp = false;
            clearMessageContent();
        }
        checkMessageInputScroll();
    });

    $form.on('submit', evt => {
        evt.preventDefault();
        submitForm(true);
    });

    $(document).on('click', ".prompt-list-item", function (e) {
        e.preventDefault();
        let prompt_id = $(this).data('id');
        let name = $(this).find('h5').html();
        newChatWithPrompt(prompt_id, name);
    });

    $messageWrapper.on('click', ".prompt-label a", function (e) {
        e.preventDefault();
        newChat();
        setMessagePlaceholder('Viết gì đó...');

    });


    // addElementToCheckHeight($inputMessage[0]);
});
// $('textarea').each(function () {
//     this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
//   }).on('input', function () {
//     this.style.height = 'auto';
//     this.style.height = (this.scrollHeight) + 'px';
//   });
