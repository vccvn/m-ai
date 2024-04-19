$(function () {
    SH.init();
    const AI_DATA = window.__AI_DATA__;
    const chatData = {
        type: 'new',
        prompt_id: 0,
        prompt_name: '',
        id: null,
        message: '',
        use_criteria: 0,
        chat_name: App.date('H:i', +7),
        name: AI_DATA.data.users.user.name,
        uuid: App.str.rand(32),
        hasCriteria: false,
        criteriaTotal: 0,
        showCriteria: false,
        isFirstChat: true,
    };

    let chats = {};

    const pendingList = [];

    const chatStorage = {};

    const CRITERIA_LABELS = {};

    const htmlTemplates = {
        promptItem: '',
        historyItem: SH.tpl('historyItem'),
        promptTopLabel: SH.tpl('promptTopLabel'),
        promptLabel: SH.tpl('promptLabel'),
        messageBlock: SH.tpl('messageBlock'),
        userMessageItem: SH.tpl('userMessageItem'),
        assistantMessageItem: SH.tpl('assistantMessageItem'),
        replyWritting: SH.tpl('replyWritting'),

        criteriaWrapper: SH.tpl('promptCriteriaInputWrapper'),
        criteriaInput: SH.tpl('promptCriteriaInput'),
        criteriaTextarea: SH.tpl('promptCriteriaTextarea'),
        criteriaToggleButton: SH.tpl('promptTopToggleButton').render(),
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
    const $historyBlock = $('#chat-history-list');
    const SEND_URL = $form.attr('action');
    const INPUT_URL = AI_DATA.urls.inputs;
    const DATA_URL = AI_DATA.urls.data;
    let currentChatData = {
        uuid: chatData.uuid,
        id: chatData.id,
        prompt_id: chatData.prompt_id,
        prompt_name: chatData.prompt_name
    }

    let isSending = false;
    let sendingID = null;
    let isTypingWriter = false;

    const updateChatBodyPaddingBottom = () => {
        $chatBody.css({ paddingBottom: ($chatFooter.height() + 30) + "px" });
        return true;
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

    const getElementTop = element => {

    }

    const strpTags = str => {
        tempElement.innerHTML = str;
        // console.log(tempElement);
        try {
            let elements = tempElement.querySelectorAll('[style]');
            if (elements.length) {
                for (let index = 0; index < elements.length; index++) {
                    const element = elements[index];
                    if (element.style) element.setAttribute("style", "");
                }
            }
        } catch (error) {

        }
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

    const newChat = (prompt_id, chat_id) => {
        chatData.isFirstChat = true;
        let pid = 0;
        if (prompt_id) {
            let n = Number(prompt_id);
            if (n && n > 0 && parseInt(prompt_id) == n) {
                pid = n;
            }
        }
        $messageWrapper.find('.prompt-label').remove();
        chatData.type = 'new';
        chatData.id = chat_id ? chat_id : null;
        chatData.prompt_id = pid;
        chatData.message = '';
        chatData.chat_name = App.date('H:i', +7);

        chatData.uuid = App.str.rand();
        chatData.prompt_name = '';
        chatData.hasCriteria = false;
        chatData.criteriaTotal = 0;
        chatData.showCriteria = false;
        chatData.use_criteria = 0;


        $messageWrapper.removeClass('has-criteria');
        $messageWrapper.removeClass('show-criteria');
        $messageWrapper.removeClass('ot-criteria');

        $criteriaWrapper.html('');
        $messageHeader.html("");
        $criteriaWrapper.empty();
        $messageHeader.empty();
        updateChatBodyPaddingBottom()
        return true;
    }

    const newChatWithPrompt = (id, name, placeholder) => {
        newChat(id);
        chatData.chat_name = name;
        chatData.prompt_name = name;
        App.Swal.showLoading();

        $messageWrapper.removeClass('show-criteria');
        App.api.post(INPUT_URL, { prompt_id: id })
            .then(rs => {
                App.Swal.hideLoading();
                console.log(rs.data);
                if (rs.status && rs.data && rs.data.inputs && rs.data.inputs.length) {
                    chatData.hasCriteria = true;
                    chatData.criteriaTotal = rs.data.inputs.length;
                    chatData.use_criteria = 1;

                    let htmlInputs = rs.data.inputs.map(inp => {
                        CRITERIA_LABELS[inp.name] = inp.label;
                        inp.placeholder = inp.placeholder ? inp.placeholder : 'Nhập ' + inp.label;
                        return htmlTemplates.criteriaWrapper.render(

                            Object.assign(
                                {},
                                inp,
                                {
                                    htmlInput: (inp.type == 'textarea' ? htmlTemplates.criteriaTextarea : htmlTemplates.criteriaInput).render(inp)
                                }
                            )
                        )
                    }).join("") + htmlTemplates.criteriaWrapper.render({ id: 0, htmlInput: '', label: "Message" });
                    $messageHeader.html(
                        htmlTemplates.promptTopLabel.render({
                            name: rs.data.prompt.name,
                            button: htmlTemplates.criteriaToggleButton
                        })
                    );
                    $criteriaWrapper.html(htmlInputs);
                    setMessagePlaceholder(rs.data.prompt.placeholder || placeholder || "Nhập thêm nội dung bạn muốn để AI đưa ra thông tin chính xác hơn")
                    let $ta = $criteriaWrapper.find('textarea');
                    if ($ta.length) {
                        addElementToCheckHeight($ta);
                    }
                    $messageWrapper.addClass('has-criteria');
                    $messageWrapper.addClass('ot-criteria');



                } else {
                    $messageHeader.prepend(htmlTemplates.promptTopLabel.render({
                        id,
                        name,
                        button: ""
                    }));
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
        let chatHtml = htmlTemplates.messageBlock.render({ uuid, name });
        if (prepend) {
            $chatList.prepend(chatHtml);
        } else {
            $chatList.append(chatHtml);
        }
        return $chatList.find('#message-block-' + uuid);
    }

    const messageContentTyper = (uuid, message) => isTypingWriter ? new Typed('#message-box-' + uuid + ' .inner-content .inner-message-content', {
        // stringsElement: '#message-box-' + uuid + ' .hidden-content',
        strings: [message],
        loop: false,
        loopCount: 1,
        showCursor: false,
        onComplete: (self) => {
            // updateChatBodyPaddingBottom()
            window.updateChatScroll('#message-box-' + uuid, 200 )
            $('#message-box-' + uuid + ' .inner-content .typed-cursor--blink').remove()
            console.log(" After complete")},

        /**
         * After each string is typed
         * @param {number} arrayPos
         * @param {Typed} self
         */
        onStringTyped: (arrayPos, self) => {
            // updateChatBodyPaddingBottom();
            window.updateChatScroll();
            console.log(" After each string is typed");
        },

        /**
         * During looping, after last string is typed
         * @param {Typed} self
         */
        onLastStringBackspaced: (self) => {
            // updateChatBodyPaddingBottom();
            window.updateChatScroll();
            console.log(" After last string is typed");
        },


        /**
         * Typing has been stopped
         * @param {number} arrayPos
         * @param {Typed} self
         */
        onTypingPaused: (arrayPos, self) => updateChatBodyPaddingBottom() | window.updateChatScroll('#message-box-' + uuid) | console.log(" After pause"),
    }) : $('#message-box-' + uuid + ' .inner-content .inner-message-content').html(message) | updateChatBodyPaddingBottom() | window.updateChatScroll('#message-box-' + uuid ) ;

    const pushChatMessage = ($chatBlock, role, message, id) => {
        if (!$chatBlock) return false;
        if (App.isString($chatBlock))
            $chatBlock = $('#message-block-' + $chatBlock);
        if (!App.isObject($chatBlock))
            return false;
        let uuid = App.str.rand(16);
        $chatBlock.append(
            htmlTemplates[role + "MessageItem"].render({
                role,
                // message,
                name: role == 'user' ? AI_DATA.data.users.user.name : AI_DATA.data.users.bot.name,
                avatar: role == 'user' ? AI_DATA.data.users.user.avatar : AI_DATA.data.users.bot.avatar,
                id: id ? id : '',
                uuid: uuid
            })
        );
        messageContentTyper(uuid, message);


        // var typed = new Typed('#message-box-' + uuid + ' .inner-content .inner-message-content', {
        //     stringsElement: '#message-box-' + uuid + ' .hidden-content',
        //     loop: false,
        //     loopCount: 1,
        // });


        // updateChatBodyPaddingBottom();
        // window.updateChatScroll();
    }

    const pushChatMessageList = ($chatBlock, messages) => {
        if (!$chatBlock || !App.isArray(messages)) return false;
        if (App.isString($chatBlock))
            $chatBlock = $('#message-block-' + $chatBlock);
        if (!App.isObject($chatBlock))
            return false;
        messages.map(message => {
            let uuid = App.str.rand(16);
            let b = App.isObject(message) && $chatBlock.append(
                htmlTemplates[message.role + "MessageItem"].render({
                    role: message.role,
                    // message: message.message,
                    name: message.role == 'user' ? AI_DATA.data.users.user.name : AI_DATA.data.users.bot.name,
                    avatar: message.role == 'user' ? AI_DATA.data.users.user.avatar : AI_DATA.data.users.bot.avatar,
                    id: message.id || '',
                    uuid: uuid
                })
            );

            messageContentTyper(uuid, message.message);
        });


        updateChatBodyPaddingBottom();
        window.updateChatScroll();
    }


    const checkToggleButton = () => {
        let $button = $('.criteria-toggle-button');
        if (!$button.length)
            return;

        if (chatData.showCriteria) {
            $button.html('<span class="material-icons">arrow_downward</span>');
        } else {
            $button.html('<span class="material-icons">arrow_upward</span>');
        }

        updateChatBodyPaddingBottom();
        window.updateChatScroll();
    }
    const toggleCriteriaBlock = () => {
        if (chatData.showCriteria) {
            if (!chatData.isFirstChat) chatData.use_criteria = 0;
            chatData.showCriteria = false;
            $messageWrapper.removeClass('show-criteria');
        } else {
            chatData.use_criteria = 1;
            chatData.showCriteria = true;
            $messageWrapper.addClass('show-criteria');
        }
        checkToggleButton()

    }
    const setPrompt = (id, name, placeholder, hasCriteria) => {
        let pid = 0;
        if (id) {
            let n = Number(id);
            if (n && n > 0 && parseInt(id) == n) {
                pid = n;
            }
        }
        newChat(id);

        chatData.prompt_id = pid;
        if (name) {
            let button = hasCriteria ? htmlTemplates.criteriaToggleButton : "";
            $messageHeader.prepend(htmlTemplates.promptTopLabel.render({ id, name, button }));
            // setMessagePlaceholder(placeholder ? placeholder : 'Nhập ' + name);
            setMessagePlaceholder(placeholder ? placeholder : "Nhập thêm nội dung bạn muốn để AI đưa ra thông tin chính xác hơn")
        }

        updateChatBodyPaddingBottom();
    };

    const renderInputs = (inputs) => {
        chatData.hasCriteria = true;
        chatData.criteriaTotal = inputs.length;
        chatData.showCriteria = true;
        chatData.use_criteria = 1;
        let htmlInputs = inputs.map(inp => {
            CRITERIA_LABELS[inp.name] = inp.label;
            inp.placeholder = inp.placeholder ? inp.placeholder : 'Nhập ' + inp.label;
            return htmlTemplates.criteriaWrapper.render(
                Object.assign(
                    {},
                    inp,
                    {
                        htmlInput: (inp.type == 'textarea' ? htmlTemplates.criteriaTextarea : htmlTemplates.criteriaInput).render(inp)
                    }
                )
            )
        }).join("")
            + htmlTemplates.criteriaWrapper.render(
                { id: 0, htmlInput: '', label: "Message" }
            );
        $criteriaWrapper.html(htmlInputs);
        let $ta = $criteriaWrapper.find('textarea');
        if ($ta.length) {
            addElementToCheckHeight($ta);
        }
        $messageWrapper.addClass('has-criteria');

        $messageWrapper.addClass('ot-criteria');
    }
    let isWritingTyper = null;
    const addReplyWritting = (uuid) => {
        let $chatBlock = $('#message-block-' + uuid);
        $chatBlock.append(htmlTemplates.replyWritting.render({ uuid, avatar: AI_DATA.data.users.bot.avatar }));
        updateChatBodyPaddingBottom();
        window.updateChatScroll();

        isWritingTyper = new Typed('#reply-writing-' + uuid + ' .chat-profile-name h6 span', {
            strings: [
                'Vui lòng chờ giây lát! ...',
                AI_DATA.data.users.bot.name + ' đang trả lời...',
            ],
            typeSpeed: 20,
            backSpeed: 10,
            /**
              * @property {boolean} loop loop strings
              * @property {number} loopCount amount of loops
              */
            loop: true,
            loopCount: Infinity,

            /**
             * @property {boolean} showCursor show cursor
             * @property {string} cursorChar character for cursor
             * @property {boolean} autoInsertCss insert CSS for cursor and fadeOut into HTML <head>
             */
            showCursor: false,
            cursorChar: '|',
            autoInsertCss: true,

        })

    }

    const removeReplyWritting = (uuid) => {
        if(isTypingWriter){
            // isTypingWriter.destroy();
            if(typeof isTypingWriter.destroy == "function"){
                isTypingWriter.destroy()
            }
            else if(typeof isTypingWriter.stop == "function"){
                isTypingWriter.stop()
            }
        }
        let $chatBlock = $('#message-block-' + uuid);
        $chatBlock.find('.reply-writting').remove();
    }

    function sendMessage(data) {
        if (chatData.isFirstChat) {
            chatData.isFirstChat = false;
            $messageWrapper.removeClass('ot-criteria');
            if (chatData.hasCriteria) {
                toggleCriteriaBlock();
            }
        }
        isSending = true;
        addReplyWritting(data.uuid);
        App.api.post(SEND_URL, data)
            .then(rs => {
                removeReplyWritting(data.uuid);
                if (rs.status) {
                    isTypingWriter = true;
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
        if (data.prompt_id && data.prompt_name && data.use_criteria) {
            message = "<h5>" + data.prompt_name + "</h5>\r\n";
        }
        let keys = Object.keys(data.criteria ? data.criteria : {});
        if (keys.length && data.use_criteria) {
            keys.map(k => {
                message += '<p><strong>' + CRITERIA_LABELS[k] + ': </strong> ' + data.criteria[k] + '</p>' + "\r\n";
            });
            if (data.message && data.message.length) {
                message += "<br>";
                message += "<strong>Thông tin liên quan:</strong><br>"
            }
        }
        message += data.message;
        return message;
    }


    const createChat = data => {
        const currentData = {
            type: data.type,
            prompt_id: data.prompt_id,
            id: data.id,
            role: 'user',
            message: data.message,
            chat_name: data.chat_name,
            name: AI_DATA.data.users.user.name,
            avatar: AI_DATA.data.users.user.avatar,
            uuid: data.uuid,
            prompt_name: data.prompt_name,
            criteria: getCriteriaData(),
            message_uuid: App.str.rand(32),
            use_criteria: data.use_criteria,
            isFirstChat: data.isFirstChat
        };

        currentChatData = {
            id: data.id,
            prompt_id: data.prompt_id,
            uuid: data.uuid
        };

        chats[data.uuid] = true;
        // mergeChatData(currentChatData);
        chatStorage[currentChatData.message_uuid] = currentData;

        isTypingWriter = true;


        let $chatBlock = createChatBlock(currentData.uuid, currentData.chat_name);
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
            role: 'user',
            message: data.message,
            chat_name: data.chat_name,
            name: AI_DATA.data.users.user.name,
            avatar: AI_DATA.data.users.user.avatar,
            uuid: data.uuid,
            prompt_name: data.prompt_name,
            criteria: getCriteriaData(),
            message_uuid: App.str.rand(32),
            use_criteria: data.use_criteria,
            isFirstChat: data.isFirstChat
        }
        currentChatData = {
            id: data.id,
            prompt_id: data.prompt_id,
            uuid: data.uuid
        };
        // mergeChatData(currentChatData);


        chatStorage[currentChatData.message_uuid] = currentData;
        isTypingWriter = true;
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
        let cond = (
            (!cleanContent || cleanContent == '') &&
            (chatData.prompt_id == 0 ||
                (
                    chatData.prompt_id != 0 && chatData.criteriaTotal == 0
                )
            )
        );
        if (chatData.isFirstChat && chatData.hasCriteria) {
            chatData.use_criteria == 1;
        }
        // console.log(cond, chatData, criteria);
        if (cond) {
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
        else if (chatData.hasCriteria && chatData.use_criteria == 1 && Object.keys(criteria).length < chatData.criteriaTotal) {
            App.Swal.warning("Vui lòng nhập đầy dủ các tiêu chí ");
        }
        else {
            chatData.message = App.str.replace(message, '&nbsp;', ' ');
            let data = Object.assign({}, chatData);
            if (data.isFirstChat && chatData.hasCriteria && chatData.criteriaTotal > 0) {
                data.use_criteria = 1;
            }
            if (typeof chats[chatData.uuid] == "undefined" || chatData.type != "continue") {
                createChat(data);
            } else {
                updateChat(data);
            }
            if (chatData.isFirstChat) {
                chatData.isFirstChat = false;


                $messageWrapper.removeClass('ot-criteria');

                if (chatData.hasCriteria) {
                    toggleCriteriaBlock();
                }
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


    const init = () => {
        isTypingWriter = false;
        $messageWrapper.removeClass('ot-criteria');

        $messageWrapper.removeClass('show-criteria');
        chatData.isFirstChat = true;
        const DATA = AI_DATA.data.chats;
        if (!DATA && typeof DATA != "object") {
            return;
        }
        if (DATA.prompt && DATA.prompt.id) {
            setPrompt(DATA.prompt.id, DATA.prompt.name, DATA.prompt.placeholder, DATA.inputs && DATA.inputs.length);
        }
        if (DATA.inputs && DATA.inputs.length) {
            renderInputs(DATA.inputs);
            chatData.criteriaTotal = DATA.inputs.length;

        }
        if (DATA.chat && DATA.chat.id) {
            chatData.id = DATA.chat.id;
            chatData.type = 'continue';
            chats[chatData.uuid] = true;
        }
        $chatList.empty();
        let $chatBlock = createChatBlock(chatData.uuid, DATA.prompt ? DATA.prompt.name : App.date("H:i"));

        if (DATA.messages && DATA.messages.length) {

            pushChatMessageList($chatBlock, DATA.messages);
        }
        window.hideLoader();
    };


    const openChat = data => {
        if (App && App.api) {
            $chatList.empty();
            newChat();
            window.showLoader();
            return App.api.post(DATA_URL, data)
                .then(rs => {
                    if (rs.status) {
                        AI_DATA.data.chats = rs.data;
                        init();
                    } else {
                        window.hideLoader();
                        App.Swal.warbing('Thông tin không hợp lệ');
                    }
                })
                .catch(e => {
                    window.hideLoader();
                    App.Swal.error("Lỗi hệ thống! Vui lòng thử lại");
                })
        }

        return setTimeout(() => {
            openChat(data);
        }, 1000);
    }

    const showTopicHistory = topic_id => {
        App.api.get(AI_DATA.urls.history, { topic_id })
            .then(rs => {
                if (rs.status) {
                    $historyBlock.empty();
                    if (rs.data && rs.data.length)
                        rs.data.map(chat => {
                            chat.chat_name = chat.prompt ? chat.prompt.name : "Chat với M.AI";
                            $historyBlock.append(htmlTemplates.historyItem.render(chat));
                        })

                }
            })
    }

    window.setChatData = (data) => {
        console.log(data);
    }

    window.setPromptChat = (prompt_id, topic_id) => {
        openChat({ prompt_id });
        if (topic_id) {
            showTopicHistory(topic_id);
        }
    }


    let clearAfterKeyUp = false;

    // addElementToCheckHeight($inputMessage);
    $criteriaWrapper.on("keydown", "textarea.inp-criteria", evt => {
        checkMessageInputScroll();
        updateChatBodyPaddingBottom()
        if (evt.keyCode == 13 && !evt.shiftKey) {
            evt.preventDefault();
            submitForm();
            // clearAfterKeyUp = true;
        }
    })
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
        newChat(null, chatData.id);
        setMessagePlaceholder('Viết gì đó...');

    });

    $(document).on('click', '.chat-history-item', function (e) {
        e.preventDefault();
        openChat({ id: $(this).data('id') });
    });

    $(document).on("click", ".criteria-toggle-button", e => {
        e.preventDefault();
        toggleCriteriaBlock();
    });

    init();
    function copyDivToClipboard(element) {
        var range = document.createRange();
        range.selectNode(element);
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
        Toastify({

            text: "Đã sao chép vào clipboard",

            duration: 3000

        }).showToast();

    }
    const ipnElement = document.querySelector('#copyable-block');
    $(document).on("click", ".btn-copy-message", function (e) {
        let $this = $(this);
        e.preventDefault();
        // step 1

        let $chat = $this.closest('.chats');
        if (!$chat || !$chat.length)
            return false;
        copyDivToClipboard($chat.find('.inner-message-content')[0]);
    });

    function exportHTML(content, filename) {
        var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
            "xmlns:w='urn:schemas-microsoft-com:office:word' " +
            "xmlns='http://www.w3.org/TR/REC-html40'>" +
            "<head><meta charset='utf-8'><title>M.AI Word Document</title></head><body>";
        var footer = "</body></html>";
        var sourceHTML = header + content + footer;

        var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
        var fileDownload = document.createElement("a");

        if (!filename) filename = 'document.doc';

        document.body.appendChild(fileDownload);
        fileDownload.href = source;
        fileDownload.download = filename;
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }


    $(document).on("click", ".btn-download-message", function (e) {
        let $this = $(this);
        e.preventDefault();
        // step 1

        let $chat = $this.closest('.chats');
        if (!$chat || !$chat.length)
            return false;
        let $cn = $this.closest('.message-block').find('.chat-line .chat-name')
        let filename = null;
        if ($cn && $cn.length) {
            let name = $cn.html();
            if (name && name.length > 5) {
                filename = name + '.doc';
            }
        }
        exportHTML($chat.find('.inner-message-content').html(), filename);
    });


});
