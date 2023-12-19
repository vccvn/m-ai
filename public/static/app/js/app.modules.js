// thiết lập truy vấn bàng jquery
// nếu cco1 sẽ dùng jquery, không có cũng ko sao chỉ là ưu tiên jquery

(function (_) {
    var __delay = function () {
        App.setSystemOption({
            queryEngine: "jQuery"
        });
        App.task(function ($) {
            var csrf = $('meta[name="csrf-token"]').attr('content');
            if ('jQuery' in window) {
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });
            }
            // css class prefix
            var prefixClass = "." + (App.getSystemOption("cssClassPrefix") || "") + "";
            var APIMethods = {
                urls: {},
                init_list: ["urls"],
                url: function (action) {
                    return typeof this.urls[action] != "undefined" ? this.urls[action] : null;
                },


                callApi: function (method, url, data, headers, controller) {
                    if (!headers) headers = {};
                    var apiUrl = this.url(url) || url;
                    // console.log(method, apiUrl, data);
                    var requestData = {
                        method: method,
                        data: data,
                        dataType: "JSON",
                        cookie: true,
                        headers: headers
                    };
                    if (controller) {
                        requestData.signal = controller.signal;
                    }
                    return App.axios(apiUrl, requestData).then(function (response) {
                        var res = response.data;
                        if ((response.status != 200 && response.statusText != "OK") || !res) {
                            console.log(response);
                            throw new Error("Lỗi không xác định");
                        } else {
                            return res;
                        }
                    });
                },
                upload: function (url, data, options) {
                    return App.axiosUpload(url, data, options).then(function (response) {
                        var res = response.data;
                        if ((response.status != 200 && response.statusText != "OK") || !res) {
                            console.log(response);
                            throw new Error("Lỗi không xác định");
                        } else {
                            return res;
                        }
                    });
                }
            };

            ["get", "post", "put", "patch", "delete", "head", "options"].map(function (method) {
                var mt = method.toUpperCase();
                /**
                 * gửi request dạng {mt}
                 * @param {string|option} url url hoặc option
                 * @param {object|null|undefined} data tham số tùy chọn
                 * @param {object|null|undefined} headers tham số tùy chọn
                 */

                APIMethods[method] = function (url, data, headers) {
                    return this.callApi(mt, url, data, headers);
                }
            });

            App.extend({
                api: APIMethods
            });









            if (typeof window.apiInit == "function" || typeof window.custoAapiInit == "function") {
                if (typeof window.apiInit == "function") {
                    window.apiInit();
                }
            }

            App.extend({
                storage: {
                    key: "__storage__object__list__",
                    support: function () {
                        if (typeof (Storage) !== "undefined") {
                            return true;
                        }
                        return true;
                    },
                    /**
                     * @returns {array}
                     */
                    getObjectList: function () {
                        if (!this.support()) return [];
                        var val = localStorage.getItem(this.key);
                        if (val) {
                            return JSON.parse(val);
                        }
                        return [];
                    },
                    addToObjectList: function (key) {
                        if (!this.support()) return this;
                        var objList = this.getObjectList();
                        if (objList.indexOf(key) === -1) {
                            objList.push(key);
                            localStorage.setItem(this.key, JSON.stringify(objList));
                        }
                        return this;
                    },
                    removeInObjectList: function (key) {
                        if (!this.support()) return this;
                        var list = this.getObjectList();
                        var ind = list.indexOf(key);
                        if (ind > -1) {
                            list.splice(ind, 1);
                            localStorage.setItem(this.key, JSON.stringify(list));
                        }
                        return this;
                    },
                    checkObjectList: function (key) {
                        var stt = false;
                        var list = this.getObjectList();
                        return list.indexOf(key) >= 0;
                    },
                    set: function (key, value) {
                        if (!this.support()) return false;
                        if (App.isObject(key)) {
                            for (const k in key) {
                                if (key.hasOwnProperty(k)) {
                                    const v = key[k];
                                    this.set(k, v);
                                }
                            }
                            return this;
                        }
                        if (App.isObject(value)) {
                            value = JSON.stringify(value);
                            this.addToObjectList(key);
                        }
                        localStorage.setItem(key, value);
                        return true;
                    },
                    get: function (key) {
                        if (!this.support()) return null;
                        var val = localStorage.getItem(key);
                        if (this.checkObjectList(key)) {
                            val = JSON.parse(val);
                        }
                        return val;
                    },
                    remove: function (key) {
                        if (!this.support()) return false;
                        this.removeInObjectList(key);
                        localStorage.removeItem(key);
                        return true;
                    }
                }
            });
            var CrazySelect = function (selector, template) {
                this.$el = $(selector);
                this.template = template;
                this.searchUrl = '';
                this.addUrl = '';
                this.advanceHandler = '';
                this.addData = {};
                this.addField = 'name';
                this.searchField = 'search';
                this.keywords = '';
                this.hiddenKeywords = '';
                this.type = 'static';
                this.oldValue = '';
                this.hasDefault = false;
                this.activeValue = '';
                this.activeText = '';
                this.$menu = this.$el.find('.select-option-menu');
                this.$message = this.$menu.find('.message');
                this.$buttons = this.$menu.find('.buttons');
                this.$options = this.$menu.find('.option-list');
                this.$text = this.$el.find('.show-text-value');
                this.$select = this.$el.find('.btn-dropdown-select');
                this.$input = $('#' + this.$el.data('id'));
                this.$searchBlock = this.$el.find('.search-block');
                this.$searchInput = this.$el.find('input');
                this.searchParamSelectors = this.$el.data('search-params') || '';

                this.confirmChange = false;
                var self = this;
                this.init = function () {
                    var $el = this.$el;
                    let selectType = $el.data('select-type') || 'static';
                    this.type = selectType;

                    let cc = $el.data('confirm-change') || "";
                    if (cc && cc != "false") {
                        this.confirmChange = true;
                    }
                    if (selectType == 'static') {
                        let itemCount = $el.find('.option-item').length;
                        let disableSearch = $el.data('disable-search') || null;
                        if (disableSearch && disableSearch != 'false') {
                            this.$searchBlock.hide();
                        }
                        else if (itemCount > 10) {
                            this.$searchBlock.show();

                        } else if (itemCount > 0) {
                            this.$searchBlock.hide();
                        } else {
                            this.$searchBlock.hide();
                            // $el.find('.message').show();
                        }


                    }// end if type == 'static'
                    else if (selectType == 'search') {
                        if ($el.data('search-field')) {
                            this.searchField = $el.data('search-field');
                        }
                        if ($el.data('search-url')) {
                            this.searchUrl = $el.data('search-url');
                        }
                    }
                    else if (selectType == 'dynamic') {
                        if ($el.data('search-field')) {
                            this.searchField = $el.data('search-field');
                        }
                        if ($el.data('search-url')) {
                            this.searchUrl = $el.data('search-url');
                        }
                        this.advanceHandler = $el.data('advance-click') || cl;
                    }
                    // end if selectType == 'search'

                    this.parseMultiLevelText();
                    // tim kiếm
                    $el.children('.select-option-menu').on('keyup mouseup', function (e) {
                        var el = e.target;
                        var tag = el.tagName.toLowerCase();
                        if (tag == 'input' && el.getAttribute('data-name') == 'search_options') {
                            let keywords = el.value;
                            var text = el.value.toLocaleLowerCase();
                            if (text == self.keywords.toLocaleLowerCase()) return;
                            self.keywords = keywords;
                            if (selectType == 'static') {
                                self.staticSearch(text);
                            } else if (selectType == 'search' || selectType == 'dynamic') {
                                self.ajaxSearch(text);
                            }
                        }
                    });


                    $(window).on("scroll", function (e) {
                        self.checkPosRef();
                    });
                    $(document).on('click', function (e) {
                        var $e = $(e.target);
                        if (!$e.closest(self.$el).length) {
                            self.hide();
                        } else {
                            if ($e.is(self.$select) || $e.closest(self.$select).length) {
                                self.toggle();
                            }
                            else if ($e.is('.search-block') || $e.closest('.search-block').length) {
                                // không lam gì cả
                                // có thể làm ngược lại nhưng ko thích
                            }
                            else {
                                let $item = [];
                                $elm = $e.closest('.select-option-item');
                                if ($e.hasClass('select-option-item')) {
                                    $item = $e;
                                } else if ($elm.length) {
                                    $item = $elm;
                                }
                                if ($item.length) {
                                    var value = $item.data('value');
                                    let isl = self.$input;
                                    let old = isl.val();
                                    if (value != old) {
                                        const changeValue = function () {
                                            var text = $item.data('text');
                                            let datadisable = self.$el.data('disable') || null;
                                            if (!datadisable || datadisable == 'false') {
                                                self.$text.html(text);
                                                self.$text.val(value);
                                                isl.val(value);
                                                $el.find('.select-option-menu').find('.option-item').removeClass('active');
                                                $item.addClass('active');
                                                self.parseMultiLevelText();
                                            }
                                            var onc = $el.data('changed-callback') || $el.data('on-change');
                                            if (onc) {
                                                let oncbs = onc.split(',');
                                                if (oncbs.length > 1) {
                                                    oncbs.forEach(element => {
                                                        let func = element.trim();
                                                        if (App.func.check(func)) {
                                                            App.func.call(func, [value, text, e.target]);
                                                        }
                                                    });
                                                }
                                                else if (App.func.check(onc)) {
                                                    App.func.call(onc, [value, text, e.target]);
                                                }
                                            }
                                        };
                                        if (self.confirmChange) {
                                            let message = self.$el.data('confirm-message') || "Thay đổi giá trị trường này có thể gây sáo trộn hoặc mất dữ liệu một số trường khác.>br>Bạn có muốn thực hiện thay đổi?";
                                            App.popup.confirm(message, function (agree) {
                                                if (agree) {
                                                    changeValue();
                                                    self.hide();
                                                }
                                            })
                                        } else {
                                            changeValue();
                                            self.hide();
                                        }
                                    } else {
                                        self.hide();
                                    }
                                }
                            }
                        }
                    });

                    // this.$el.on('click', '.btn-dropdown-select', function (e) {
                    //     self.toggle();
                    // });
                };

                this.show = function () {
                    if (!this.$el.hasClass('show')) {
                        this.$el.addClass('show');
                    }
                    this.checkPosRef();
                    return this;
                }
                this.hide = function () {
                    this.$el.removeClass('show');
                    return this;
                };
                this.toggle = function () {
                    if (!this.$el.hasClass('show')) {
                        this.show();
                    } else {
                        this.$el.removeClass('show');
                    }
                    return this;
                };

                this.checkPosRef = function () {
                    // console.log()
                    var top = $(document).scrollTop();
                    var osTop = self.$el.offset().top;
                    var e = self.$el[0];
                    var b = e.getBoundingClientRect();
                    var winHeight = window.innerHeight;
                    var outScreen = top + winHeight;

                    var optHeight = self.$el.find('.select-option-menu').height();

                    if (b.y + b.height + optHeight >= winHeight) {
                        if (!self.$el.hasClass('dis-above')) self.$el.addClass('dis-above')
                    } else {
                        self.$el.removeClass('dis-above')
                    }

                };


                /**
                 * tim kiem trong danh saxh
                 * @param {string} keyword Từ khóa tim kiếm
                 * @return {void}
                 */
                this.staticSearch = function (keyword) {
                    var cc = 0;
                    var $options = self.$el.children('.select-option-menu');
                    var k = App.str.clearUnicode(keyword);
                    keyword = k.toLocaleLowerCase();
                    var kws = keyword.split(',');
                    var keywords = [];

                    kws.map(function (kw, ind) {
                        let k = kw.trim();
                        if (k !== '' && k !== ' ') {
                            keywords.push(k);
                        }
                    });
                    var findText = function (text, find) {
                        var t = App.str.clearUnicode(String(text)).toLowerCase();
                        var rs = t.split(find).length > 1;
                        console.log('tim', find, 'trong', t);
                        console.log('kq', rs);

                        return rs;
                    };
                    $options.find('.option-list .option-item').removeClass('found').removeClass('hidden-item');
                    $options.find('.option-list>.option-item').each(function (ind, elem) {
                        if (keywords.length) {
                            let isSearch = false;
                            keywords.forEach(text => {
                                if (!isSearch) {
                                    if (findText($(elem).data('text'), text) || findText($(elem).html(), text) || findText($(elem).data('value'), text)) {
                                        $(elem).show();
                                        $(elem).addClass('found');
                                        cc++;
                                        isSearch = true;
                                    } else {
                                        $(elem).hide();
                                    }
                                }
                            });

                            if (!isSearch) {
                                $(elem).addClass('hidden-item');
                            }
                        }
                        else if (findText($(elem).data('text'), keyword) || findText($(elem).html(), keyword) || findText($(elem).data('value'), keyword)) {
                            $(elem).show();
                            $(elem).addClass('found');
                            cc++;
                        } else {
                            $(elem).hide();
                            $(elem).addClass('hidden-item');
                        }
                    });

                    var checkGroup = function (group, callback) {
                        var s = false;
                        var dropdownHeader = $(group).children('.select-option-header');
                        if (dropdownHeader.length) {
                            if (keywords.length) {
                                let isSearch = false;
                                keywords.forEach(text => {
                                    if (!isSearch) {
                                        if (findText($(dropdownHeader[0]).data('text'), text) || findText($(dropdownHeader[0]).html(), text) || findText($(dropdownHeader[0]).data('value'), text)) {
                                            $(group).find('.option-group').show();
                                            $(group).find('.option-item').show();
                                            $(group).find('.option-item').addClass('found');
                                            isSearch = true;
                                            s = true;
                                        }
                                    }
                                });
                            }
                            else if (findText($(dropdownHeader[0]).data('text'), keyword) || findText($(dropdownHeader[0]).html(), keyword) || findText($(dropdownHeader[0]).data('value'), keyword)) {
                                s = true;
                                $(group).find('.option-group').show();
                                $(group).find('.option-item').show();
                                $(group).find('.option-item').addClass('found');
                            }
                        }
                        if (!s) {
                            $(group).children().each(function (ind, elm) {
                                if ($(elm).hasClass('option-item')) {
                                    if (keywords.length) {
                                        let isSearch = false;
                                        keywords.forEach(text => {
                                            if (!isSearch) {
                                                if (findText($(elem).data('text'), text) || findText($(elem).html(), text) || findText($(elem).data('value'), text)) {
                                                    $(elm).show();
                                                    $(elm).addClass('found');
                                                    isSearch = true;
                                                    s = true;
                                                }
                                            }
                                        });
                                        if (!isSearch) {
                                            $(elm).hide();
                                            $(elm).addClass('hidden-item');
                                        }
                                    }
                                    else if (findText($(elem).data('text'), keyword) || findText($(elem).html(), keyword) || findText($(elem).data('value'), keyword)) {
                                        s = true;
                                        $(elm).show();
                                        $(elm).addClass('found');
                                    } else {
                                        $(elm).hide();
                                        $(elm).addClass('hidden-item');
                                    }

                                } else if ($(elm).hasClass('option-group')) {
                                    var a = callback(elm, callback);
                                    if (a) s = a;
                                }
                            });

                        }
                        if (s) {
                            $(group).show();
                            dropdownHeader.show();
                            if (dropdownHeader.hasClass('option-item')) {
                                dropdownHeader.addClass('found');
                            }
                        } else {
                            $(group).hide();
                            dropdownHeader.hide();
                            if (dropdownHeader.hasClass('option-item')) {
                                dropdownHeader.addClass('hidden-item');
                            }
                        }
                        return s;
                    };

                    $options.find('.option-list>.option-group').each(function (index, elem) {
                        if (checkGroup(elem, checkGroup)) cc++;
                    });

                    if (!cc) {
                        self.hideLoader('Không có kết quả');
                    } else {
                        $options.find('.message').hide();
                    }
                };
                /**
                 * tim kiem bang ajax
                 * @param {string} keyword Từ khóa tim kiếm
                 * @return {void}
                 */
                this.ajaxSearch = function (keyword, callback) {
                    var data = {};
                    data[this.searchField] = keyword;
                    var $el = self.$el;
                    if (self.searchParamSelectors && $(self.searchParamSelectors).length) {
                        $(self.searchParamSelectors).each(function (i, ele) {
                            let $e = $(ele);
                            if ($e.attr('name')) {
                                data[$e.attr('name')] = $e.val();
                            }
                        });
                    }
                    self.showLoader('Đang tìm kiếm...');
                    self.$options.hide();
                    self.$buttons.hide();

                    App.ajax(this.searchUrl, {
                        type: "GET",
                        dataType: "json",
                        data: data,
                        cookie: true
                    }).then(function (rs) {
                        if (rs.status) {
                            if (rs.data) {
                                self.hideLoader();
                                // do something
                                self.$options.html(self.getSelectOptions(rs.data));
                                if (self.$options.children().length) {
                                    self.$message.hide();
                                    self.$options.show();
                                }
                                if (!self.has(keyword) && keyword.length) {
                                    if (self.type == 'dynamic') {
                                        self.$buttons.show();
                                    }
                                }
                                if (typeof callback == "function") {
                                    callback();
                                }
                            } else {
                                self.hideLoader('Không có kết quả');
                                self.$options.hide();
                                if (self.type == 'dynamic') {
                                    self.$buttons.show();
                                }
                            }
                        } else {
                            self.hideLoader('Không có kết quả');
                            self.$options.hide();
                            if (self.type == 'dynamic') {
                                self.$buttons.show();
                            }
                        }
                    }).catch(function (e) {
                        self.hideLoader('Lỗi không xác định!');
                        self.$options.hide();
                    });

                };


                /**
                 * tim kiem trong danh saxh
                 * @param {string} keyword Từ khóa tim kiếm
                 * @return {void}
                 */
                this.has = function (keyword) {
                    keyword += "";
                    var cc = 0;
                    var $options = self.$el.children('.select-option-menu');
                    keyword = App.str.clearUnicode(keyword).toLowerCase();
                    var kws = keyword.split(',');
                    var keywords = [];
                    kws.map(function (kw, ind) {
                        let k = kw.trim();
                        if (k !== '' && k !== ' ') {
                            keywords.push(k);
                        }
                    });
                    $options.find('.option-list>.option-item').each(function (ind, elem) {
                        if (keywords.length) {
                            let isSearch = false;
                            keywords.forEach(text => {
                                if (!isSearch) {
                                    if (App.str.clearUnicode($(elem).data('text') + "").toLowerCase() == text) {
                                        cc++;
                                        isSearch = true;
                                    }
                                }
                            });
                        }
                        else if (App.str.clearUnicode($(elem).data('text') + "").toLowerCase() == keyword) {
                            cc++;
                        }
                    });

                    var checkGroup = function (group, callback) {
                        var s = false;
                        var $selectHeader = $(group).children('.select-option-header');
                        if ($selectHeader.length) {
                            if (keywords.length) {
                                let isSearch = false;
                                keywords.forEach(text => {
                                    if (!isSearch) {
                                        if (App.str.clearUnicode($($selectHeader[0]).data('text') + "").toLowerCase() == text) {
                                            isSearch = true;
                                            s = true;
                                        }
                                    }
                                });
                            }
                            else if (App.str.clearUnicode($($selectHeader[0]).data('text') + "").toLowerCase() == keyword) {
                                s = true;
                            }
                        }
                        if (!s) {
                            $(group).children().each(function (ind, elm) {
                                if ($(elm).hasClass('option-item')) {
                                    if (keywords.length) {
                                        let isSearch = false;
                                        keywords.forEach(text => {
                                            if (!isSearch) {
                                                if (App.str.clearUnicode($(elm).data('text') + "").toLowerCase() == text) {
                                                    isSearch = true;
                                                    s = true;
                                                }
                                            }
                                        });
                                    }
                                    else if (App.str.clearUnicode($(elm).data('text') + "").toLowerCase() == keyword) {
                                        s = true;
                                    }

                                } else if ($(elm).hasClass('option-group')) {
                                    var a = callback(elm, callback);
                                    if (a) s = a;
                                }
                            });

                        }
                        return s;
                    };

                    $options.find('.option-list>.option-group').each(function (index, elem) {
                        if (checkGroup(elem, checkGroup)) cc++;
                    });

                    return cc;
                };


                /**
                 * kich hoat option
                 * @param {string} value giá trị
                 * @return {void}
                 */
                this.active = function (value) {
                    var cc = 0;
                    var $options = self.$el.children('.select-option-menu');
                    value = App.str.clearUnicode("" + value).toLowerCase();
                    $options.find('.option-list>.option-item').each(function (ind, elem) {
                        if (App.str.clearUnicode($(elem).data('value') + "").toLowerCase() == value) {
                            self.deactive();
                            self.changeText($(elem).data('text'));
                            self.changeValue($(elem).data('value'));
                            $(elem).addClass('active');
                            cc++;
                        }
                    });
                    if (cc) {
                        this.parseMultiLevelText();
                    }
                    if (cc) return true;

                    var checkGroup = function (group, callback) {
                        var s = false;
                        $(group).children().each(function (ind, elm) {
                            if ($(elm).hasClass('option-item')) {
                                if (App.str.clearUnicode($(elm).data('value') + "").toLowerCase() == value) {
                                    self.deactive();
                                    self.changeText($(elm).data('text'));
                                    self.changeValue($(elm).data('value'));
                                    $(elm).addClass('active');
                                    s = true;
                                }

                            } else if ($(elm).hasClass('option-group')) {
                                var a = callback(elm, callback);
                                if (a) s = a;
                            }
                        });
                        return s;
                    };
                    $options.find('.option-list>.option-group').each(function (index, elem) {
                        if (checkGroup(elem, checkGroup)) cc++;
                    });
                    if (cc) {
                        this.parseMultiLevelText();
                    }

                    return cc;
                };


                this.disableSearch = function () {
                    this.$searchBlock.hide();
                }
                this.enableSearch = function () {
                    this.$searchBlock.show();
                }

                this.showLoader = function (message) {
                    self.$message.addClass('m-loader m-loader--success m-loader--right').show();
                    if (message) {
                        self.$message.html(message);
                    }
                };

                this.hideLoader = function (message) {
                    self.$message.removeClass('m-loader m-loader--success m-loader--right').hide();
                    if (message) {
                        self.$message.html(message);
                        self.$message.show();
                    }
                }

                this.getSelectOptions = function (options, defaultVal, html, level, template) {
                    var otype = App.getType(options);
                    if (otype != 'object' && otype != 'array') return '';
                    if (!html) html = '';
                    var group_header = self.getOptionLabelType();
                    if (!template) template = '<a href="javascript:void(0);" data-value="{$value}" data-text="{$hetext}" class="select-option-item option-item {$class} {$active}">{$text}</a>';
                    //die($group_header);
                    var df = defaultVal || null;

                    for (const key in options) {
                        let val = options[key];
                        let t = App.getType(val);
                        if (['object', 'array'].indexOf(t) >= 0) {
                            let opt = val;
                            let lbl = key;

                            if (t == 'object' && (val.hasOwnProperty('label') || val.hasOwnProperty('text')) && (val.hasOwnProperty('data') || val.hasOwnProperty('list'))) {
                                lbl = val.hasOwnProperty('label') ? val.label : val.text;
                                let isset = false;
                                if (val.hasOwnProperty('data')) {
                                    if (['array', 'object'].indexOf(App.getType(val.data))) {
                                        opt = val.data;
                                        isset = true;
                                    }
                                }
                                if (!isset && val.hasOwnProperty('list')) {
                                    if (['array', 'object'].indexOf(App.getType(val.list))) {
                                        opt = val.data;
                                        isset = true;
                                    }
                                }
                            }


                            html += '<div class="option-group">';
                            if (key == df) {
                                self.changeText(lbl);
                                self.hasDefault = true;
                                self.activeText = lbl;
                                self.activeValue = key;
                            }
                            if (group_header != 'value') {
                                html += "<h6 class=\"select-option-header\">" + lbl + "</h6>";
                            } else {
                                html += App.str.eval(template, {
                                    value: key,
                                    text: App.str.htmlentities(lbl),
                                    hetext: App.str.htmlentities(lbl),
                                    active: (df == key) ? 'active' : '',
                                    class: "select-option-header option-header"
                                });

                            }
                            html = self.getSelectOptions(opt, df, html, level + 1, template);

                            html += '</div>';

                        }
                        else {
                            if (key == df) {
                                self.changeText(val);
                                self.hasDefault = true;
                                self.activeText = val;
                                self.activeValue = key;
                            }

                            html += App.str.eval(template, {
                                value: key,
                                text: App.str.htmlentities(val),
                                hetext: App.str.htmlentities(val),
                                active: (df == key) ? 'active' : '',
                                class: ""
                            });


                        }
                    }
                    return html;

                };

                this.getOptionLabelType = function () {
                    var group_label = 'header';
                    var type = self.$el.data('label-type') || null;
                    if (type && ['header', 'value', 'label'].indexOf(type) > -1) {
                        group_label = type;
                    }
                    return group_label;
                };


                this.parseMultiLevelText = function () {
                    var $options = self.$el.children('.select-option-menu');
                    var maxLevel = -1;
                    var data = {};
                    var items = $options.find('.option-list>.option-item.active');
                    if (items.length) {
                        data[0] = $(items[0]).html();
                        maxLevel = 0;
                    } else {
                        var checkGroup = function (group, callback, level) {
                            var s = false;
                            var $selectHeader = $(group).children('.select-option-header');
                            if ($selectHeader.length) {
                                if ($($selectHeader[0]).hasClass('active')) {
                                    data[level] = $($selectHeader[0]).html();
                                    if (level > maxLevel) maxLevel = level;
                                    s = true;
                                }
                            }
                            if (!s) {
                                $(group).children().each(function (ind, elm) {
                                    if ($(elm).hasClass('option-item')) {
                                        if ($(elm).hasClass('active')) {
                                            data[level + 1] = $(elm).html();
                                            s = true;
                                            if (level + 1 > maxLevel) maxLevel = level + 1;
                                        }
                                    } else if ($(elm).hasClass('option-group')) {
                                        var a = callback(elm, callback, level + 1);
                                        if (a) s = a;
                                    }
                                });
                            }
                            if (s) {
                                data[level] = $selectHeader.html();
                                if (level > maxLevel) maxLevel = level;
                            }
                            return s;
                        };

                        $options.find('.option-list>.option-group').each(function (index, elem) {
                            var $header = $(elem).children('.select-option-header');
                            if (checkGroup(elem, checkGroup, 0)) {
                                data[0] = $header.html();
                            }
                        });

                    }
                    var dataText = [];
                    if (maxLevel >= 0) {
                        for (let i = 0; i <= maxLevel; i++) {
                            dataText[i] = data[i];
                        }
                        self.changeText(dataText.join(" / "));
                    }
                };

                this.changeText = function (text) {
                    if (text) {
                        this.$text.html(text);
                        this.$text.val(text);

                    }
                };

                this.changeValue = function (value) {
                    var val = (typeof value != "undefined") ? value : '';
                    this.$input.val(val);
                }



                this.changeOptions = function (options) {
                    self.changeText("Chọn một");
                    let currentValue = self.$input.val();
                    self.hasDefault = false;
                    self.activeValue = '';
                    self.activeText = "Chọn một";
                    self.oldValue = self.$input.val();
                    self.$input.val('');
                    self.$options.html(self.getSelectOptions(options, self.oldValue));
                    if (self.hasDefault) {
                        self.$input.val(self.activeValue);
                        self.changeText(self.activeText);
                    }
                    self.hasDefault = false;

                    self.showSearchBlock();
                    this.parseMultiLevelText();
                };

                this.addOption = function (value, text) {
                    var type = App.getType(value);
                    var options = {};
                    if (type == 'object' || type == 'array') {
                        this.deactive();
                        self.$options.append(self.getSelectOptions(value, self.oldValue));
                    } else {
                        options[value] = text;
                        this.deactive();
                        self.$options.append(self.getSelectOptions(options, value));
                        self.changeValue(value);
                        self.changeText(text);

                    }

                    self.$options.show();
                    self.$buttons.hide();
                    self.$message.hide();

                };

                this.deactive = function () {
                    this.$el.find('.select-option-menu').find('.option-item').removeClass('active');
                };


                this.showSearchBlock = function () {
                    if (self.type == 'static') {
                        let itemCount = self.$el.find('.option-item').length;
                        if (itemCount > 10) {
                            self.$el.find('.search-block').show();

                        } else if (itemCount > 0) {
                            self.$el.find('.search-block').hide();
                        } else {
                            self.$el.find('.search-block').hide();
                            // self.$el.find('.message').show();
                        }


                    }// end if type == 'static'
                };
                this.resetDefault = function () {
                    let activeItem = this.$options.find('.option-item.active');
                    cl(activeItem)
                    if (activeItem.length && !activeItem.hasClass('hidden-item')) {
                        this.changeValue(activeItem.data('value'));
                        this.changeText(activeItem.data('text'));

                        return true;
                    }
                    let foundItem = this.$options.find('.option-item');
                    foundItem.removeClass('active');
                    let status = false;
                    if (foundItem.length) {
                        for (let index = 0; index < foundItem.length; index++) {
                            const element = foundItem[index];
                            if (!$(element).hasClass('hidden-item')) {
                                this.changeValue($(element).data('value'));
                                this.changeText($(element).data('text'));
                                status = true;
                                break;
                            }

                        }
                    }
                    if (!status) {
                        this.changeValue('');
                        this.changeText('-- Chọn một --');

                    }
                };

                this.val = function () {
                    return self.$input.val();
                };

            };

            App.dom.select = {
                list: {},
                add: function (selector) {
                    var $el = $(selector);
                    if ($el.length) {
                        let $select = new CrazySelect($el[0]);
                        $select.init();
                        this.list[$el.data('id')] = $select;
                    }
                },
                /**
                 * lấy thẻ select
                 * @param {string} id
                 *
                 * @return {CrazySelect}
                 */
                getTag: function (id) {
                    if (id) {
                        if (typeof this.list[id] != "undefined") {
                            return this.list[id];
                        }
                    }
                    return null;
                },
                changeOptions: function (id, options) {
                    if (typeof this.list[id] != "undefined") {
                        this.list[id].changeOptions(options);
                    }
                },
                addOption: function (id, value, text) {
                    if (typeof this.list[id] != "undefined") {
                        this.list[id].addOption(value, text);
                    }
                },
                ajaxSearch: function (id, keywords, callback) {
                    if (typeof this.list[id] != "undefined") {
                        this.list[id].ajaxSearch(keywords, callback);
                    }
                },
                staticSearch: function (id, keywords) {
                    if (typeof this.list[id] != "undefined") {
                        this.list[id].staticSearch(keywords);
                    }
                },
                deactive: function (id) {
                    if (typeof this.list[id] != "undefined") {
                        this.list[id].deactive();
                    }
                },
                active: function (id, value) {
                    if (typeof this.list[id] != "undefined") {
                        this.list[id].active(value);
                    }
                },
                getValue: function (id, defaultValue) {
                    var val = !defaultValue ? null : defaultValue;
                    if (typeof this.list[id] != "undefined") {
                        val = this.list[id].val();
                    }
                    return val;
                }
            };

            var $cs = $('.crazy-select');
            if ($cs.length) {
                $cs.each(function (i, el) {
                    App.dom.select.add(el);
                });
            }
            App.extend({
                popup: {
                    alert: function (message, showCallback, hideCallback) {
                        Swal.fire({
                            title: "Thông báo",
                            html: message,
                            icon: 'info',
                            type: 'info',
                            width: 500,
                            animation: false,
                            customClass: {
                                popup: 'animated tada'
                            }
                        })
                    },
                    warning: function (message, showCallback, hideCallback) {
                        Swal.fire({
                            icon: 'warning',
                            type: 'warning',
                            title: "Cảnh báo",
                            width: 500,
                            html: message,
                            animation: false,
                            customClass: {
                                popup: 'animated tada'
                            }
                        })
                    },
                    error: function (message, showCallback, hideCallback) {
                        Swal.fire({
                            icon: 'error',
                            title: "Ối! Lỗi rồi!...",
                            html: message,
                            animation: false,
                            width: 500,
                            customClass: {
                                popup: 'animated tada'
                            }
                        })
                    },
                    success: function (message, showCallback, hideCallback) {
                        Swal.fire({
                            icon: 'success',
                            title: "Thành công!",
                            html: message,
                            animation: false,
                            width: 500,
                            customClass: {
                                popup: 'animated tada'
                            }
                        })
                    },
                    show: function (message, options) {
                        Swal.fire({
                            title: message,
                            width: 500,
                            animation: false,
                            customClass: {
                                popup: 'animated tada'
                            }
                        })
                    },
                    confirm: function (question, callback) {
                        Swal.fire({
                            title: 'Xác nhận',
                            text: question,
                            // type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Không',
                            confirmButtonText: 'Có',
                            animation: false,
                            customClass: {
                                popup: 'animated tada'
                            },
                            width: 500,
                        }).then((result) => {
                            if (result.value) {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            }
                        })
                    }
                },
                Swal: {
                    /**
                    * Hiển thị thông báo
                    * @param {string} message Nội dung thông báo
                    * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                    * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                    */
                    alert: function (message, callback, hideCallback) {
                        var msg = '';
                        var title = 'Thông báo';

                        var icon = 'info';
                        var tyoe = App.getType(message);
                        if (tyoe == 'string') {
                            msg = message;
                        }
                        else if (type == 'array') {
                            msg = message.map(function (m) {
                                return App.str.replace(m, ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }
                        else if (type == 'object') {
                            msg = message.content ? message.content : (message.message ? message.message : (message.html ? message.html : ""));
                            if (message.title) title = message.title;
                            if (Object.hasOwnProperty.call(message, 'icon')) icon = message.icon;
                            else if (Object.hasOwnProperty.call(message, 'type')) icon = message.tyoe;
                        }
                        msg = App.str.replace(msg, ['[nl]', "\r\n", "\n"], '<br />');

                        Swal.fire({
                            title: title,
                            html: msg,
                            type: icon,
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        });
                    },


                    /**
                     * Hiển thị thông tin
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    info: function (message, callback, hideCallback) {
                        var msg = '';
                        var title = 'Thông báo';

                        var icon = 'info';
                        var tyoe = App.getType(message);
                        if (tyoe == 'string') {
                            msg = message;
                        }
                        else if (type == 'array') {
                            msg = message.map(function (m) {
                                return App.str.replace(m, ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }
                        else if (type == 'object') {
                            msg = message.content ? message.content : (message.message ? message.message : (message.html ? message.html : ""));
                            if (message.title) title = message.title;
                            if (Object.hasOwnProperty.call(message, 'icon')) icon = message.icon;
                            else if (Object.hasOwnProperty.call(message, 'type')) icon = message.tyoe;
                        }
                        msg = App.str.replace(msg, ['[nl]', "\r\n", "\n"], '<br />');

                        Swal.fire({
                            title: title,
                            html: msg,
                            type: icon,
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        });
                    },


                    /**
                     * Hiển thị thông báo hành động thành công
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    success: function (message, callback, hideCallback) {
                        var msg = '';
                        var title = 'Thành công!';
                        var icon = 'success';
                        var tyoe = App.getType(message);
                        if (tyoe == 'string') {
                            msg = message;
                        }
                        else if (type == 'array') {
                            msg = message.map(function (m) {
                                return App.str.replace(m, ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }
                        else if (type == 'object') {
                            msg = message.content ? message.content : (message.message ? message.message : (message.html ? message.html : ""));
                            if (message.title) title = message.title;
                            if (Object.hasOwnProperty.call(message, 'icon')) icon = message.icon;
                            else if (Object.hasOwnProperty.call(message, 'type')) icon = message.tyoe;
                        }
                        msg = App.str.replace(msg, ['[nl]', "\r\n", "\n"], '<br />');

                        Swal.fire({
                            title: title,
                            html: msg,
                            type: icon,
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        });
                    },


                    /**
                     * Hiển thị thông báo
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    warning: function (message, callback, hideCallback) {
                        var msg = '';
                        var title = 'Cảnh báo!';
                        var icon = 'warning';
                        var tyoe = App.getType(message);
                        if (tyoe == 'string') {
                            msg = message;
                        }
                        else if (type == 'array') {
                            msg = message.map(function (m) {
                                return App.str.replace(m, ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }
                        else if (type == 'object') {
                            msg = message.content ? message.content : (message.message ? message.message : (message.html ? message.html : ""));
                            if (message.title) title = message.title;
                            if (Object.hasOwnProperty.call(message, 'icon')) icon = message.icon;
                            else if (Object.hasOwnProperty.call(message, 'type')) icon = message.tyoe;
                        }
                        msg = App.str.replace(msg, ['[nl]', "\r\n", "\n"], '<br />');

                        Swal.fire({
                            title: title,
                            html: msg,
                            type: icon,
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        });
                    },


                    /**
                     * Hiển thị thông báo lỗi
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    error: function (message, callback, hideCallback) {
                        var msg = '';
                        var title = 'Đã xảy ra lỗi';
                        var icon = 'error';
                        var tyoe = App.getType(message);
                        if (tyoe == 'string') {
                            msg = message;
                        }
                        else if (type == 'array') {
                            msg = message.map(function (m) {
                                return App.str.replace(m, ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }
                        else if (type == 'object') {
                            msg = message.content ? message.content : (message.message ? message.message : (message.html ? message.html : ""));
                            if (message.title) title = message.title;
                            if (Object.hasOwnProperty.call(message, 'icon')) icon = message.icon;
                            else if (Object.hasOwnProperty.call(message, 'type')) icon = message.tyoe;
                        }
                        msg = App.str.replace(msg, ['[nl]', "\r\n", "\n"], '<br />');

                        Swal.fire({
                            title: title,
                            html: msg,
                            type: icon,
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        });
                    },

                    errorDetail: function (title, detail, callback, hideCallback) {
                        var message = '';
                        var tyoe = App.getType(detail);
                        if (tyoe == 'string') {
                            message = App.str.replace(detail, ['[nl]', "\r\n", "\n"], '<br />');
                        }
                        else if (type == 'array') {
                            message = detail.map(function (m) {
                                return App.str.replace(m, ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }
                        else if (type == 'object') {
                            message = Object.keys(detail).map(function (m) {
                                return App.str.replace(detail[m], ['[nl]', "\r\n", "\n"], '<br />');
                            }).join('<br />');
                        }

                        return Swal.fire({
                            title: title,
                            html: message,
                            type: 'error',
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        });
                    },


                    confirm: function (message, yes, no, callback, hideCallback) {
                        var title = 'Xác nhận';
                        var tyoe = 'question';
                        if (App.getType(message) == 'object') {
                            if (message.title) title = message.title;
                            if (Object.hasOwnProperty.call(message, 'type')) tyoe = message.type;
                            else if (Object.hasOwnProperty.call(message, 'icon')) icon = message.icon;
                            if (typeof message.confirmed == "function") yes = message.confirmed;

                            message = message.message ? message.message : (message.content ? message.content : "")
                        }
                        message = App.str.replace(message, ['[nl]', "\r\n", "\n"], '<br />');

                        Swal.fire({
                            title: title,
                            html: message,
                            type: tyoe,
                            showCancelButton: true,
                            confirmButtonColor: '#0000FF',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Không',
                            willOpen: function willOpen() {
                                if (typeof callback == "function") {
                                    callback();
                                }
                            },
                            didClose: function didClose() {
                                if (typeof hideCallback == "function") {
                                    hideCallback();
                                }
                            }
                        }).then(function (result) {
                            if (result.value) {
                                if (typeof yes == "function") {
                                    yes();
                                }
                            } else {
                                if (typeof no == "function") {
                                    no();
                                }
                            }
                        });
                    },
                    timer: function (timer, message, title, type, button, position) {
                        if (!timer) timer = 1500;
                        if (!type) type = 'success';
                        var option = {
                            timer: timer,
                            timerProgressBar: true
                        };
                        if (typeof button != "undefined") {
                            if (button === false) {
                                option.showConfirmButton = false;
                            } else if (App.isString(button)) {
                                option.showConfirmButton = true;
                                option.confirmButtonText = button;
                            }
                        }else{
                            option.showConfirmButton = false;

                        }
                        if (position) {
                            option.position = position;
                        }
                        if (message) {
                            if (!title) {
                                option.title = message;
                                // option.html = message;
                            } else {
                                option.title = message;
                                option.html = title;
                            }
                        }
                        Swal.fire(option);


                    },

                    showLoading: function (timer, title, message) {
                        let timerInterval
                        if (!timer || isNaN(timer)) {
                            if (App.isObject(timer)) {
                                //
                            } else {

                            }
                        }
                        var getDots = function (t) {
                            var dots = '.';
                            for (let i = 0; i < t; i++) {
                                dots += '.';

                            }
                            return dots;
                        }
                        var n = 0;
                        Swal.fire({
                            title: title ? title : 'Đang xử lý',
                            html: message ? message : 'Vui lòng chờ trong giây lát...',
                            timer: timer,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading()
                                timerInterval = setInterval(() => {
                                    // const content = Swal.getContent()
                                    // if (content) {
                                    //     const b = content.querySelector('b')
                                    //     if (b) {
                                    //         b.textContent = getDots(n%5)
                                    //         n++;
                                    //     }
                                    // }
                                }, 100)
                            },
                            didClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal.DismissReason.timer) {
                                // console.log('I was closed by the timer')
                            }
                        })
                    },
                    hideLoading: function (timer) {
                        if (timer && App.isNumber(timer)) {
                            setTimeout(function () {
                                Swal.close();
                            }, timer);
                        } else {
                            Swal.close();
                        }
                    }

                }
            });
            $(function () {
                function CrazyModal(selector) {
                    var $modal = $(selector);
                    var $header = $modal.find('.modal-header'),
                        $body = $modal.find('.modal-body'),
                        $footer = $modal.find('.modal-footer');
                    var $title = $header.find('.modal-title'),
                        $message = $body.find('.modal-message'),
                        $buttons = $footer.find('.modal-buttons');
                    var hideCallback = null;
                    var ShowCallback = null;
                    var self = this;
                    this.isOpen = false;
                    this.show = function (callback) {

                    };
                }


                /**
                 * modal
                 */

                App.modal = {
                    confirm_callback: App.log,
                    hide_callback: null,
                    show_callback: null,
                    is_on: false,
                    current_open: null,
                    last_open: null,
                    waiting_for_hide: false,
                    popupData: {},
                    popupFiles: {},
                    /**
                     * Hiển thị modal
                     * @param {string} id id của modal
                     * @param {Function} show_callback ham thuc thi ngay sau khi hien thi modal
                     * @param {Function} hide_callback Hàn thực thi sau khi đóng modal
                     */
                    show: function (id, show_callback, hide_callback) {

                        var self = this;
                        if (id == self.current_open) return true;
                        if (self.is_on) {
                            self.hide(null, function () {
                                self.show(id, show_callback, hide_callback);
                            });
                        } else {
                            if (typeof show_callback == 'function') {
                                self.show_callback = show_callback;
                            }
                            $('#' + id).modal('show');

                            if (typeof hide_callback == "function") {
                                setTimeout(function () {
                                    self.hide_callback = hide_callback;
                                }, 201);
                            }
                        }
                    },
                    /**
                     * ẩn modal
                     * @param {string} id id của modal
                     * @param {Function} callback Hàn thực thi sau khi đóng modal
                     */
                    hide: function (id, callback) {
                        var self = this;
                        if (typeof callback == 'function') {
                            self.hide_callback = callback;
                        }
                        if (id) {
                            $('#' + id).modal('hide');
                        } else {
                            $('.modal').modal('hide');
                        }
                    },


                    /**
                     * mở hộp thoại xác thực
                     * @param {string} message
                     * @param {function} callback
                     */
                    confirm: function (message, callback, hide_callback) {
                        if (typeof (callback) == 'function') {
                            this.confirm_callback = callback;
                        } else {
                            this.confirm_callback = App.emptyFunc;
                        }

                        $('#confirm-modal .modal-message').html(message);
                        this.show('confirm-modal', null, hide_callback);
                    },

                    answer: function modal(stt) {
                        this.confirm_callback(stt ? true : false);
                    },


                    /**
                     * Hiển thị thông báo
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    alert: function (message, callback, hideCallback) {
                        $('#alert-modal .modal-message').html(message);
                        this.show('alert-modal', callback, hideCallback);
                    },


                    /**
                     * Hiển thị thông tin
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    info: function (message, callback, hideCallback) {
                        $('#info-modal .modal-message').html(message);
                        this.show('info-modal', callback, hideCallback);
                    },


                    /**
                     * Hiển thị thông báo hành động thành công
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    success: function (message, callback, hideCallback) {
                        $('#success-modal .modal-message').html(message);
                        this.show('success-modal', callback, hideCallback);
                    },


                    /**
                     * Hiển thị thông báo
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    warning: function (message, callback, hideCallback) {
                        $('#warning-modal .modal-message').html(message);
                        this.show('warning-modal', callback, hideCallback);
                    },


                    /**
                     * Hiển thị thông báo lỗi
                     * @param {string} message Nội dung thông báo
                     * @param {function} callback hàm sẽ thực thi sau khi hiển thị modal
                     * @param {function} hideCallback hàm sẽ thực thi sau khi ẩn modal
                     */
                    error: function (message, callback, hideCallback) {
                        $('#error-modal .modal-message').html(message);
                        this.show('error-modal', callback, hideCallback);
                    },

                    /**
                     * Hiển thị nội dung tùy ý
                     * @param {object} data
                     */
                    custom: function (data, showCallback, hideCallback) {
                        if (data) {
                            var t = App.getType(data);
                            if (t == 'object') {
                                var title = data.title ? data.title : '';
                                var content = data.content ? data.content : '';
                                var buttons = document.getElementById('custom-modal-buttons');
                                var btns = [];
                                buttons.innerHTML = '';
                                if (data.buttons) {
                                    var btnData = data.buttons;
                                    var bt = App.getType(btnData);
                                    if (bt == 'object') {
                                        btns[0] = btnData;
                                    } else if (bt == 'array') {
                                        btns = btnData;
                                    }

                                    for (var i = 0; i < btns.length; i++) {
                                        let btn = btns[i];
                                        let btnText = 'Button';
                                        // let btnHtml = '<button';
                                        let button = document.createElement('button');
                                        if (!btn.type) {
                                            button.setAttribute('type', 'button');
                                        }
                                        for (let prop in btn) {
                                            let key = prop.toLowerCase();
                                            let val = btn[prop];
                                            if (key == 'text') {
                                                btnText = val;
                                            } else if (key == 'classname' || key == 'class') {
                                                button.className = val;
                                            } else if (key == 'click' || key == 'onclick') {
                                                if (typeof val == 'function') {
                                                    button.addEventListener('click', val, false);
                                                } else {
                                                    button.setAttribute('data-click', val);
                                                    button.addEventListener('click', function (e) {
                                                        App.func.call(this.getAttribute('data-click'));
                                                    }, false);
                                                }
                                            } else {
                                                button.setAttribute(prop, val);
                                            }
                                        }
                                        button.classList.add('mr-2');
                                        // let text = document.createTextNode(btnText);
                                        button.innerHTML = btnText;
                                        buttons.appendChild(button);
                                    }
                                }
                                $('#custom-modal .custom-modal-content').html(content);
                                $('#custom-modal .modal-title').html(title);
                                // $('#custom-modal .modal-buttons').html(buttons);
                                if (data.size) {
                                    $('#custom-modal .modal-dialog').removeClass().addClass('modal-dialog modal-' + data.size);
                                }
                                this.show('custom-modal', showCallback, hideCallback);
                            }
                        }
                    },

                    /**
                     * Hiển thị popup để nhập thông tin
                     * @param {object} options
                     * @param {function} doneCallback hàm sẽ thực thi sau khi du744 liệu được nhập dúng mẫu
                     */

                    popup: function (options, doneCallback, showCallback, hideCallback) {
                        this.popupData = {};
                        var defaults = {
                            btnDone: "Xong",
                            btnCancel: "Đóng",
                            title: "Nhập thông tin",
                            options: {}
                        };
                        var params = {};
                        if (typeof doneCallback == 'function') {
                            params.done = doneCallback;
                        }
                        else {
                            params.done = App.log;
                        }
                        if (App.getType(options) == 'object') {
                            params.btnDone = options.btnDone || defaults.btnDone;
                            params.btnCancel = options.btnCancel || defaults.btnCancel;
                            params.title = options.title || defaults.title;
                            params.options = options.options || defaults.options;
                            if (typeof options.done == 'function') params.done = options.done;
                            if (typeof options.inputs != 'undefined' && options.inputs) {
                                var t = App.getType(options.inputs);
                                if (t == "object") {
                                    params.inputs = options.inputs;
                                } else if (t == "Array" && options.inputs.length) {
                                    var inputs = {};
                                    var l = options.inputs.length;
                                    for (let i = 0; i < l; i++) {
                                        const input = options.inputs[i];
                                        if (App.getType(input) == "object") {
                                            if (input.name) {
                                                inputs[input.name] = input;
                                            }
                                        }
                                    }
                                }
                            }
                            if (typeof options.validate != "undefined" && App.getType(options.validate) == "object") {
                                params.validate = options.validate;
                            }
                        }
                        if (params.inputs) {
                            this.popupData = params;
                            var html = "";
                            var label_class = "col-sm-4 col-lg-3";
                            var input_class = "form-control m-input";
                            var wrapper_class = "col-sm-8 col-lg-9";
                            var formGroup = '<div class="form-group row" id="{$id}-group"><label for="{$id}" id="{$id}-label" class="{$label_class}">{$label}</label>'
                                + '<div class="{$wrapper_class}">{$input}</div>'
                                + '</div>';
                            if (params.options.label_class) label_class = params.options.label_class;
                            if (params.options.input_class) input_class = params.options.input_class;
                            if (params.options.wrapper_class) wrapper_class = params.options.wrapper_class;
                            var inplist = params.inputs;
                            for (const name in inplist) {
                                if (inplist.hasOwnProperty(name)) {
                                    const inp = inplist[name];
                                    if (!inp.name) inp.name = name;
                                    if (!inp.type) inp.type = "text";
                                    let events = {};
                                    let $inp = '';
                                    let tagName = '';
                                    let label = inp.label ? inp.label : inp.name;
                                    if (!inp.placeholder) inp.placeholder = label;
                                    let id = inp.id ? inp.id : inp.name;
                                    let type = inp.type.toLowerCase();
                                    let props = '';
                                    let inp_class = "";
                                    var value = (typeof inp.value != "undefined" && inp.value !== null && (inp.value || ("" + inp.value).length)) ? inp.value : ((typeof inp.defaultValue != "undefined" && (inp.defaultValue || inp.defaultValue.length)) ? inp.defaultValue : '');
                                    let append = '';
                                    let prepend = '';
                                    if (type == 'file') {
                                        inp_class += ' custom-file-input';
                                    }
                                    for (const prop in inp) {
                                        if (inp.hasOwnProperty(prop)) {
                                            const val = inp[prop];
                                            let p = prop.toLowerCase();
                                            const tv = App.getType(val);
                                            if (p != 'type' && p != 'append' && p != 'prepend' && p != 'value' && p != 'label' && (tv == 'string' || tv == 'number')) {
                                                let vp = App.str.htmlentities(val);
                                                if (p == 'classname') p = 'class';
                                                if (p == 'class') {
                                                    vp = '{$input_class} ' + vp;
                                                    inp_class += vp;
                                                }
                                                props += ' ' + prop + '="' + vp + '"';
                                            }
                                            else if (p == 'validate') {
                                                this.addPopupRule(inp.name, val);
                                            }
                                            else if (p == 'append') {
                                                append = val;
                                            }
                                            else if (p == 'prepend') {
                                                prepend = val;
                                            }
                                            else if (prop.substr(0, 1) == '@') {
                                                if (tv == 'function') {
                                                    events[p.substr(1)] = val;
                                                } else if (App.func.check(val)) {
                                                    events[p.substr(1)] = function (e) {
                                                        App.func.call(val, [e]);
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if (inp_class != "") {
                                        input_class = type == 'file' ? inp_class : input_class;
                                    }
                                    props += ' class="{$input_class}"';

                                    if (type == 'textarea') $inp = '<' + type + props + '>' + (value) + '</' + type + '>';
                                    else if (type == 'select') {
                                        let opts = '';
                                        let data = inp.data || inp.options || {};
                                        if (App.getType(data) == "object") {
                                            for (const v in data) {
                                                if (data.hasOwnProperty(v)) {
                                                    let text = data[v];
                                                    opts += '<option value="' + v + '" ' + (v == value ? 'selected' : '') + '>' + text + '</option>';
                                                }
                                            }
                                        }
                                        $inp = '<' + type + props + '>' + (opts) + '</' + type + '>';
                                    }

                                    else if (type == 'file') {
                                        $inp = '<div class="custom-file">' +
                                            '<input type="file" ' + props + ' data-on-change="App.modal.onPopupFile">' +
                                            '<label class="custom-file-label" for="' + id + '">' + (value.length ? App.str.htmlentities(value) : 'Chọn file') + '</label>' +
                                            '</div>';
                                    }
                                    else if (type == 'media') {
                                        $inp = '' +
                                            `<div class="input-media" data-type="{{$type}}">
                                <div class="input-group">
                                    <input type="hidden" name="{{$input->name}}" id="{{$input->id?$input->id:$input->name}}" value="{{$input->value}}" class="media-input-hidden">
                                    <div class="input-group-prepend">
                                        <img src="{{$file->thumbnail}}" alt="{{$file->filename}}" class="media-image-thumbnail">
                                    </div>
                                    <input type="text" name="{{$input->name}}{{count(explode(']', $input->name)) > 1 ? '[text]': '_text'}}" value="{{$file->original_filename}}" class="media-input-text form-control m-input" readonly disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text file-size">{{$input->value?$file->size.$file->size_unit:''}}</span>
                                    </div>
                                </div>
                            </div>`;
                                    }

                                    else {
                                        $inp = '<input type="' + type + '"' + props + ' value="' + App.str.htmlentities(value) + '">';
                                    }

                                    if (append || prepend) {
                                        $inp = '<div class="input-group">' + $inp;
                                        if (prepend) {
                                            $inp = '<div class="input-group-prepend">' + prepend + '</div>' + $inp;
                                        }
                                        if (append) {
                                            $inp += '<div class="input-group-append">' + append + '</div>';
                                        }
                                        $inp += '</div>';
                                    }

                                    if (type == "hidden") {
                                        html += App.str.eval($inp, { input_class: input_class });
                                    }
                                    else {
                                        html += App.str.eval(formGroup, {
                                            label_class: label_class + " col-form-label",
                                            input_class: input_class,
                                            wrapper_class: wrapper_class + (' ' + (inp.wrapper_class ? inp.wrapper_class : '')),
                                            label: label,
                                            id: id,
                                            input: App.str.eval($inp, { input_class: input_class })
                                        });
                                    }


                                }
                            }
                            let $popup = $('#popup-modal');
                            $popup.find('.modal-header .modal-title span').html(params.title);
                            $popup.find('.modal-body').html(html);
                            $popup.find('.modal-footer .btn-done').html(params.btnDone);
                            $popup.find('.modal-footer .btn-cancel').html(params.btnCancel);
                            if (options.size) {
                                $('#popup-modal .modal-dialog').removeClass().addClass('modal-dialog modal-' + options.size);
                            }

                            this.show('popup-modal', showCallback, hideCallback);

                        }
                    },
                    popupDone: function (formData) {
                        var self = this;
                        var errors = [];
                        var data = {};
                        var $popup = $('#popup-modal');
                        let inputs = self.popupData.inputs;
                        if (formData.length) {
                            formData.map(function (e) {
                                let name = e.name;
                                let value = e.value;
                                let input = inputs[name];
                                if (typeof self.popupData.validate == 'object' && typeof self.popupData.validate[name] == 'function') {
                                    let validate = self.popupData.validate[name];
                                    if (validate(value)) {
                                        data[name] = value;
                                    } else {
                                        errors[errors.length] = input.label + " Không hợp lệ";
                                    }
                                }
                                else {
                                    data[name] = value;
                                }
                            });
                            if (errors.length) {
                                self.error(errors.join("<br>"), function () {
                                    self.hide_callback = function () {
                                        self.show('popup-modal');
                                    };
                                });
                            } else {
                                self.hide('popup-modal', function () {
                                    self.popupData.done(data);
                                });

                            }
                        }
                    },
                    addPopupRule: function (name, rule) {
                        if (typeof rule != "function") return false;
                        if (typeof this.popupData.validate == "undefined") {
                            this.popupData.validate = {};
                        }
                        this.popupData.validate[name] = rule;
                        return this;

                    },
                    onPopupFile: function (input, list) {
                        this.popupFiles[input.name] = list;
                    },
                    last: function () {
                        if (this.last_open) {
                            this.show(this.last_open);
                        }
                    }
                };



                if ($('.modal').length > 0) {
                    $('#confirm-modal .btn-confirm-answer').click(function () {
                        var $this = $(this);
                        App.modal.hide('confirm-modal');
                        if ($this.hasClass('yes')) {
                            App.modal.answer(true);
                        } else {
                            App.modal.answer(false);
                        }
                    });


                    $('#popup-modal #crazy-popup-form').submit(function (e) {
                        e.preventDefault();
                        var data = $(this).serializeArray();
                        var files = $(this).find('input[type="file"]');

                        if (files.length) {
                            for (let i = 0; i < files.length; i++) {
                                const inp = files[i];
                                if (typeof App.modal.popupFiles[inp.name] != "undefined") {
                                    data.push({
                                        name: inp.name,
                                        value: App.modal.popupFiles[inp.name]
                                    })
                                }
                            }
                        }
                        App.modal.popupDone(data);
                        return false;
                    });

                    $('.modal').on('hidden.bs.modal', function () {
                        App.modal.is_on = false;
                        var $this = this;
                        setTimeout(function () {
                            if (typeof App.modal.hide_callback == 'function') {
                                App.modal.hide_callback($this, App.modal);
                            }

                            App.modal.last_open = App.modal.current_open;
                            App.modal.current_open = null;

                            App.modal.hide_callback = null;
                        }, 200);


                    });
                    $('.modal').on('show.bs.modal', function (e) {
                        var $this = this;
                        App.modal.is_on = true;

                        setTimeout(function () {
                            App.modal.current_open = $($this).attr('id');
                            if (typeof App.modal.show_callback == 'function') {
                                App.modal.show_callback($this, App.modal);
                            }
                            App.modal.show_callback = null;
                        }, 200);

                    });
                }
            });    /**
     * doi tuong quan li item
     * @type {object}
     */
            App.extend({
                comments: {
                    urls: {},
                    init_list: ["urls"],
                    form: null,
                    send: function (form) {
                        var self = this;
                        try {
                            let $form = $(form);
                            let data = {};
                            let errors = [];
                            this.form = $form;
                            $form.find('textarea, input, select').each(function (index, el) {
                                let $inp = $(el);
                                let name = $inp.attr('name');
                                if (name != '_token') {
                                    let value = $inp.val();
                                    data[name] = value;
                                }

                                //cl(el);
                            });
                            let url = $form.data('ajax-url');
                            let btn = $form.find('.post-comment');
                            let btnClass = btn.data('class') || "btn-primary";
                            let btnDisableClass = btn.data('disable-class') || "btn-default";
                            let BtnHtml = btn.html();
                            let sendingText = btn.data('sending-text') || 'Đang gửi liên hệ...';
                            if (btn.length) {
                                btn.prop('disable', true);
                                btn.html(sendingText);
                                btn.removeClass(btnClass).addClass(btnDisableClass);
                                // btn.attr('type', 'button');
                            }

                            App.ajax(url, {
                                method: "post",
                                data: data,
                                cookie: true
                            })
                                .then(function (res) {
                                    if (App.isString(res)) res = JSON.parse(res);
                                    if (res.status) {
                                        self.alert("Bạn đã gửi bình luận thành công! <br />Chúng tôi sẽ phản hồi trong thời gian sớm nhất!", 'success');
                                        $(form).find('.inp').each(function (ind, el) {
                                            let $inp = $(el);
                                            $inp.val('');
                                        });

                                        $(form).find('.cancel-reply').hide(300, function () {
                                            $('input#comment-reply-id').val(0);
                                            $(this).parent().parent().removeClass('reply-mode');
                                            $(this).parent().remove();
                                        });
                                    } else {
                                        var message = '';
                                        if (res.errors) {
                                            var messages = [];
                                            for (const key in res.errors) {
                                                if (res.errors.hasOwnProperty(key)) {
                                                    const error = res.errors[key];
                                                    messages.push(error);
                                                }
                                            }
                                            message = messages.join("<br>");
                                        } else {
                                            message = res.message;
                                        }
                                        self.alert(message);
                                    }
                                    if (btn.length) {
                                        btn.prop('disable', false);
                                        btn.html(BtnHtml);
                                        btn.removeClass(btnDisableClass).addClass(btnClass);
                                    }

                                })
                                .catch(function (e) {
                                    let msg = e.responseText;
                                    self.alert(msg, e.status == 200 ? 'success' : 'danger');
                                    if (btn.length) {
                                        btn.prop('disable', false);
                                        btn.html(BtnHtml);
                                        btn.removeClass(btnDisableClass).addClass(btnClass);
                                    }
                                });
                        }
                        catch (e) {

                            self.alert("Đã có lỗi bất ngờ xảy ra. vui lòng thử lại trong giây lát");
                        }
                    },
                    alert: function (message, type) {
                        var self = this;
                        if (!type) type = 'danger';
                        if (self.form.find('#contact-alert-' + type).length) {
                            self.form.find('#contact-alert-' + type).remove();
                        }
                        let alertTag = '<div id="contact-alert-' + type + '" class="alert alert-' + type + ' mt-4" role="' + type + '">' + message + '</div>';
                        self.form.append(alertTag);
                        self.form.find('#contact-alert-' + type).show(400);
                        setTimeout(function () {
                            self.form.find('#contact-alert-' + type).hide(400, function () {
                                $(this).remove();
                            });
                        }, 10000);

                    }

                }
            });

            $(function () {

                var $commentForm = $(prefixClass + 'comment-form');
                $(prefixClass + 'comment-form').submit(function (e) {
                    e.preventDefault();
                    App.comments.send(this);
                    return false;
                });




                $(document).on('click', '.btn-reply-comment, ' + prefixClass + "btn-reply-comment", function () {
                    let replyID = $(this).data('id');
                    let user = $(this).data('reply-for');
                    if (replyID) {
                        $('input#comment-reply-id').val(replyID);
                        if (user) {
                            let txtField = $('.comment-message-content');
                            if (txtField.length) {
                                let wrapper = txtField.parent();
                                wrapper.addClass('reply-mode');
                                wrapper.find('.reply-item').remove();
                                wrapper.prepend('<div class="reply-item">Trả lời ' + user + ' <a href="javascripy:void(0);" class="cancel-reply" data-id="' + replyID + '">x</a></div>')
                                if ($commentForm.length) {
                                    $commentForm[0].scrollIntoView(false);
                                }
                            }
                        }
                    }

                });

                $(document).on('click', '.cancel-reply', function () {
                    $('input#comment-reply-id').val(0);
                    $(this).parent().parent().removeClass('reply-mode');
                    $(this).parent().remove();
                });
            });

            // giỏ hàng
            App.extend({
                products: {
                    decimal: 0,
                    urls: {},
                    templates: {
                        item: "<p>{$name} - {{$price}}</p>"
                    },
                    init_list: ["urls", "templates", "decimal"],
                    // kiểm tra giá sdan3 phẩm kèm thuộc tính
                    /**
                     *
                     * @param {*} data
                     */
                    checkPrice: function (data) {
                        App.api.post(this.urls.check_price, data)
                            .then(function (res) {
                                if (res.status) {
                                    var d = res.data;
                                    $(prefixClass + 'product-detail-info-' + d.product.id).find(prefixClass + "product-price").html(App.cart.currency(d.price));
                                    if (!d.status) {
                                        App.Swal.warning(d.message);
                                        $(prefixClass + 'product-detail-info-' + d.product.id).find(prefixClass + "product-order-quantity").val(d.quantity)
                                    }
                                }
                            });
                    },

                    checkPriceOfForm: function (form) {
                        var data = {
                            product_id: $(form).find(prefixClass + "product-order-id").val(),
                            attrs: [],
                            quantity: $(form).find(prefixClass + "product-order-quantity").val() || 1
                        };
                        var inputs = $(form).find(prefixClass + "product-variants").find('select, input[type="radio"]:checked, input[type="rcheckbox"]:checked');
                        for (let i = 0; i < inputs.length; i++) {
                            const inp = inputs[i];
                            data.attrs.push($(inp).val());
                        }
                        this.checkPrice(data);

                    },
                    getData: function (id, callback) {
                        var ajax = App.ajax(this.urls.get_data, {
                            method: "GET",
                            data: { id: id },
                            dataType: "json"
                        });
                        if (App.isCallable(callback)) {
                            ajax.then(callback);
                        }
                        return ajax;
                    },
                    currency: function (total) {
                        var c = App.config.currency;
                        return App.number.format(total, c.decimal, c.decimal_poiter, c.thousands_sep, c.currency_type, c.currency_position)
                    },
                    review: function (form) {
                        var serialized = $(form).serializeArray();
                        var data = {};
                        var $form = $(form);
                        serialized.map(function (inp) {
                            if (inp.name == '_token') return null;
                            if (typeof data[inp.name] != "undefined") {

                                if (App.getType(data[inp.name]) == 'array') {
                                    data[inp.name].push(inp.value);
                                } else {
                                    data[inp.name] = [data[inp.name], inp.value];
                                }
                            } else {
                                data[inp.name] = inp.value;
                            }
                        });
                        if (!data.rating || data.rating == "0" || !data.name || !data.email) {
                            App.popup.warning("Vui lòng xếp hạn sản phẩm, điều đầy dủ họ tên và email!");
                        } else {
                            App.ajax(this.urls.review, {
                                method: "post",
                                data: data,
                                dataType: "JSON"
                            }).then(function (res) {
                                if (res.status) {
                                    App.popup.alert(res.message);
                                    $form.find("input,textarea").map(function (inp) {
                                        if (["comment", "name", "email"].indexOf(inp.name) >= 0) {
                                            $(inp).val("");
                                        }
                                    })
                                } else {
                                    var errMessage = "";
                                    if (res.errors) {
                                        for (const key in res.errors) {
                                            if (res.errors.hasOwnProperty(key)) {
                                                const error = res.errors[key];
                                                errMessage = error;
                                                break;
                                            }
                                        }
                                    }
                                    App.popup.error(errMessage ? errMessage : "Kiểm tra lại thông tin");
                                }
                            }).catch(function (e) {
                                App.popup.error("Lỗi không xác định");
                            });
                        }
                    }
                },
                orderFormSelector: null
                // end cart
            });


            if (typeof window.productAppInit == "function" || typeof window.customProductAppInit == "function") {
                if (typeof window.productAppInit == "function") {
                    window.productAppInit();
                }
                if (typeof window.customProductAppInit == "function") {
                    window.customProductAppInit();
                }


                var detailClass = prefixClass + "product-detail";


                var frmSelector = detailClass + " " + prefixClass + "product-order-form";
                App.products.orderFormSelector = frmSelector;
                $(document).on("change", frmSelector, function (e) {
                    App.products.checkPriceOfForm(this);
                });

                var productOrderForms = $(frmSelector);
                if (productOrderForms.length) {
                    for (let i = 0; i < productOrderForms.length; i++) {
                        const form = productOrderForms[i];
                        App.products.checkPriceOfForm(form);
                    }
                }

                $(prefixClass + "product-review-form").submit(function (e) {
                    e.preventDefault();
                    App.products.review(this);
                    return false;
                });

            }

            //auth
            App.extend({
                auth: {
                    urls: {},
                    templates: {
                        link: "<li><a href='{$link}'>{$text}</a></li>",
                        account_section: "{$name}"
                    },
                    init_list: ["urls", "templates"],
                    // kiểm tra giá sdan3 phẩm kèm thuộc tính
                    /**
                     *
                     * @param {*} data
                     */
                    check: function (callback) {
                        var self = this;
                        App.ajax(this.urls.check, {
                            method: "post",
                            data: { a: 1 },
                            dataType: "json"
                        }).then(function (res) {
                            var d = res.data;
                            if (res.status) {
                                $(prefixClass + "account-section").html(App.str.eval(self.templates.account_section || "{$name}", d));
                            }
                            if (d.links && d.links.length) {
                                // var l = d.links.length;
                                var links = d.links;
                                var $links = $(prefixClass + "account-links");
                                $links.html("");
                                for (const key in links) {
                                    if (links.hasOwnProperty(key)) {
                                        const item = links[key];
                                        $links.append(App.str.eval(self.templates.link || "<li><a href='{$link}'>{$text}</a></li>", item));
                                    }
                                }
                            }
                            //return res;
                            var a = typeof callback == "function" ? callback(res) : null;
                        });
                    }
                }
                // end cart
            });


            if (typeof window.authInit == "function" || typeof window.customauthInit == "function") {
                if (typeof window.authInit == "function") {
                    window.authInit();
                }
            }


            // giỏ hàng
            App.extend({
                location: {
                    urls: {},
                    init_list: ["urls"],
                    regionID: 0,
                    districtID: 0,

                    changeRegionID: function (value, text, el) {
                        if (value != this.regionID) {
                            this.regionID = value;
                            App.dom.select.deactive('district_id');
                            App.dom.select.deactive('ward_id');
                            App.dom.select.changeOptions('district_id', { 0: "Chọn Quận / Huyện" });
                            App.dom.select.changeOptions('ward_id', { 0: "Chọn Xã / Phường" });
                            if (value && value != "0") {
                                App.ajax(this.urls.district_options, {
                                    method: "get",
                                    data: { region_id: value },
                                    dataType: "JSON"
                                }).then(function (res) {
                                    if (res.status) {
                                        App.dom.select.changeOptions('district_id', res.data);
                                    }
                                });
                            }
                        }
                    },

                    changeDistrictID: function (value, text, el) {
                        if (value != this.districtID) {
                            this.districtID = value;
                            App.dom.select.deactive('ward_id');
                            App.dom.select.changeOptions('ward_id', { 0: "Chọn Xã / Phường" });
                            if (value && value != "0") {
                                App.ajax(this.urls.ward_options, {
                                    method: "get",
                                    data: { district_id: value },
                                    dataType: "JSON"
                                }).then(function (res) {
                                    if (res.status) {
                                        App.dom.select.changeOptions('ward_id', res.data);
                                    }
                                });
                            }
                        }
                    }



                }
                // location
            });


            if (typeof window.locationInit == "function") {
                if (typeof window.locationInit == "function") {
                    window.locationInit();
                }
            }

            //contact
            App.extend({
                contact: {
                    urls: {},
                    init_list: ["urls"],
                    // kiểm tra giá sdan3 phẩm kèm thuộc tính
                    /**
                     *
                     * @param {*} data
                     */
                    send: function (form, success, error) {
                        var self = this;
                        var formData = $(form).serialize();
                        var url = self.urls.send_contact_url || $(form).attr('data-ajax-url') || $(form).attr('action');

                        var successCallback = typeof success == "function" ? success : function (res) {
                            if (res.status) {
                                App.popup.success("Bạn đã gữi liên hệ thành công!</br>Chúng tôi sẽ phản hồi trong thời gian sớm nhất");
                                var inps = $(form).find('.inp');
                                if (inps.length) {
                                    for (let i = 0; i < inps.length; i++) {
                                        const inp = inps[i];
                                        $(inp).val("");
                                    }
                                }
                            } else {
                                App.popup.warning("Vui lòng điền đầy đủ thông tin!");
                            }
                        };
                        var errorCallback = typeof error == "function" ? error : function (e) {
                            App.popup.error(e.responseText);
                        };
                        App.ajax(url, {
                            method: "post",
                            data: formData,
                            dataType: "json"
                        }).then(successCallback).catch(errorCallback);
                    }
                }
                // end cart
            });


            if (typeof window.contactInit == "function" || typeof window.customcontactInit == "function") {
                if (typeof window.contactInit == "function") {
                    window.contactInit();
                }
            }

            var contactForm = $(prefixClass + "contact-form");
            contactForm.submit(function (e) {
                e.preventDefault();
                App.contact.send(this);
            });

            // giỏ hàng
            App.extend({
                cart: {
                    decimal: 2,
                    urls: {},
                    promo: {
                        types: {}
                    },
                    currency_position: "right",
                    templates: {
                        item: "<p>{$name} - {$price}</p>"
                    },
                    currency_unit: "Đ",
                    init_list: ["urls", "templates", "decimal", "currency_unit", "currency_position", "promo"],
                    regionID: 0,
                    districtID: 0,
                    shippingRegionID: 0,
                    shippingDistrictID: 0,
                    cartType: "",
                    cartID: 0,
                    // kiểm tra giá sdan3 phẩm kèm thuộc tính
                    /**
                     *
                     * @param {*} data
                     */
                    checkPrice: function (data) {
                        data.cart_type = this.cartType;
                        data.cart_id = this.cartID;
                        App.api.post(this.urls.check_price, data).then(function (res) {
                            if (res.status) {
                                var d = res.data;
                                $(prefixClass + 'product-detail-info-' + d.product.id).find(prefixClass + "product-price").html(App.cart.currency(parseInt(d.price)));
                            }
                        })
                    },

                    updateCartData: function (data) {
                        if (!App.isObject(data)) data = {};
                        var cart_quantity = 0,
                            sub_total = data.sub_total || 0,
                            tax = data.tax || 0,
                            total_money = data.total_money || 0,
                            promo_total = data.promo_total || 0,
                            shipping_fee = data.shipping_fee || 0,
                            details = data.details && data.details.length ? data.details : [];
                        $(prefixClass + "cart-sub-total-ammount, " + prefixClass + "cart-sub-total-amount").html(this.currency(parseInt(sub_total)));

                        $(
                            prefixClass + "cart-tax-ammount, " + prefixClass + "cart-tax-total-ammount, "
                            + prefixClass + "cart-tax-amount, " + prefixClass + "cart-tax-total-amount"
                        ).html(this.currency(parseInt(tax)));
                        $(
                            prefixClass + "cart-shipping-fee, " + prefixClass + "cart-shipping-fee-ammount, " +
                            prefixClass + "cart-shipping-fee, " + prefixClass + "cart-shipping-fee-amount"
                        ).html(this.currency(parseInt(shipping_fee)));

                        if (promo_total && promo_total > 0) {
                            total_money -= promo_total;
                            $(
                                prefixClass + "cart-promo-total, " + prefixClass + "cart-promo-total-ammount, "
                                + prefixClass + "cart-promo-total, " + prefixClass + "cart-promo-total-amount, "
                                + prefixClass + "cart-promo-amount, " + prefixClass + "cart-promo-total-amount"
                            ).html(this.currency(promo_total));

                        } else if (data.promo_type == this.promo.types.TYPE_FREESHIP) {
                            $(
                                prefixClass + "cart-promo-total, " + prefixClass + "cart-promo-total-ammount, "
                                + prefixClass + "cart-promo-total, " + prefixClass + "cart-promo-total-amount, "
                                + prefixClass + "cart-promo-amount, " + prefixClass + "cart-promo-total-amount"
                            ).html("Miễn phí giao hàng");

                        }
                        $(
                            prefixClass + "cart-total-ammount, " + prefixClass + "cart-total-money-ammount, "
                            + prefixClass + "cart-total-amount, " + prefixClass + "cart-total-money-amount"
                        ).html(this.currency(parseInt(total_money)));

                        var itemTemplate = this.templates.item;
                        var attrTemplate = this.templates.attribute;
                        var detailLength = details.length;
                        var cartItems = '';
                        if (detailLength) {
                            for (let i = 0; i < detailLength; i++) {
                                var item = details[i];
                                if (!item.name) item.name = item.product_name;
                                item.price = this.currency(parseInt(item.price ? item.price : item.final_price));
                                cart_quantity += item.quantity;
                                $(prefixClass + "item-total-price-" + item.id + ", #cart-item-" + item.id)
                                    .find(prefixClass + "item-total-price")
                                    .html(this.currency(item.total_price ? item.total_price : item.quantity * item.final_price));

                                $(prefixClass + "item-total-price-" + item.id + ", #cart-item-" + item.id)
                                    .find(prefixClass + "item-total-list-price")
                                    .html(this.currency(item.total_list_price ? item.total_list_price : item.quantity * item.list_price));


                                var attributes = "";

                                if (item.attributes && App.isArray(item.attributes)) {
                                    var attrLength = item.attributes.length;
                                    if (attrLength) {
                                        for (let j = 0; j < item.attributes.length; j++) {
                                            var attr = item.attributes[j];
                                            attr.value = attr.text_value || attr[attr.value_type + "_value"] || "";
                                            attributes += App.str.eval(attrTemplate, attr);
                                        }
                                    }
                                }
                                item.attributes = attributes;

                                item.total_price = this.currency(item.total_price ? item.total_price : item.quantity * item.final_price);
                                cartItems += App.str.eval(itemTemplate, item);
                            }
                        } else {
                            cartItems = '<p>Không có sản phẩm nào</p>';
                        }
                        $(prefixClass + "cart-quantity").html(cart_quantity);
                        $(prefixClass + "cart-items").html(cartItems);
                        // code here

                    },


                    checkCartData: function () {
                        App.api.post(this.urls.check_cart_data, {
                            key: App.str.rand()
                        }).then(function (result) {
                            if (result.status) {
                                App.cart.updateCartData(result.data);
                            }
                        })
                    },

                    addCartItem: function (data, redirect) {
                        if (!data || !data.product_id) return App.popup.alert("Hành động không hợp lệ!");
                        App.api.post(this.urls.add_cart_item, data).then(function (result) {
                            if (result.status) {
                                App.cart.updateCartData(result.data);
                                if (redirect == 'checkout') {
                                    top.location.href = App.cart.urls.checkout;
                                }
                                else {

                                    App.popup.confirm("Sản phẩm đã được thêm vào giỏ hàng thành công!\nBạn có muốn đến trang thanh toán không?", function (p) {
                                        top.location.href = App.cart.urls.view_cart;
                                    })
                                }
                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (e) {
                            App.popup.alert("Lỗi ko xác định");
                        })
                    },
                    addCartCombo: function (data, redirect) {
                        if (!data || !data.combo_id) return App.popup.alert("Hành động không hợp lệ!");
                        App.api.post(this.urls.add_cart_combo, data).then(function (result) {
                            if (result.status) {
                                App.cart.updateCartData(result.data);
                                if (redirect == 'checkout') {
                                    top.location.href = App.cart.urls.checkout;
                                }
                                else {

                                    App.popup.confirm("Sản phẩm đã được thêm vào giỏ hàng thành công!\nBạn có muốn đến trang thanh toán không?", function (p) {
                                        top.location.href = App.cart.urls.view_cart;
                                    })
                                }
                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (e) {
                            App.popup.alert("Lỗi ko xác định");
                        })
                    },

                    removeCartItem: function (id) {
                        if (!id) return App.popup.alert("ID giỏ hàng không hợp lệ");

                        var self = this;
                        var data = { id: id };
                        var coupon = self.getCouponCode();

                        if (coupon) {
                            data.coupon = coupon;
                        }
                        data.cart_type = this.cartType;
                        data.cart_id = this.cartID;


                        App.api.post(this.urls.remove_cart_item, data).then(function (result) {
                            if (result.status) {
                                App.cart.updateCartData(result.data);
                                $("#cart-item-" + id + ", " + prefixClass + "cart-item-" + id).hide(300, function (e) {
                                    $(this).remove();
                                })
                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (e) {
                            App.popup.alert("Lỗi ko xác định");
                        });
                    },
                    getCouponCode: function () {
                        var $cupon = $(prefixClass + "coupon-code");
                        if (!$cupon.length || !$cupon.val()) {
                            return '';
                        }
                        else {
                            return $cupon.val();
                        }
                    },

                    applyCoupon: function (coupon) {
                        if (!coupon) return App.Swal.warning("Vui lòng nhập mã giảm giá");

                        var self = this;
                        var data = { coupon: coupon };
                        data.cart_type = this.cartType;
                        data.cart_id = this.cartID;

                        App.api.post(this.urls.apply_coupon, data).then(function (rs) {
                            if (rs.status) {
                                App.cart.updateCartData(rs.data);
                            } else {
                                App.Swal.error(rs.message);
                            }
                        }).catch(function (error) {

                            App.Swal.error("Lỗi không xác định");
                        })
                    },

                    checkPriceOfForm: function (form) {
                        var data = {
                            product_id: $(form).find(prefixClass + "product-order-id").val(),
                            attrs: [],
                            quantity: $(form).find(prefixClass + "product-order-quantity").val() || 1
                        };
                        var inputs = $(form).find(prefixClass + "product-varients").find('select, input[type="radio"]:checked, input[type="checkbox"]:checked');
                        for (let i = 0; i < inputs.length; i++) {
                            const inp = inputs[i];
                            data.attrs.push($(inp).val());
                        }
                        this.checkPrice(data);
                    },
                    addToCartByForm: function (form) {
                        var data = {
                            product_id: 0,
                            attrs: [],
                            quantity: 1
                        };

                        var redirect = null;
                        var inputs = $(form).serializeArray();
                        for (let i = 0; i < inputs.length; i++) {
                            const inp = inputs[i];
                            if (inp.name == "_token") continue;
                            else if (inp.name == "product_id" || inp.name == "quantity") data[inp.name] = inp.value;
                            else if (inp.name == "qty") data["quantity"] = inp.value;
                            else if (inp.name == "redirect") redirect = inp.value;
                            else data.attrs.push(inp.value);
                        }
                        this.addCartItem(data);
                    },


                    updateCartQuantity: function (data) {
                        var self = this;
                        var coupon = self.getCouponCode();
                        if (coupon) {
                            data.coupon = coupon;
                        }
                        data.cart_type = this.cartType;
                        data.cart_id = this.cartID;


                        console.log(data);
                        App.api.post(this.urls.update_cart_quantity, data).then(function (result) {
                            if (result.status) {
                                self.updateCartData(result.data);
                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (error) {
                            App.log(error);
                        });
                    },
                    updateAllCartItemQuantity: function () {
                        //var $form = $(prefixClass + "cart-form");
                        var quantity = {};
                        var inputs = $(prefixClass + "item-quantity");
                        if (inputs.length) {
                            for (let i = 0; i < inputs.length; i++) {
                                const inp = inputs[i];
                                var $inp = $(inp);
                                var id = $inp.data('item-id');
                                var qty = parseInt($inp.val());
                                if (!isNaN(qty) && qty > 0) {
                                    quantity[id] = qty;
                                }

                            }
                            if (!App.isEmpty(quantity)) {
                                this.updateCartQuantity({ quantity: quantity });
                            }
                        }
                    },

                    changeItemAttribute: function (item_id) {

                        var self = this;
                        var $itemBlock = $(prefixClass + "cart-item-" + item_id);
                        if (!$itemBlock.length) return App.Swal.warning("Lỗi không xác định");

                        var data = {
                            item_id: item_id,
                            attrs: [],
                            quantity: $itemBlock.find(prefixClass + "product-order-quantity").val() || 1
                        };
                        var inputs = $itemBlock.find('select, input[type="radio"]:checked, input[type="checkbox"]:checked');
                        for (let i = 0; i < inputs.length; i++) {
                            const inp = inputs[i];
                            data.attrs.push($(inp).val());
                        }
                        var coupon = self.getCouponCode();
                        if (coupon) {
                            data.coupon = coupon;
                        }
                        data.cart_type = this.cartType;
                        data.cart_id = this.cartID;

                        console.log(data);

                        App.api.post(this.urls.update_item, data).then(function (result) {
                            if (result.status) {
                                self.updateCartData(result.data);
                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (error) {
                            App.log(error);
                        });
                        // this.checkPrice(data);
                    },
                    currency: function (total) {
                        var c = App.config.currency;

                        return App.number.format(parseInt(total), 0, c.decimal_poiter, c.thousands_sep, c.currency_type, c.currency_position)
                    },

                    changeRegionID: function (value, text, el) {
                        if (value != this.regionID) {
                            this.regionID = value;
                            App.dom.select.deactive('billing_district_id');
                            App.dom.select.deactive('billing_ward_id');
                            App.dom.select.changeOptions('billing_district_id', { 0: "Chọn Quận / Huyện" });
                            App.dom.select.changeOptions('billing_ward_id', { 0: "Chọn Phường / Xã" });
                            if (value && value != "0") {
                                App.ajax(this.urls.district_options, {
                                    method: "get",
                                    data: { region_id: value },
                                    dataType: "JSON"
                                }).then(function (res) {
                                    if (res.status) {
                                        App.dom.select.changeOptions('billing_district_id', res.data);
                                    }
                                });
                            }
                        }
                    },

                    changeDistrictID: function (value, text, el) {
                        if (value != this.districtID) {
                            this.districtID = value;
                            App.dom.select.deactive('billing_ward_id');
                            App.dom.select.changeOptions('billing_ward_id', { 0: "Chọn Xã / Phường" });
                            if (value && value != "0") {
                                App.ajax(this.urls.ward_options, {
                                    method: "get",
                                    data: { district_id: value },
                                    dataType: "JSON"
                                }).then(function (res) {
                                    if (res.status) {
                                        App.dom.select.changeOptions('billing_ward_id', res.data);
                                    }
                                });
                            }
                        }
                    },


                    changeShippingRegionID: function (value, text, el) {
                        if (value != this.shippingRegionID) {
                            this.shippingRegionID = value;
                            App.dom.select.deactive('shipping_district_id');
                            App.dom.select.deactive('shipping_ward_id');
                            App.dom.select.changeOptions('shipping_district_id', { 0: "Chọn Quận / Huyện" });
                            App.dom.select.changeOptions('shipping_ward_id', { 0: "Chọn Xã / Phường" });
                            if (value && value != "0") {
                                App.ajax(this.urls.district_options, {
                                    method: "get",
                                    data: { region_id: value },
                                    dataType: "JSON"
                                }).then(function (res) {
                                    if (res.status) {
                                        App.dom.select.changeOptions('shipping_district_id', res.data);
                                    }
                                });
                            }
                        }
                    },

                    changeShippingDistrictID: function (value, text, el) {
                        if (value != this.shippingDistrictID) {
                            this.shippingDistrictID = value;
                            App.dom.select.deactive('shipping_ward_id');
                            App.dom.select.changeOptions('shipping_ward_id', { 0: "Chọn xã / phường" });
                            if (value && value != "0") {
                                App.ajax(this.urls.ward_options, {
                                    method: "get",
                                    data: { district_id: value },
                                    dataType: "JSON"
                                }).then(function (res) {
                                    if (res.status) {
                                        App.dom.select.changeOptions('shipping_ward_id', res.data);
                                    }
                                });
                            }
                        }
                    },


                    addBuyNowItem: function (data, redirect) {
                        if (!data || !data.product_id) return App.popup.alert("Hành động không hợp lệ!");
                        App.api.post(this.urls.buy_now_item, data).then(function (result) {
                            if (result.status) {
                                top.location.href = App.cart.urls.view_buy_now_cart;

                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (e) {
                            App.popup.alert("Lỗi ko xác định");
                        })
                    },

                    buyNowByForm: function (form) {
                        var data = {
                            product_id: 0,
                            attrs: [],
                            quantity: 1
                        };

                        var redirect = null;
                        var inputs = $(form).serializeArray();
                        for (let i = 0; i < inputs.length; i++) {
                            const inp = inputs[i];
                            if (inp.name == "_token") continue;
                            else if (inp.name == "product_id" || inp.name == "quantity") data[inp.name] = inp.value;
                            else if (inp.name == "qty") data["quantity"] = inp.value;
                            else if (inp.name == "redirect") redirect = inp.value;
                            else data.attrs.push(inp.value);
                        }
                        this.addBuyNowItem(data);
                    },

                    buyNowCombo: function (data, redirect) {
                        if (!data || !data.combo_id) return App.popup.alert("Hành động không hợp lệ!");
                        App.api.post(this.urls.buy_now_combo, data).then(function (result) {
                            if (result.status) {
                                top.location.href = App.cart.urls.view_buy_now_cart;

                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (e) {
                            App.popup.alert("Lỗi ko xác định");
                        })
                    },


                }
                // end cart
            });


            if (typeof window.orderCartInit == "function" || typeof window.customCartInit == "function") {
                if (typeof window.orderCartInit == "function") {
                    window.orderCartInit();
                }
                if (typeof window.customCartInit == "function") {
                    window.customCartInit();
                }


                App.cart.checkCartData();

                var detailClass = prefixClass + "product-detail";

                var frmSelector = detailClass + " " + prefixClass + "product-order-form";
                $(document).on("submit", frmSelector, function (e) {
                    e.preventDefault();
                    App.cart.addToCartByForm(this);
                    return false;
                });
                $(document).on("click", frmSelector + " " + prefixClass + 'btn-buy-now', function (e) {
                    e.preventDefault();
                    var form = $(this).closest(prefixClass + "product-order-form");
                    if (form.length) {
                        App.cart.buyNowByForm(form);
                    }

                    return false;
                });



                // var productOrderForms = $(frmSelector);

                // if (productOrderForms.length) {
                //     // for (let i = 0; i < productOrderForms.length; i++) {
                //     //     const form = productOrderForms[i];
                //     //     App.cart.checkPriceOfForm(form);
                //     // }
                //     // productOrderForms.change(function (e) {
                //     //     App.cart.checkPriceOfForm(this);
                //     // });

                //     productOrderForms.(function (e) {
                //         e.preventDefault();
                //         App.cart.addToCartByForm(this);
                //         return false;
                //     });

                // }

                $(document).on('click', prefixClass + "remove-cart-item", function (e) {
                    e.preventDefault();
                    try {
                        App.cart.removeCartItem($(this).data('item-id'));
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });


                $(document).on('click', prefixClass + "add-to-cart", function (e) {
                    e.preventDefault();
                    try {
                        App.cart.addCartItem({
                            product_id: $(this).data('product-id')
                        }, $(this).data('redirect'));
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });
                $(document).on('click', prefixClass + "add-combo", function (e) {
                    e.preventDefault();
                    try {
                        App.cart.addCartCombo({
                            combo_id: $(this).data('combo-id')
                        }, $(this).data('redirect'));
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });

                $(document).on('click', prefixClass + "buy-now-combo", function (e) {
                    e.preventDefault();
                    try {
                        App.cart.buyNowCombo({
                            combo_id: $(this).data('combo-id')
                        }, $(this).data('redirect'));
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });


                $(document).on('click', prefixClass + "btn-update-cart", function (e) {
                    e.preventDefault();
                    try {
                        App.cart.updateAllCartItemQuantity();
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });


                $(document).on('click', prefixClass + "btn-apply-coupon", function (e) {
                    e.preventDefault();
                    try {
                        var $cupon = $(prefixClass + "coupon-code");
                        if (!$cupon.length || !$cupon.val()) {
                            App.Swal.warning("Bạn chưa nhập mã giảm giá");
                        }
                        else {
                            App.cart.applyCoupon($cupon.val());
                        }
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });



                App.cart.cartType = $(prefixClass + "cart-form").data('cart-type');
                App.cart.cartID = $(prefixClass + "cart-form").data('cart-id');
                $(prefixClass + "cart-form").submit(function (e) {
                    e.preventDefault();
                    try {
                        App.cart.updateAllCartItemQuantity();
                    } catch (error) {
                        App.log(error)
                    }
                    return false;
                });

                $(document).on("change", prefixClass + 'product-order-quantity', function (e) {
                    App.cart.updateAllCartItemQuantity();
                });




                $(document).on("change", prefixClass + 'cart-item-attribute', function (e) {
                    // App.cart.updateAllCartItemQuantity();
                    var item_id = $(this).data('item-id');
                    App.cart.changeItemAttribute(item_id);
                });



                var createAccountCheckbox = $(prefixClass + "create-account-checkbox");
                if (createAccountCheckbox.length) {
                    var createAccountGroup = $(prefixClass + "create-account-group");
                    var toggleCreateAccountGroup = function () {
                        if (createAccountCheckbox.is(':checked')) {
                            createAccountGroup.show(300);
                        } else {
                            createAccountGroup.hide(300);
                        }
                    };
                    createAccountCheckbox.on('change', function (e) {
                        toggleCreateAccountGroup();
                    });
                    toggleCreateAccountGroup();
                }


                var shiptoDifferentAddressCheckbox = $(prefixClass + "ship-to-different-address");
                if (shiptoDifferentAddressCheckbox.length) {
                    var shiptoDifferentAddressGroup = $(prefixClass + "shipping-address-group");
                    var toggleshiptoDifferentAddressGroup = function () {
                        if (shiptoDifferentAddressCheckbox.is(':checked')) {
                            shiptoDifferentAddressGroup.show(300);
                        } else {
                            shiptoDifferentAddressGroup.hide(300);
                        }
                    };
                    shiptoDifferentAddressCheckbox.on('change', function (e) {
                        toggleshiptoDifferentAddressGroup();
                    });
                    toggleshiptoDifferentAddressGroup();
                }

                var paymentMethods = $(prefixClass + "payment-methods");

                if (paymentMethods.length) {
                    var hidePaymentMethodDescription = function () {
                        paymentMethods.find(prefixClass + "payment-method-description").removeClass('show');

                    };
                    var showPaymentMethodDescription = function (value) {
                        hidePaymentMethodDescription();
                        paymentMethods.find(prefixClass + "payment-method-description[data-method=" + value + "]").addClass('show');
                    }

                    var paymentValues = paymentMethods.find(prefixClass + "payment-method-value");
                    // hidePaymentMethodDescription();
                    paymentValues.map(function (e) {
                        if ($(e).is(":checked")) {
                            showPaymentMethodDescription($(e).val());
                        }
                    });
                    $(document).on('change', prefixClass + "payment-method-value", function (e) {
                        if ($(this).is(":checked")) {
                            showPaymentMethodDescription($(this).val());
                        }
                    });

                }
            }


            // giỏ hàng
            App.extend({
                orders: {
                    urls: {},
                    init_list: ["urls"],
                    cancel: function (id, code, success) {
                        App.api.post(this.urls.cancel, { id: id }).then(function (result) {
                            if (result.status) {
                                App.popup.alert("Đã hủy đơn hàng " + code + " thành công!");
                                if (typeof success == "function") {
                                    success();
                                }
                            } else {
                                App.popup.alert(result.message);
                            }
                        }).catch(function (err) {
                            App.popup.alert("Lỗi không xác định");
                        });

                    }
                }
                // end orders
            });


            if (typeof window.orderInit == "function") {
                if (typeof window.orderInit == "function") {
                    window.orderInit();
                }
                $(document).on('click', prefixClass + "btn-cancel-order", function (e) {
                    e.preventDefault();
                    var self = this;
                    var id = $(this).data('id');
                    var code = $(this).data('order-code');
                    App.popup.confirm("Bạn có chắc chắn muốn hủy đơn hàng " + code + " không?", function () {
                        App.orders.cancel(id, code, function () {
                            $(self).remove();
                        });
                    });
                    return false;
                });
            }


            // giỏ hàng
            App.extend({
                subcribes: {
                    urls: {},
                    init_list: ["urls"],
                    subcribe: function (email) {
                        return App.ajax(this.urls.subcribe, {
                            method: "POST",
                            dataType: "json",
                            data: { email: email }
                        }).then(function (result) {
                            if (result.status) {
                                App.popup.alert("Đăng ký theo dõi thành công!");
                                return true;
                            } else {
                                var message = '';
                                if (result.errors) {
                                    var errors = result.errors;
                                    var messages = [];
                                    for (const key in errors) {
                                        if (errors.hasOwnProperty(key)) {
                                            const error = errors[key];
                                            messages.push(error);
                                        }
                                    }
                                    message = messages.join("<br>");
                                } else {
                                    message = result.message;
                                }
                                App.popup.alert(message);
                            }
                        }).catch(function (err) {
                            App.popup.alert("Lỗi không xác định");
                        });

                    }
                }
                // end subcribes
            });


            if (typeof window.subcribeInit == "function") {
                if (typeof window.subcribeInit == "function") {
                    window.subcribeInit();
                }
                var subcribeForm = $(prefixClass + "subcribe-form");
                if (subcribeForm.length) {
                    subcribeForm.submit(function (e) {
                        e.preventDefault();
                        var self = this;
                        App.subcribes.subcribe($(this).find('input[name="email"]').val())
                            .then(function (res) {
                                $(self).find('input[name="email"]').val("");
                            });
                        return false;
                    });
                }

            }

            //contact
            App.extend({
                visitors: {
                    urls: {},
                    init_list: ["urls"],
                    tasks: [],
                    data: {},
                    // kiểm tra giá sdan3 phẩm kèm thuộc tính
                    /**
                     *
                     * @param {*} data
                     */
                    checkVisitor: function (data) {
                        var self = this;
                        App.api.post(this.urls.check, data ? data : this.data).then(function (rs) {
                            if (rs.status) {
                                if (self.tasks.length) {
                                    self.tasks.map(function (fn) {
                                        if (typeof fn == "function") {
                                            fn(rs.data);
                                        }
                                    })
                                }
                            }
                        })
                    },
                    addTask: function (fn) {
                        if (typeof fn == "function") {
                            this.tasks.push(fn);
                            return true;
                        }
                        return false;
                    }


                }
                // end cart
            });


            if (typeof window.visitorInit == "function") {
                if (typeof window.visitorInit == "function") {
                    window.visitorInit();
                    setTimeout(function () {
                        App.visitors.checkVisitor();
                    }, 1000);
                }
            }


            App.dispatchModuleLoaded();
        });
    };

    if (App.laravel && App.laravel.cache) {
        App.request.get(App.laravel.urls.token, {
            data: { key: App.str.rand() },
            dataType: "json"
        }).then(function (res) {
            if (res.status) {
                var token = res.data.token;
                App.request.setHeaders({
                    'X-CSRF-TOKEN': token
                });
                _('meta[name="csrf-token"]').attr('content', token);
                _('input[name="_token"]').val(token);
                __delay();
                __delay = null;
            } else {
                __delay();
                App.popup.warning("Có lỗi trong quá trình tải dữ liệu. Một số tính năng có thể sẽ không hoạt động");
                __delay = null;
            }
        }).catch(function (err) {
            __delay();
            App.popup.warning("Có lỗi trong quá trình tải dữ liệu. Một số tính năng có thể sẽ không hoạt động");
            __delay = null;
        });
    } else {
        __delay();
        __delay = null;
    }
}(App.query));

