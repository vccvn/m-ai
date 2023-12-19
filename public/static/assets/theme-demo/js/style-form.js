$(function () {

    function StyleForm(selector, templateData) {
        var mapData = {};
        var $el, $components, $preview, $items;

        isFirst = false;
        var self = this;
        this.setData = function (data) {
            if (typeof templateData != "object") return false;
            mapData = {};
            mapData.itemConfigs = {};
            if (data.itemConfigs) {
                for (let index = 0; index < data.itemConfigs.length; index++) {
                    const itemConfig = data.itemConfigs[index];
                    var itemMap = {};
                    if (itemConfig.templateItems && itemConfig.templateItems.length) {
                        itemConfig.templateItems.map(function (item) {
                            itemMap[item.id] = item;
                        })
                    }
                    itemConfig.items = itemMap;

                    mapData.itemConfigs[itemConfig.id] = itemConfig;
                }
            }
        };
        this.getData = function () {
            return mapData;
        };

        this.setSelector = function (s) {
            $el = $(s);
            $components = $el.find('.style-form-components');
            $preview = $components.find('.style-preview');
            $items = $components.find('.style-items');
            this.addEvents();
        };

        this.changeItemImage = function (itemConfigId, templateItemId) {
            if (
                typeof mapData == "object" &&
                typeof mapData.itemConfigs == "object" &&
                typeof mapData.itemConfigs[itemConfigId] == "object" &&
                typeof mapData.itemConfigs[itemConfigId].items == "object" &&
                typeof mapData.itemConfigs[itemConfigId].items[templateItemId] == "object"
            ) {
                var item = mapData.itemConfigs[itemConfigId].items[templateItemId];

                $('#preview-item-back-' + itemConfigId).html('<img src="' + item.back_image_url + '" />');
                $('#preview-item-front-' + itemConfigId).html('<img src="' + item.front_image_url + '" />');
            }
        };
        this.init = function (data) {
            this.setData(data ? data : templateData);
            this.setSelector(selector);

            if (!isFirst) {
                isFirst = true;
                $('.style-form-components .style-items .template-item input[type=radio]:checked').each(function (i, inp) {
                    var $this = $(inp).closest('.template-item');
                    var itemConfigId = $this.data('item-config-id'), templateItemId = $this.data('item-id');
                    self.changeItemImage(itemConfigId, templateItemId);
                })
                $(document).on("change", '.style-form-components .style-items .template-item input[type=radio]', function (event) {
                    var $this = $(this).closest('.template-item');
                    var itemConfigId = $this.data('item-config-id'), templateItemId = $this.data('item-id');
                    self.changeItemImage(itemConfigId, templateItemId);
                })
                var activeTab = $('.style-items .tab-nav>ul>li>a.active');
                var iid = activeTab.data('id')
                var activedList = [
                    iid
                ];
                if(iid == 'body-shape'){
                    this.addShapeSlide($(activeTab.attr('href')))
                }else{
                    this.addMobileSlide('.tab-contents .tab-item.active .style-item-slides .slides');
                }
                
                
                $(document).on("click", ".style-items .tab-nav>ul>li>a", function (event) {
                    event.preventDefault();
                    var $this = $(this);
                    if ($this.hasClass('active')) return false;
                    $this.closest('.tab-nav>ul').find('li>a').removeClass('active');
                    $this.addClass("active");
                    var tabId = $this.attr('href');
                    var itemId = $this.data('id');
                    var $styleItems = $this.closest('.style-items');
                    $styleItems.find(".tab-contents .tab-item").removeClass('active');
                    var $activeTab = $styleItems.find(tabId);
                    if (activedList.indexOf(itemId) == -1) {
                        activedList.push(itemId);
                        if (itemId == 'body-shape') {
                            self.addShapeSlide($(tabId));
                        }
                        else {
                            var $slides = $(tabId + ' .style-item-slides .slides');
                            if ($slides.length) {
                                var html = $slides.html();

                                $slides.html('<div class="alert alert-success text-center">Đang tải... </div>');
                                $activeTab.addClass('active');
                                setTimeout(function () {
                                    $slides.html(html);
                                    self.addMobileSlide($slides[0]);
                                }, 200);

                            } else {
                                $activeTab.addClass('active');
                            }
                        }
                    } else {
                        $activeTab.addClass('active');
                    }


                    return false;
                });



            }
            $el.on("click", '.btn-save-style', function (event) {
                event.preventDefault();
                var name = $(this).data('name');
                self.saveOnMobile(name);
                return false;
            });



            $el.on("click", ".attr-nav-item", function (event) {
                event.preventDefault();
                var $wrapper = $(this).closest('.attribute-wrapper');

                var $nav = $(this).closest('.attribute-nav');
                $wrapper.find('.attribute-item-block').hide();
                var isActive = $(this).hasClass('active');
                $nav.find('.attr-nav-item').removeClass('active');
                if (isActive) return false;
                $(this).addClass('active');
                $wrapper.find($(this).attr('href')).show(300)

            });

            $el.find('.body-size-groups input[type="range"]').each(function (i, el) {
                var $el = $(el);
                var $output = $el.closest(".col-input").find(".output span");
                $el.rangeslider({
                    polyfill: false,
                    onInit: function () {
                        $output.html($el.val());
                    },
                    onSlide: function (position, value) {
                        //console.log('onSlide');
                        // console.log('position: ' + position, 'value: ' + value);
                        $output.html(value);
                    },
                    onSlideEnd: function (position, value) {
                        //console.log('onSlideEnd');
                        //console.log('position: ' + position, 'value: ' + value);
                    }
                });
            });

            $('.btn-show-edit-form').on('click', function(e){
                e.preventDefault();
                var $wrapper = $('.section-personal-style-form');
                $wrapper.removeClass('hidden-form').addClass('hidden-header')
            });
        }

        this.addEvents = function () {
            // $items.on("click", ".template")
        };

        this.addMobileSlide = function (select) {
            var $slider = $(select);
            if ($slider.length) {
                $slider.slick({
                    dots: false,
                    infinite: false,
                    speed: 500,
                    arrows: false,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1630,
                            settings: {
                                slidesToShow: 4,
                            },
                        },
                        {
                            breakpoint: 1366,
                            settings: {
                                slidesToShow: 3,
                            },
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                slidesToShow: 3,
                            },
                        }
                    ]
                });
            }
        };

        this.addShapeSlide = function (elem) {
            var shapeData = elem.find('.body-shape-groups').html();
            console.log(shapeData)
            elem.find('.body-shape-groups').html('');
            setTimeout(() => {
                elem.find('.body-shape-groups').html(shapeData);
                setTimeout(() => {
                    elem.find('.body-shape-groups .body-shape-sliders').slick({
                        dots: false,
                        infinite: false,
                        speed: 500,
                        arrows: false,
                        slidesToShow: 5,
                        slidesToScroll: 1,
                        responsive: [
                            {
                                breakpoint: 1200,
                                settings: {
                                    slidesToShow: 5,
                                },
                            },
                            {
                                breakpoint: 992,
                                settings: {
                                    slidesToShow: 5,
                                },
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 4,
                                },
                            },
                            {
                                breakpoint: 576,
                                settings: {
                                    slidesToShow: 3,
                                },
                            },
                            {
                                breakpoint: 375,
                                settings: {
                                    slidesToShow: 2,
                                },
                            }
                        ]
                    });
                }, 50);
            }, 500);
            
        };

        this.saveOnMobile = async function () {
            const { value: StyleName } = await Swal.fire({
                title: 'Lưu Style',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                inputPlaceholder: "Tên Style",
                inputValue: $el.find('#input-style-name').val(),
                showCancelButton: false,
                confirmButtonText: 'Xác nhận',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                customClass: {
                    container: "save-style-confirm",
                    input: 'form-control',
                    confirmButton: 'btn btn-theme btn-colored-default',
                    title: "popup-title"
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Vui lòng nhập tên style'
                    }
                }
            });
            if (StyleName) {
                $el.find('#input-style-name').val(StyleName);
                App.Swal.showLoading();
                $el.submit();
            }

        }
    }

    var styleForm = new StyleForm('#personal-style-set-form', window.style_template_data);
    styleForm.init();
    App.extend({
        styleForm: styleForm
    })



    function customFileChoose() {
        $(".custom-file-input").off();
        $(document).on("change", ".custom-file-input", function () {
            var t = $(this).val();
            var self = this;
            $(this).next(".custom-file-label").addClass("selected").html(t);

            var onc = $(self).data('on-change');
            var files = this.files;
            var callback = function (fs) {
                if (!fs) fs = [];
                if (onc) {
                    let oncbs = onc.split(',');
                    if (oncbs.length > 1) {
                        oncbs.forEach(element => {
                            let func = element.trim();
                            if (App.func.check(func)) {
                                App.func.call(func, [self, fs]);
                            }
                        });

                    }
                    else if (App.func.check(onc)) {
                        App.func.call(onc, [self, fs]);
                    }

                }
            };
            if (window.File && window.FileList && files && files.length) {
                var list = [];
                var lsName = [];
                let max = files.length - 1;
                for (var i = 0; i < files.length; i++) {
                    let file = files[i];
                    lsName.push(file.name);
                    if (onc && window.FileReader) {
                        (function (file, index, coumt) {
                            let fileReader = new FileReader();
                            fileReader.onload = function (f) {
                                let src = f.target.result;
                                let data = {
                                    filename: file.name,
                                    size: file.size,
                                    data: src
                                };

                                list.push(data);
                                if (index == coumt) {
                                    callback(list);
                                }
                            };
                            fileReader.readAsDataURL(file);
                        })(file, i, max);
                    }
                    if (i == max) {
                        $(self).next(".custom-file-label").addClass("selected").html(lsName.join(', '));
                    }

                }
            } else {
                callback([]);
            }
        });
    }

    customFileChoose();
});