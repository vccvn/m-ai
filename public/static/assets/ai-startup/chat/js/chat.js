$(function () {
    const chatData = {
        type: 'new',
        prompt_id: 0,
        prompt_name: '',
        id: null,
        message: '',
        name: App.date('H:i', +7),
        uuid: App.str.rand(32),
        hasCriteria: false,
        criteriaTotal: 0
    };

    let chats = {};

    const pendingList = [];

    const chatStorage = {};

    const CRITERIA_LABELS = {};

    const htmlTemplates = {
        promptItem: '',
        promptTopLabel: $('#prompt-top-label-template').html(),
        promptLabel: $('#prompt-label-template').html(),
        messageBlock: $('#message-block-template').html(),
        messageItem: $('#message-item-template').html(),
        replyWritting: $('#reply-writting-template').html(),

        criteriaWrapper: $('#prompt-criteria-input-wrapper-template').html(),
        criteriaInput: $('#prompt-criteria-input-template').html(),
        criteriaTextarea: $('#prompt-criteria-textarea-template').html(),
    };
    const tempElement = document.createElement('div');
    const $form = $('#chat-message-form');
    const $inputMessage = $('#chat-message-input');
    const $messageWrapper = $('.message-wrapper');
    const $criteriaWrapper = $('.criteria-wrapper');
    const $messageHeader = $('.chat-message-header');

    const $chatList = $('.chat-message-list');
    const $chatInputWrapper = $('.chat-input-wrapper');

    const $chatMessageBlock = $('.chat-message-block');
    const $chatBody = $('.chat-scrollable .chat-body');
    const $chatFooter = $('.chat-footer');

    const SEND_URL = $form.attr('action');
    const INPUT_URL = window.__prompt_input_url__;

    let currentChatData = {
        uuid: chatData.uuid,
        id: chatData.id,
        prompt_id: chatData.prompt_id,
        prompt_name: chatData.prompt_name
    }

    let isSending = false;
    let sendingID = null;

    const updateChatBodyPaddingBottom = () => {
        $chatBody.css({ paddingBottom: ($chatFooter.height() + 30) + "px" })
    }

    const checkTextareaContentHeight = function checkTextareaContentHeight(el) {
        el.setAttribute('style', 'height:' + (el.scrollHeight) + 'px;overflow-y:hidden;');
    }
    const addElementToCheckHeight = function addElementToCheckHeight(els) {
        $(els).each(function () {
            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
        }).on('input keyup keydown change', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            updateChatBodyPaddingBottom();
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
        // content = $inputMessage.val();
        return content;
    }
    const setMessageContent = (content) => {
        $inputMessage.html(content);
        if (content == '') {
            $inputMessage[0].innerHTML = null;
            // $inputMessage.chldren().remove();
            $inputMessage.empty();

        }
        // $inputMessage.val(content);

    }
    const clearMessageContent = () => {
        $inputMessage[0].innerHTML = null;
        // $inputMessage.chldren().remove();
        $inputMessage.empty();

        // $inputMessage.val('')
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
        chatData.prompt_name = '';
        chatData.hasCriteria = false;
        chatData.criteriaTotal = 0;
        $messageWrapper.removeClass('has-criteria');

        $criteriaWrapper.html('');
        $messageHeader.html("");
        $criteriaWrapper.empty();
        $messageHeader.empty();
        updateChatBodyPaddingBottom()
        return true;
    }

    const newChatWithPrompt = (id, name, placeholder) => {
        newChat(id);
        chatData.name = name;
        chatData.prompt_name = name;
        App.Swal.showLoading();
        App.api.post(INPUT_URL, { prompt_id: id })
            .then(rs => {
                App.Swal.hideLoading();
                // console.log(rs.data);
                if (rs.status && rs.data && rs.data.inputs && rs.data.inputs.length) {
                    chatData.hasCriteria = true;
                    chatData.criteriaTotal = rs.data.inputs.length;
                    let htmlInputs = rs.data.inputs.map(inp => {
                        CRITERIA_LABELS[inp.name] = inp.label;
                        inp.placeholder = inp.placeholder ? inp.placeholder : 'Nhập ' + inp.label;
                        return App.str.eval(
                            htmlTemplates.criteriaWrapper,
                            Object.assign(
                                {},
                                inp,
                                {
                                    htmlInput: App.str.eval(
                                        inp.type == 'textarea' ? htmlTemplates.criteriaTextarea : htmlTemplates.criteriaInput,
                                        inp
                                    )
                                }
                            )
                        )
                    }).join("")
                        + App.str.eval(
                            htmlTemplates.criteriaWrapper, { id: 0, htmlInput: '', label: "Message" }
                        );
                    $messageHeader.html(App.str.eval(htmlTemplates.promptTopLabel, { name: rs.data.prompt.name }));
                    $criteriaWrapper.html(htmlInputs);
                    setMessagePlaceholder(rs.data.prompt.placeholder || placeholder || "Nhập thêm nội dung bạn muốn để AI đưa ra thông tin chính xác hơn")
                    let $ta = $criteriaWrapper.find('textarea');
                    if ($ta.length) {
                        addElementToCheckHeight($ta);
                    }
                    $messageWrapper.addClass('has-criteria');
                } else {
                    $messageHeader.prepend(App.str.eval(htmlTemplates.promptTopLabel, { id, name }));
                    setMessagePlaceholder(placeholder ? placeholder : 'Nhập ' + name);
                }
                updateChatBodyPaddingBottom()
            })
            .catch(e => {
                console.warn(e);
                App.Swal.error('Lỗi hệ thống! Vui lòng thử lại sau giây lát');
                updateChatBodyPaddingBottom()

            })

    }

    const createChatBlock = (uuid, name, prepend) => {
        let chatHtml = App.str.eval(htmlTemplates.messageBlock, { uuid, name });
        if (prepend) {
            $chatList.prepend(chatHtml);
        } else {
            $chatList.append(chatHtml);
        }
        return $chatList.find('#message-block-' + uuid);
    }


    const setupTypewriter = (t) => {
        var HTML = t.innerHTML;

        t.innerHTML = "";

        var cursorPosition = 0,
            tag = "",
            writingTag = false,
            tagOpen = false,
            typeSpeed = 100, // higher number = slower
            tempTypeSpeed = 0;

        var type = function () {

            if (writingTag === true) {
                tag += HTML[cursorPosition];
            }

            if (HTML[cursorPosition] === "<") {
                tempTypeSpeed = 0;
                if (tagOpen) {
                    tagOpen = false;
                    writingTag = true;
                } else {
                    tag = "";
                    tagOpen = true;
                    writingTag = true;
                    tag += HTML[cursorPosition];
                }
            }
            if (!writingTag && tagOpen) {
                tag.innerHTML += HTML[cursorPosition];
            }
            if (!writingTag && !tagOpen) {
                if (HTML[cursorPosition] === " ") {
                    tempTypeSpeed = 0;
                }
                else {
                    tempTypeSpeed = (Math.random() * typeSpeed) + 50;
                }
                t.innerHTML += HTML[cursorPosition];
            }
            if (writingTag === true && HTML[cursorPosition] === ">") {
                tempTypeSpeed = (Math.random() * typeSpeed) + 50;
                writingTag = false;
                if (tagOpen) {
                    var newSpan = document.createElement("span");
                    t.appendChild(newSpan);
                    newSpan.innerHTML = tag;
                    tag = newSpan.firstChild;
                }
            }

            cursorPosition += 1;
            if (cursorPosition < HTML.length - 1) {
                setTimeout(type, tempTypeSpeed);
            }

            updateChatBodyPaddingBottom();
            window.updateChatScroll();

        };

        return {
            type: type
        };
    }


    const pushChatMessage = ($chatBlock, role, message) => {
        if (!$chatBlock) return false;
        if (App.isString($chatBlock))
            $chatBlock = $('#message-block-' + $chatBlock);
        if (!App.isObject($chatBlock))
            return false;
        $chatBlock.append(App.str.eval(htmlTemplates.messageItem, { role, message }));
        let innerContent = $chatBlock.find('.inner-content')[0];
        // var typer = document.getElementById('typewriter');
        typewriter = setupTypewriter(innerContent);
        typewriter.type();
        updateChatBodyPaddingBottom();
        window.updateChatScroll();


    }
    const pushChatMessageList = ($chatBlock, messages) => {
        if (!$chatBlock || !App.isArray(messages)) return false;
        if (App.isString($chatBlock))
            $chatBlock = $('#message-block-' + $chatBlock);
        if (!App.isObject($chatBlock))
            return false;
        messages.map(message => App.isObject(message) && $chatBlock.append(App.str.eval(htmlTemplates.messageItem, { role: message.role, message: message.message })));
        window.updateChatScroll();
    }

    const addReplyWritting = (uuid) => {
        let $chatBlock = $('#message-block-' + uuid);
        $chatBlock.append(App.str.eval(htmlTemplates.replyWritting, { uuid }));
        window.updateChatScroll();
    }

    const removeReplyWritting = (uuid) => {
        let $chatBlock = $('#message-block-' + uuid);
        $chatBlock.find('.reply-writting').remove();
    }

    function sendMessage(data) {
        isSending = true;
        addReplyWritting(data.uuid);
        App.api.post(SEND_URL, data)
            .then(rs => {
                removeReplyWritting(data.uuid);
                if (rs.status) {
                    pushChatMessage(data.uuid, rs.data.role, rs.data.message);

                    if (data.uuid = chatData.uuid) {
                        chatData.id = rs.data.chat_id;
                        chatData.type = 'continue';
                    }
                }
                else {
                    App.Swal.warning(rs.message);
                    newChat();
                }
                isSending = false;
                checkSendingProccess();


            }).catch(e => {
                console.warn(e)
                isSending = false;
                removeReplyWritting(data.uuid);

                App.Swal.warning('Lỗi hệ thống. Vui lòng thử lại sau giay lát');
                newChat();
                checkSendingProccess();
            })
    }

    function checkSendingProccess() {
        if (isSending || pendingList.length == 0) return false;
        // isSending = true;

        let data = pendingList.shift();
        if (data.uuid != chatData.uuid && data.type == 'continue') {
            data.type = 'new';
        }

        sendMessage(data);

    };


    const getCriteriaData = () => {
        let criteria = {};
        let i = 0;
        $criteriaWrapper.find('.inp-criteria').each((n, el) => {
            i++;
            let val = $(el).val();
            if (val != '')
                criteria[$(el).data('name')] = $(el).val();
            // $(el).val('');
        });
        return criteria;
    }
    const cleanCriteriaData = () => $criteriaWrapper.find('.inp-criteria').each((n, el) => $(el).val(''));
    const mergeChatData = data => {
        let criteria = getCriteriaData();

        if (Object.keys(criteria).length == chatData.criteriaTotal) {
            data.criteria = criteria;
        }
        return data;
    }




    const parseMessageContent = (data, replace) => {
        let message = '';
        if (data.prompt_id && data.prompt_name) {
            message = "<h5>" + data.prompt_name + "</h5>\r\n";
        }
        let keys = Object.keys(data.criteria ? data.criteria : {});
        if (keys.length) {
            keys.map(k => {
                message += '<p><strong>' + CRITERIA_LABELS[k] + ': </strong> ' + data.criteria[k] + '</p>' + "\r\n";
            });
            message += "<br>";
        }
        message += data.message;
        return message;
    }


    const createChat = data => {
        const currentData = {
            type: data.type,
            prompt_id: data.prompt_id,
            id: data.id,
            message: data.message,
            name: data.name,
            uuid: data.uuid,
            prompt_name: data.prompt_name,
            criteria: getCriteriaData(),
            message_uuid: App.str.rand(32)
        };

        currentChatData = {
            id: data.id,
            prompt_id: data.prompt_id,
            uuid: data.uuid
        };

        chats[data.uuid] = true;
        // mergeChatData(currentChatData);
        chatStorage[currentChatData.message_uuid] = currentData;

        let $chatBlock = createChatBlock(currentData.uuid, currentData.name);
        pushChatMessage($chatBlock, 'user', parseMessageContent(currentData));
        sendingID = data.uuid;

        pendingList.push(currentData);

        chatData.type = 'continue';
        updateChatBodyPaddingBottom();
        checkSendingProccess();
    }
    const updateChat = data => {
        const currentData = {
            type: data.type,
            prompt_id: data.prompt_id,
            id: data.id,
            message: data.message,
            name: data.name,
            uuid: data.uuid,
            prompt_name: data.prompt_name,
            criteria: getCriteriaData(),
            message_uuid: App.str.rand(32)
        }
        currentChatData = {
            id: data.id,
            prompt_id: data.prompt_id,
            uuid: data.uuid
        };
        // mergeChatData(currentChatData);


        chatStorage[currentChatData.message_uuid] = currentData;

        let $chatBlock = $('#message-block-' + data.uuid);
        pushChatMessage($chatBlock, 'user', parseMessageContent(currentData));
        sendingID = data.uuid;

        pendingList.push(currentData);

        chatData.type = 'continue';
        updateChatBodyPaddingBottom();
        checkSendingProccess();
    };

    const submitForm = (clean) => {
        let message = getMesssageContent();
        let cleanContent = strpTags(message).trim();
        // console.log(cleanContent);
        let criteria = getCriteriaData();

        if ((!cleanContent || cleanContent == '') && (chatData.type != 'new' || (chatData.type == 'new' && chatData.prompt_id == 0))) {
            // console.log('không làm gì');
            if (clean) {
                setMessageContent('');
                updateChatBodyPaddingBottom();
                setTimeout(() => {
                    setMessageContent();

                    updateChatBodyPaddingBottom();
                }, 20);
            }
        }
        else if (chatData.hasCriteria && Object.keys(criteria).length < chatData.criteriaTotal) {
            App.Swal.warning("Vui lòng nhập đầy dủ các tiêu cxhi1");
        }
        else {
            chatData.message = message;
            if (typeof chats[chatData.uuid] == "undefined" || chatData.type != "continue") {
                createChat(chatData);
            } else {
                updateChat(chatData);
            }

            setMessageContent('');
            clearMessageContent();
            if (!chatData.hasCriteria)
                setMessagePlaceholder('Viết gì đó...');
            updateChatBodyPaddingBottom();
            // App.Swal.showLoading();
            // setTimeout(() => App.Swal.error('Lỗi server. Vui lòng thử lại sau giây lát'), 3000);

        }
    }

    let clearAfterKeyUp = false;

    // addElementToCheckHeight($inputMessage);

    $inputMessage.on('keydown', evt => {
        checkMessageInputScroll();
        updateChatBodyPaddingBottom()
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
        updateChatBodyPaddingBottom()
    });

    $form.on('submit', evt => {
        evt.preventDefault();
        submitForm(true);
    });

    $(document).on('click', ".prompt-list-item", function (e) {
        e.preventDefault();
        let prompt_id = $(this).data('id');
        let name = $(this).find('h5').html();
        let placeholder = $(this).find('span.placeholder').html();
        newChatWithPrompt(prompt_id, name, placeholder);
    });

    $messageWrapper.on('click', ".prompt-label a", function (e) {
        e.preventDefault();
        newChat();
        setMessagePlaceholder('Viết gì đó...');

    });

    $messageWrapper.on('click', ".prompt-top-label a", function (e) {
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
