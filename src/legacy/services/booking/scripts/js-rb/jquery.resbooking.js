var _DEFAULT_URL_ = "http://des-admin.resbooking.es/Book/Index/";
var _DEFAULT_CSS_CLASS_ = "content";
$(function(){
    $.fn.extend({
        resbooking : function(options){
            // Combinar los parámetros pasados con los por defecto
            var opt = $.extend({
                url:_DEFAULT_URL_,
                id:0,
                cssClass: _DEFAULT_CSS_CLASS_
            }, options);
            // Establecer la url del iframe
            var url = opt.url + opt.id;
            // Crear iframe
            var iframe = $( "<iframe />" )
                    .attr( "src", url)
                    .addClass( opt.cssClass );
            // Agregar iframe
            $(this).append(iframe);
        }
    });
});
