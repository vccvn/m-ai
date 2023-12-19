function addRangeSliderInput(el){
    var $el = $(el);
    $el.find('.hidden-range-input').ionRangeSlider({
        type:"double",
        grid:!0,
        min:Number($el.data('min')),
        max:Number($el.data('max')),
        from:Number($el.data('from')),
        to:Number($el.data('to')),
        prefix:$el.data('prefix')?$el.data('prefix'):''
    });
}
$(function(){
    $('.crazy-range-slider').each(function(i, el){
        addRangeSliderInput(el);
    });
})
