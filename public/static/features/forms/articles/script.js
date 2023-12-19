$(function(){
    const ArticleItem = function ArticleItem(el) {
        var $el = $(el);
        this.$el = $el;
        var self = this;
        this.init = function init() {
            $el.find('textarea').each(function(i, r){
                addCkeditor(r);
            });

            $el.on("click", ".btn-remove-article", function(e){
                e.preventDefault();
                $el.hide(300, function(){
                    $(this).remove();
                });
                return false;
            })
        }
    };

    const ArticleInputManager = function ArticleInputManager(el) {
        var $el = $(el);
        this.$el = $el;
        this.maxIndex = $el.data('max-index');
        this.template = $el.find('script').html();

        var self = this;
        this.init = function init() {
            let items = $el.find('.article-item').each(function(i, e){
                let item = new ArticleItem(el);
                item.init();
                return item;
            });
            $el.on("click", ".btn-add-article", function(e){
                e.preventDefault();
                self.addItem();
                return false;
            })
        };
        this.addItem = function addItem() {
            var itemHtml = App.str.eval(this.template, {index: this.maxIndex});
            $el.find('.list').append(itemHtml);
            var el = $el.find('.item-' + this.maxIndex);
            let item = new ArticleItem(el);
                item.init();
            this.maxIndex++;

        }
    }

    App.addArticleInput = function addArticleInput(el) {
        var ai=new ArticleInputManager(el);
        ai.init();
        return ai;
    };

    $('.input-article-wrapper').each(function(i, el){
        App.addArticleInput(el);
    })

})
