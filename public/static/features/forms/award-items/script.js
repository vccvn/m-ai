$(function () {
    const AwardItem = function AwardItem(el) {
        var $el = $(el);
        this.$el = $el;
        var self = this;
        this.init = function init() {
            // $el.find('textarea').each(function (i, r) {
            //     addCkeditor(r);
            // });

            $el.on("click", ".btn-remove-award-item", function (e) {
                e.preventDefault();
                $el.hide(300, function () {
                    $(this).remove();
                });
                return false;
            })
        }
    };

    const AwardInputManager = function AwardInputManager(el) {
        var $el = $(el);
        this.$el = $el;
        this.maxIndex = $el.data('max-index');
        this.template = $el.find('script').html();

        var self = this;
        this.init = function init() {
            let items = $el.find('.award-item').each(function (i, e) {
                let item = new AwardItem(el);
                item.init();
                return item;
            });
            $el.on("click", ".btn-add-award-item", function (e) {
                e.preventDefault();
                self.addItem();
                return false;
            })
        };
        this.addItem = function addItem() {
            var itemHtml = App.str.eval(this.template, { index: this.maxIndex });
            $el.find('.list').append(itemHtml);
            var el = $el.find('.item-' + this.maxIndex);
            let item = new AwardItem(el);
            item.init();
            this.maxIndex++;

        }
    }

    App.addAwardInput = function addAwardInput(el) {
        var ai = new AwardInputManager(el);
        ai.init();
        return ai;
    };

    $('.input-award-items-wrapper').each(function (i, el) {
        App.addAwardInput(el);
    })

})
