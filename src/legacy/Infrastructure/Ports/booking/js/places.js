

/**
 * Función para limpiar los mensajes de error generados previamente
 * @returns {Void}
 */
function limpiarErrores(){
    $.each($(".has-error").find(".help-block"), function(i,o){
       $(o).text($(o).data("msg"));
    });
    $(".has-error").removeClass("has-error");
}

/**
 * Función para preparar el formulario para una nueva sala
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function nuevoEspacio(obj){
    limpiarErrores();
    $("[name='Id']").val(0);
    $("[name='Name']").val("");
    $("[name='Description']").val("");
}

/**
 * Función para cargar los datos de la sala en el formulario de edición
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function editarEspacio(obj){
    limpiarErrores();
    $("[name='Id']").val($(obj).data("id"));
    $("[name='Name']").val($(obj).data("name"));
    $("[name='Description']").val($(obj).data("desc"));
}

/**
 * Función para generar la petición para eliminación de una sala
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function eliminarEspacio(obj){
    window.location=$(obj).data("url") + $(obj).data("id");
}

/**
 * Manejador del evento onload
 * @returns {Void}
 */
$(function(){
    if($("#err").val() === "1"){
        $("#formulario").modal("show");
    }

    if($("#resultado").text() !== ""){
        $("#resultado").parent().removeClass("hide");
    }
});
