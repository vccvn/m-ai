$(() => {
    SH.init();

    const $topicNav = $('.topic-nav');
    const $topicChildren = $('.topic-children');
    const $topicPromptBlock = $('.topic-prompts');
    const $topicPromptList = $('.topic-prompt-list');

    const $chatArea = $('.chat-area.chat-iframe');

    const $chatFrameWrapper = $chatArea.find('.chat-frame-wrapper');
    const $chatPreloader = $chatArea.find('.chat-preloader');


    const topicNavItem = SH.tpl('topicNavItem');
    const promptItem = SH.tpl('promptItem');

    const chatUrlTemplate = SH.create(window.__CHAT_URL__);
    // const promptItem = SH.tpl('promptItem');
    const chatFrameTemplate = SH.tpl('chatFrame');
    const topicMapData = {};

    const $topicChildrenHeader = $topicChildren.find('.list-header');
    const $topicChildrenBody = $topicChildren.find('.list-body');

    const $AIPageContentAndFooter = $('.ai-page-content, .navbar-area, .footer-area,.search-overlay')

    function registerTopicItem(topic) {
        if (topic) {
            topicMapData[topic.id] = topic;
            if (topic.children && topic.children.length)
                topic.children.map(t => registerTopicItem(t))
        }
    }

    const registerTopicMAp = () => {
        if (window.__TOPIC_DATA__ && window.__TOPIC_DATA__.length)
            window.__TOPIC_DATA__.map(topic => registerTopicItem(topic));

    }

    const getTopic = id => typeof topicMapData[id] != "undefined" && Object.prototype.hasOwnProperty.call(topicMapData, id) ? topicMapData[id] : null;

    const activeNavItem = id => {
        if (id) {
            let activeItem = $topicNav.find('[data-id="' + id + '"]');
            if (activeItem.length) {
                $topicNav.find('.topic-nav-item').removeClass('active');
                activeItem.addClass('active');
            }
        }
    }

    const renderNav = () => {
        let html = '';
        if (window.__TOPIC_DATA__ && window.__TOPIC_DATA__.length) {
            html = window.__TOPIC_DATA__.map(topic => topicNavItem.render(topic)).join('');
        }
        $topicNav.html(html);
    }

    const cleanChildren = () => {
        $topicChildrenHeader.html('');
        $topicChildrenBody.html('');
        $topicChildrenHeader.empty();
        $topicChildrenBody.empty();
    }
    const cleanTopicPrompt = () => {
        $topicPromptList.html('');
        $topicPromptList.empty();
    }

    const showTopic = id => {
        let topic = getTopic(id);
        if (!$topicChildrenBody.hasClass('d-none')) $topicChildrenBody.addClass('d-none')
        if (!topic) {
            cleanChildren();
            cleanTopicPrompt();
            return;
        }
        $('html, body').scrollTop($(".about-area-two").offset().top);
        activeNavItem(id);
        $topicChildren.hide(100, () => cleanChildren());
        $topicPromptBlock.hide(120, () => cleanTopicPrompt());

        let hasChildren = topic.children && topic.children.length > 0;
        setTimeout(() => {
            $topicChildrenHeader.append(SH.tpl('topicNavBack').render({ id: topic.parent_id }));
            $topicChildrenHeader.append(topicNavItem.render(Object.assign({ className: "active" }, topic)));


            $topicChildren.show(200, () => {
                if (hasChildren) {
                    $topicChildrenBody.removeClass('d-none');
                    let t = 100;
                    topic.children.map(tp => {
                        setTimeout(() => {
                            $topicChildrenBody.append(topicNavItem.render(tp));
                        }, t);
                        t += 100;
                    })
                }
            })
        }, 150);
        if (topic.prompts && topic.prompts.length) {
            $topicPromptBlock.show(200, () => {
                $('html, body').scrollTop($(".topic-children").offset().top - 90);
                let t = 100;
                topic.prompts.map(tp => {
                    setTimeout(() => {
                        $topicPromptList.append(promptItem.render(tp));
                    }, t);
                    t += 100;
                })
            })

        }

    }

    let contentWindow = null;
    let isInited = false;
    const initDefault = (prompt_id, topic_id) => {

        let x = document.getElementById('chatBoxFrame');
        if (!x) {


            let html = chatFrameTemplate.render({ chat_url: chatUrlTemplate.render({ prompt_id: prompt_id ? prompt_id : '', topic_id: '' }) });
            $chatFrameWrapper.empty();
            $chatFrameWrapper.append(html);

            // console.log(html);
            return setTimeout(() => {
                initDefault(prompt_id, topic_id);
            }, 100);
        }

        contentWindow = (x.contentWindow || x.contentDocument);
        isInited = true;
        console.log('inited');
    }

    window.getContentWindow = () => contentWindow;

    const setChatData = (prompt_id) => window.getContentWindow().setPromptChat(prompt_id);

    function openChat(prompt_id, topic_id, turn) {
        $AIPageContentAndFooter.addClass('d-none');
        $chatPreloader.removeClass('d-none');
        console.log("open Chat")

        if (!contentWindow || !isInited) {
            console.log("not init")
            if (!isInited) initDefault();
            if (!turn) turn = 0;
            if (turn > 4) return 0;
            return setTimeout(() => {
                openChat(prompt_id, topic_id, turn + 1);
            }, 500);
        }
        setChatData(prompt_id);
        // $chatFrameWrapper.append(chatFrameTemplate.render({ chat_url: chatUrlTemplate.render({ prompt_id, topic_id }) }))

        $chatArea.addClass('open');
        setTimeout(() => {
            $chatPreloader.addClass('d-none');

        }, 500);
    }
    const closeChat = () => {
        $chatArea.removeClass('open');
        $AIPageContentAndFooter.removeClass('d-none');
        // $chatFrameWrapper.html('');
        // $chatFrameWrapper.empty()
        $('html, body').scrollTop($(".topic-children").offset().top - 90);
    }


    registerTopicMAp();
    renderNav();
    // initDefault();
    $(window).on('load', e => {
        console.log("loaded")
        initDefault()
    });
    setTimeout(() => {
        if(!isInited) initDefault();
    }, 2000);

    $(document).on('click', ".topic-page .topic-nav-item", function (e) {
        e.preventDefault();
        showTopic($(this).data('id'));
    })
    $(document).on('click', ".prompt-item", function (e) {
        e.preventDefault();
        openChat($(this).data('id'), $(this).data('topoc-id'));
    })
    $(document).on('click', ".chat-btn-back", function (e) {
        e.preventDefault();
        closeChat();
    })

})
