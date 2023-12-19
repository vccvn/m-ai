if (typeof Product == "undefined") {
    Product = {};
}

Product.form = {
    getAttrInputUrl: '',
    jsTagSrc: '',
    onSale: false,
    removeGroupsClasss: function (groups, groupClass, time) {
        if (typeof time == "undefined") time = 0;
        if (time) {
            for (let i = 0; i < groups.length; i++) {
                const group = groups[i];
                setTimeout(function () {
                    $('#' + group + '-form-group').removeClass(groupClass);
                }, time + i * 50);

            }
            return true;
        }
        for (let i = 0; i < groups.length; i++) {
            const group = groups[i];
            $('#' + group + '-form-group').removeClass(groupClass);
        }
        return true;

    },
    addGroupsClass: function (groups, groupClass, time) {
        if (typeof time == "undefined") time = 0;
        if (time) {
            for (let i = 0; i < groups.length; i++) {
                const group = groups[i];
                setTimeout(function () {
                    $('#' + group + '-form-group').addClass(groupClass);
                }, time + i * 50);

            }
            return true;
        }
        for (let i = 0; i < groups.length; i++) {
            const group = groups[i];
            $('#' + group + '-form-group').addClass(groupClass);
        }
        return true;
    },
    getTagsHaveToggle: function (togglePrefix) {
        var toggle = $('.crazy-form .toggle-by-' + togglePrefix);
        return toggle.length ? toggle : null;
    },
    addHiddenClassBy: function (togglePrefix) {
        var toggle = this.getTagsHaveToggle(togglePrefix);
        if (toggle) toggle.addClass('hide-by-' + togglePrefix);
    },
    removeHiddenClassBy: function (togglePrefix) {
        var toggle = this.getTagsHaveToggle(togglePrefix);
        if (toggle) toggle.removeClass('hide-by-' + togglePrefix);
    },
    checkOnSaleStatus: function (stt) {
        if (stt) {
            this.removeHiddenClassBy('on-sale-status');
        } else {
            this.addHiddenClassBy('on-sale-status');
        }
    },

    checkOnPriceStatus: function (value) {
        if (value != "-1"){
            this.removeHiddenClassBy('price-status');
            $('#list_price').prop("required", true);
        }else{
            this.addHiddenClassBy('price-status');
            
            $('#list_price').prop("required", false);
        }
    },

    changeCategory: function (id) {
        let product_id = $('.crazy-form #hidden-id').val();
        let data = {
            category_id: id,
            product_id: product_id
        };
        var self = this;
        App.api.get(this.getAttrInputUrl, data).then(function (rs) {
            if (rs.status) {
                let category_group = $('#attributes-form-group .m-accordion__item');
                if (category_group.length) {
                    category_group.addClass('d-none');
                }

                if (rs.data) {
                    for (let i = 0; i < rs.data.length; i++) {
                        const attr = rs.data[i];
                        let code = attr.html_code;

                        if (attr.input_group == 'variants') {
                            let variantexistsGroup = $('#product-variants #product-variants_' + attr.input_name);
                            if (variantexistsGroup.length) {
                                variantexistsGroup.removeClass('d-none');
                                // existsGroup.remove();
                            }
                            else {
                                $('#product-variants').append(code);
                                if (attr.input_type == 'tags') {
                                    self.addTagInput(attr.input_name);
                                }
                            }
                        }
                        else {
                            let existsGroup = $('#product-attributes_' + attr.input_group + ' ' +'#product-attributes-' + attr.input_group + '_' + attr.input_name);
                            if (existsGroup.length) {
                                existsGroup.removeClass('d-none');
                                // existsGroup.remove();
                            }
                            else {
                                $('#product-attributes_' + attr.input_group).append(code);
                                if (attr.input_type == 'tags') {
                                    self.addTagInput(attr.input_name);
                                }
                            }
                        }

                    }
                }
            }
        }).catch(function (res) {
            cl(res);
        });
    },

    addTagInput: function (name) {
        let $input = $('#attributes-' + name + '.crazy-tag');
        if (!App.tagInput) {
            this.addTagLib();
            // setTimeout(function(){
            //     App.tagInput.add($input);
            // }, 300);
        } else {
            App.tagInput.add($input);
        }
    },

    addTagLib: function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.src = this.jsTagSrc;
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    },

    onVariantChange: function (status, el) {
        var value_id = $(el).data('value');
        var $wrapper = $('#product-variant-value-' + value_id);
        if ($wrapper.length) {
            var $option = $wrapper.find('.variant-option');
            if (status) {
                $option.addClass('show');
            } else {
                $option.removeClass('show');
            }
        }

    },
    checkType: function (value) {
        if (value == 'digital') {
            $('#download_url-form-group').removeClass('d-none');
        } else {
            $('#download_url-form-group').addClass('d-none');
        }
    }
};



$(function () {
    if (crazy_form_data) {
        if (crazy_form_data.attributes) {
            let attributes = crazy_form_data.attributes;
            Product.form.getAttrInputUrl = attributes.input_url;
            Product.form.jsTagSrc = attributes.tag_srx;

        }
    }
    if ($('.crazy-form').length) {
        if ($('input#on_sale').length) {
            Product.form.checkOnSaleStatus($('input#on_sale').is(':checked'));
        }
        var variantAttributes = $('.product-variant-input .variant-input-checkbox');
        if (variantAttributes.length) {
            variantAttributes.each(function (i, el) {
                Product.form.onVariantChange($(el).is(':checked'), el);
            });
        }
    }


})