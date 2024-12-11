

/**
 * Función para limpiar los errores del formulario
 * @returns {Void}
 */
function limpiarErrores(){
    $.each($(".has-error").find(".help-block"), function(i,o){
       $(o).text($(o).data("msg"));
    });
}

/**
 * Función para obtener la configuración de turnos de una oferta
 * y establecer los controles del formulario
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function obtenerConfiguracion(obj){
    $("#turnos").find("input").prop("checked", false);
    var url = $("#current_path").val()
            + "/Offers/GetConfig/" + $("#offer").val();
    $.getJSON(url, function(data){
        if(data.Error === false && $.isArray(data.Result)){
            $.each($(data.Result), function(i,o){
                var selector = "input[data-turn='" + o.Turn
                    + "'][data-dow='" + o.Day + "']";
                $(selector).data("id", o.Id).prop("checked", true);
            });
        }
    });
}

/**
 * Función para guardar la información de una configuración relativa
 * a una oferta.
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function guardarConfiguracion(obj){
    var url = $("#current_path").val() + "/Offers/SetConfig";
    $.post(url, {
            Id:$(obj).data("id")
            ,Offer:$(obj).data("offer")
            ,Day:$(obj).data("dow")
            ,Slot:$(obj).data("slot")
            ,Turn:$(obj).data("turn")
        }, function(data){
        if(data.Error === false){
            $(obj).data("id", data.Result);
            $(obj).prop("checked",(data.Result !== 0));
        }
    });
}

/**
 * Función para eliminar una oferta.
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function eliminarOferta(obj){
    var url = $(obj).data("url") + $(obj).data("id");
    window.location=url;
}

function getDateTime(sDate){
    // yyyy-mm-dd hh:ii:ss
    if(sDate !== undefined && sDate !== ""){
        var arr = sDate.split(" ");
        var arr2 = arr[0].split("-");
        return new Date(parseInt(arr2[0]),
            parseInt(arr2[1])-1, parseInt(arr2[2]));
    }
    return null;
}

/**
 * Función para cargar el formulario de edición de oferta
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function editarOferta(obj){
    limpiarErrores();
    $("[name='Id']").val($(obj).data("id"));
    $("[name='Title']").val($(obj).data("title"));
    $("[name='Description']").val($(obj).data("desc"));
    $("[name='Terms']").val($(obj).data("terms"));
    $("[name='Start']").val("");
    $("[name='End']").val("");
    var start = getDateTime($(obj).data("start"));
    if(!isNaN(start.getYear()) && start.getFullYear() > 2000){
        $("[name='Start']").datepicker("setDate",start);
    }
    var end = getDateTime($(obj).data("end") );
    if(!isNaN(end.getYear())&& end.getFullYear() > 2000){
        $("[name='End']").datepicker("setDate",end);
    }
    $("[name='Web']").prop("checked", $(obj).data("web") === 1
            || $(obj).data("web") === "1" );
}

/**
 * Función para abrir el formulario de nueva oferta
 * @returns {Void}
 */
function nuevaOferta(){
    limpiarErrores()
    $("form").find("input[type='text']").val("");
    $("form").find("input[type='checkbox']").prop("checked", false);
    $("form").find("textarea").val("");
    $("#Id").val(0);
}

/**
 * Función para configurar los controles de turnos
 * @returns {Void}
 */
function configurarTurnos(){
    $.each($(".tr_turno"),function(i,o){
        var turno = $(o).data("id");
        $.each($(o).data("days"),function(index, item){
            var selector = "input[data-turn='" + turno
                    + "'][data-dow='" + item + "']";
            $(selector).removeAttr("disabled").parent().removeAttr("title");
        });
    });
}

/**
 * Gestión del evento onready. Inicializa las propiedades y controles
 * de la pantalla
 * @returns {Void}
 */
$(function(){

    configurarTurnos();

    $(".calendar").datepicker({dateFormat: 'dd-mm-yy'});

    $.each($(".calendar"), function(i,o){
        var value = $(o).data("value");
        var date = new Date(value);
        if(!isNaN(date.getYear())){
            $(o).datepicker("setDate", date);
        }
    });

    if($("#err").val() === "1"){
        $("#formulario").modal("show");
    }

    if($("#resultado").text() !== ""){
        $("#resultado").parent().removeClass("hide");
    }
});
