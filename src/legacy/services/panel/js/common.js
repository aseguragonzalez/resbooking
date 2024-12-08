/**
 * Establece el item seleccionado en los controles select
 * @returns {Void}
 */
function setSelectedItem(){
    $.each($( "select" ), function(index, item){
        var a = $(item).data( "selected" );
        if( a !== undefined && a !== "" && a !== "0"){
            $( item ).val( a );
            $( item ).find( "option[value='" + a + "']" )
                    .attr( "selected", "selected" );
        }
    });
}

/**
 * Establece el proyecto actual
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function setCurrentProject(obj){
    var id = parseInt($(obj).val());
    if($.isNumeric(id) && id > 0){
        var url = $("#current_path").val()
                + "/Home/SetProject/" + id;
        window.location = url;
    }
}

/**
 * Ejecuta la navegación del enlace en el iframe indicado
 * @param {Object} obj Referencia al control
 * @returns {Boolean}
 */
function loadUrl(obj){
    var url = $(obj).data("path") + $(obj).data("url");
    if(url !== ""){
        var target = $(obj).data("target");
        $(target).attr("src", url);
    }
    return false;
}

/**
 * Cierre de la sesión en los servicios activos
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function logout(obj){
    var count = $(".service").length - 1;
    $.each($(".service"), function(i,o){
        $("#main-container").attr("src", $(o).data("url") + "/Accounts/Logout");
        //$.get($(o).data("url") + "/Accounts/Logout");
        if(i === count){
            window.location = $("#current_path").val() + "/Accounts/Logout";
        }
    });
    return false;
}

/**
 * Proceso de carga del servicio en el iframe objetivo
 * @param {Object} obj Referencia al control de servicio
 * @returns {Boolean}
 */
function loadService(obj){

    var randValue = (new Date()).getTime() + Math.floor(Math.random() * 1000000);

    // Establecer la url del servicio
    var url = $(obj).data("url") + "/Home/SetProject/" + $("#projects").val()
        + "?&ticket=" + $(obj).data("ticket") + "&r=" + randValue;
    // establecer la url en el iframe
    var target = $(obj).data("target");
    $(target).attr("src", url);
    // bloquear navegación posterior
    return false;
}

/**
 * Proceso de actualización de todos los bullet de servicio
 * @returns {Void}
 */
function updateBullets(){
    $.each($(".service"), function(i,o){
        getBulletValue(o);
    });
}

/**
 * Establece el valor del bullet/badge del control de selección
 * de servicio referenciado
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function getBulletValue(obj){
    var url = $("#current_path").val()
            + "/Home/GetBullet/" + $(obj).data("service");
    $.get(url,function(json){
        if(json.Error === false){
            $(obj).find(".badge").attr("data-count", json.Result).text(json.Result);
        }
    });
}

/**
 * Proceso de actualización del ticket de autenticación en los controles
 *
 * @returns {undefined}
 */
function updateTicket(){
    var url = $("#current_path").val()
            + "/Home/GetTicket";
    $.get(url,function(json){
        if(json.Error === false){
            $.each($(".service"),function(i,o){
               $(o).data("ticket", json.Result);
            });
        }
    });
}

/**
 * Manejador del evento onload
 * @returns {Voi}
 */
$(function(){
    // Configurar los controles Select
    setSelectedItem();
    //  Proceso para actualizar el ticket
    window.setInterval("updateTicket()", 100000);
    // Proceso para actualizar los bullet
    window.setInterval("updateBullets()", 50000);

    updateBullets();
});
