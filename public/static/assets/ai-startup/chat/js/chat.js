$(function () {
    const chatData = {
        type: 'new',
        prompt_id: 0,
        id: null,
        message: ''
    };

    const htmlTemplates = {
        promptItem: '',
        promptLabel: $('#prompt-label-template').html()
    };

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
    const tempElement = document.createElement('div');

    const strpTags = str => {
        tempElement.innerHTML = str;
        // console.log(tempElement);
        const stringWithoutTags = tempElement.textContent || tempElement.innerText || '';
        // console.log(stringWithoutTags);
        const ss = stringWithoutTags.replace(/<[^>]*>/g, '');
        // console.log(ss);
        return ss;
    }


    const $form = $('chat-message-form');
    const $inputMessage = $('#chat-message-input');
    const $messageWrapper = $('.message-wrapper');
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
        let r = 60 / 100 * windowHeight -40;
        if(messageInputHeight > r){
            $inputMessage.addClass('scroll-height');
        }
        else{
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

        return true;
    }

    const newChatWithPrompt = (id, name) => {
        newChat(id);
        $messageWrapper.prepend(App.str.eval(htmlTemplates.promptLabel, { id, name }));
        setMessagePlaceholder('Nhập thêm chi tiết...');
    }



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
            console.log(chatData);

            setMessageContent('');
            clearMessageContent();
            setMessagePlaceholder('Viết gì đó...');
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
        if(clearAfterKeyUp){
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
