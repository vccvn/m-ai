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

    const spinLoader = SH.tpl('SpinLoader');


    const PaginationBlock = SH.tpl('PaginationBlock');
    const PaginationFirstButton = SH.tpl('PaginationFirstButton');
    const PaginationLastButton = SH.tpl('PaginationLastButton');
    const PaginationLinkButton = SH.tpl('PaginationLinkButton');
    const PaginationActiveButton = SH.tpl('PaginationActiveButton');
    const PaginationDotButton = SH.tpl('PaginationDotButton');
    const AlertBlock = SH.tpl('AlertBlock');







    const topicMapData = {};

    const $topicChildrenHeader = $topicChildren.find('.list-header');
    const $topicChildrenBody = $topicChildren.find('.list-body');

    const $AIPageContentAndFooter = $('.ai-page-content, .navbar-area, .footer-area,.search-overlay')
    const $navWrapper = $('.nav-wrapper');

    const $toolSearchForm = $('#tool-search-form');
    const $inputSearch = $('#tool-search-input');
    const $inputTopic = $('input#topic_id');

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

    let currentTopicId = '';
    const showTopic = id => {
        currentKeyword = id;
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
                    let t = 20;
                    topic.children.map(tp => {
                        setTimeout(() => {
                            $topicChildrenBody.append(topicNavItem.render(tp));
                        }, t);
                        t += 20;
                    })
                }
            })
        }, 150);
        if (topic.prompts && topic.prompts.length) {
            $topicPromptBlock.show(200, () => {
                $('html, body').scrollTop($(".topic-children").offset().top - 90);
                let t = 20;
                topic.prompts.map(tp => {
                    setTimeout(() => {
                        $topicPromptList.append(promptItem.render(tp));
                    }, t);
                    t += 20;
                })
            })

        }

    }

    const renderPaginationLinks = links => {
        let buttons = '';
        console.log(links);
        if (links && links.length) {
            let arrButtons = [];
            for (let index = 0; index < links.length; index++) {
                const btn = links[index];
                const btnTemplate = index == 0 ? PaginationFirstButton : (
                    index == links.length - 1 ? PaginationLastButton : (
                        btn.active ? PaginationActiveButton : (
                            btn.label == '...' ? PaginationDotButton : (
                                PaginationLinkButton
                            )
                        )
                    )
                );
                arrButtons.push(btnTemplate.render({
                    url: btn.url ? (location.protocol === 'https:' ? App.str.replace(btn.url, 'http://', 'https://'):btn.url) : '#',
                    label: btn.label
                }))

            }
            buttons = arrButtons.join("");
        }

        $topicPromptList.append(PaginationBlock.render({ buttons: buttons }));
    }

    let currentKeyword = '';
    let listViewMode = 'topic';
    const getPromptData = (url, data) => listViewMode == 'search' && App.api.get(url, data ? data : {})
        .then(rs => {
            if(listViewMode != 'search')
                return;
            $topicPromptList.html("");
            if (rs.status) {
                if (rs.data && rs.data.length) {
                    rs.data.map(tp => $topicPromptList.append(promptItem.render(tp)))
                    if (rs.links && rs.links.length) {
                        renderPaginationLinks(rs.links);
                    }
                } else {
                    $topicPromptList.append(AlertBlock.render({ type: "warning", message: "Không tìm thấy kết quả phù hợp" }));
                }

            } else {
                App.Swal.warning('Lỗi không xác định')
            }
        })
        .catch(e => {
            $topicPromptList.html("");
            console.warn(e);
            App.Swal.warning('Lỗi không xác định')
        })

    let currentSearchTopicID = null;

    const searchPrompt = keyword => {
        let topic_id = $inputTopic.val();
        if (!keyword || (keyword == currentKeyword && topic_id == currentSearchTopicID)) {
            if (!keyword || keyword == '') {
                $navWrapper.removeClass('d-none');
                listViewMode = 'topic';
                $topicPromptList.html('');
                showTopic(currentTopicId);

            }
            return false;
        }

        currentSearchTopicID = topic_id;
        listViewMode = 'search';
        if (!$navWrapper.hasClass('d-none')) {
            $navWrapper.addClass('d-none');
        }
        $topicPromptBlock.show();
        $topicPromptList.html(spinLoader.render({ text: "Đang tìm kiếm..." }));

        currentKeyword = keyword;
        let searchData = { search: keyword };
        if (topic_id && topic_id != '0') {
            searchData.topic_id = topic_id;
        }
        return getPromptData(window.__TOOL_SEARCH__, searchData);
    }

    const checkExpired = () => {
        if (window.__EXPIRED_AT__ != "" && window.__EXPIRED_AT__.length) {
            return true;
        }
        App.Swal.warning('BẠn đã hết thời hạn sử dụng dịch vụ hoặc chưa đăng ký gói AI nào!', null, () => {
            top.location.href = window.__PAY_OPTION_URL__;
        })
        return false;
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

    const setChatData = (prompt_id, topic_id) => window.getContentWindow().setPromptChat(prompt_id, topic_id);

    function openChat(prompt_id, topic_id, turn) {
        if (!checkExpired())
            return false;
        $AIPageContentAndFooter.addClass('d-none');
        $chatPreloader.removeClass('d-none');

        if (!contentWindow || !isInited) {
            if (!isInited) initDefault();
            if (!turn) turn = 0;
            if (turn > 4) return 0;
            return setTimeout(() => {
                openChat(prompt_id, topic_id, turn + 1);
            }, 500);
        }
        setChatData(prompt_id, topic_id);
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
        initDefault()
    });
    setTimeout(() => {
        if (!isInited) initDefault();
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
    });

    $toolSearchForm.on('submit', e => {
        e.preventDefault();
        let keyword = $inputSearch.val();
        // if (keyword != '')
        searchPrompt(keyword);
    })

    let inputTimer;
    $inputSearch.on('input change keyup', function (e) {
        clearTimeout(inputTimer);
        inputTimer = setTimeout(function () {
            searchPrompt($inputSearch.val())
        }, 500)
    })
    let currentUrl = ''
    $(document).on('click', ".pagination-area .page-numbers", function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        if (url && url.length && url != '#' && url != currentUrl) {
            currentUrl = url;
            $topicPromptBlock.show();
            $topicPromptList.html(spinLoader.render({ text: "Đang tải..." }));
            getPromptData(url);
        }
    })
})
