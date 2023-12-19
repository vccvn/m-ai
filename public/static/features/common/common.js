// coloris
App.coloris = {
    swatches: [],
    add: function(selector){
        var self = this;
        var swatches = [];
        var $el = $(selector);
        if($el.length){
            $el.each(function(i, e){
                var s = $(e).data('swatches');
                if(s){
                    var b = JSON.parse(s);
                    if(b && b.length){
                        swatches = b;
                    }else{
                        swatches = self.swatches;
                    }
                }else{
                    swatches = self.swatches;
                }
            })
            Coloris({
                el: selector,
                swatches: swatches
            });
        }
    }
};

if(typeof coloris_swatches != "undefined"){
    App.coloris.swatches= coloris_swatches;
}
$(function(){
    App.coloris.add('.coloris, [data-coloris]');
});