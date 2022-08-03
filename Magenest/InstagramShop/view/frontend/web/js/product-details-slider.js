define(['jquery','slick'],function($){
    $.widget('magenest.productDetailSlider',{
        options: {
            arrows: true,
            center: false,
            dots: false,
            draggable: false,
            infinite: false,
            lazyLoad: true,
        },

        _create: function(){
            this._initSlider();
        },

        _initSlider: function(){
            var self = this;
            $(this.element).slick(self.options);
        },

    });
    return $.magenest.productDetailSlider;
});