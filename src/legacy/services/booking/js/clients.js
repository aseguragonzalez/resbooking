/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(function(){

    if($("#err").val()==='1'){
        $("#frmCliente").modal("show");
        var vip = $("input[name='Vip']").data("vip") === 1
                || $("input[name='Vip']").data("vip") === "1" ;
        $("input[name='Vip']").prop("checked", vip);
    }

    configurarTablaClientes();
});


/**
 * Función para configurar la tabla de clientes
 * @returns {Void}
 */
function configurarTablaClientes(){
    var language = {
       "emptyTable":     "No hay clientes",
       "info":           "Cargados del _START_ al _END_ de _TOTAL_ clientes",
       "infoEmpty":      "0 de 0 de un total de 0 clientes",
       "infoFiltered":   "(filtrados de un total de _MAX_ clientes)",
       "infoPostFix":    "",
       "thousands":      ",",
       "lengthMenu":     "Ver   _MENU_ clientes por página",
       "loadingRecords": "Cargando...",
       "processing":     "Procesando...",
       "search":         "Buscar:  ",
       "zeroRecords":    "La búsqueda no tiene resultados",
       "paginate": {
           "first":      "Primero",
           "last":       "Último",
           "next":       ">>",
           "previous":   "<<"
       },
       "aria": {
           "sortAscending":  ": activate to sort column ascending",
           "sortDescending": ": activate to sort column descending"
       }
   };

   $('#clientes').dataTable({
       language:language,
       lengthMenu: [[100, 250, 500], [100, 250, 500]]
    }).removeClass("hide");
}


/**
 * Función para la actualización del tipificado de cliente como VIP
 * @param {Object} obj Referencia al control
 * @returns {Voi}
 */
function actualizarVip(obj){

    var url = $("#current_path").val() + "/Clients/SetVip/" + $(obj).data("id");

    $.post(url, function(data){
        var message = "No se ha podido ejecutar la operación. Inténtelo más tarde";
        if(data.Result >= 0){
            var vip = ($(obj).data("vip") === "1" || $(obj).data("vip") === 1 )
                ? 0 : 1;
            $(obj).data("vip", vip);
            $(obj).attr("data-vip", vip);
            $(".btn-editar[data-id='" + $(obj).data("id") + "']").data("vip", vip);
            message = "Información actualizada correctamente.";
        }
        visualizarResultado(message, data.Result >= 0);
    }).error(function(data){
        var message = "Se ha producido un error interno. Inténtelo más tarde.";
        visualizarResultado(message, false);
    });
}

/**
 * Función para parametrizar el modal de eliminación de cliente
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function abrirEliminar(obj){
    $("#btnEliminar").data("id", $(obj).data("id"));
}

/**
 * Función para lanzar el proceso de eliminación
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function eliminarCliente(obj){
    var url = $("#current_path").val() + "/Clients/Delete/"
           + $(obj).data("id");
   $.post(url, function(data){
       var message = "Se ha producido un problema. Inténtelo más tarde";
       var success = false;
       if(data !== undefined && data.Result === 0){
           message = "La operación se ha realizado correctamente.";
           success = true;
           obj = $(".btn-eliminar[data-id='" + $(obj).data("id") + "']");
           eliminarFila(obj);
           $("#frmCliente").modal("hide");
       }
       visualizarResultado(message, success);
       $("#eliminar").modal("hide");
   }).error(function(data){
       var message = "Se ha producido un error. Inténtelo más tarde";
       visualizarResultado(message, false);
   });

}

/**
 * Función para limpiar los errores del formulario
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function LimpiarFicha(obj){
    $.each($(".has-error").find(".help-block"), function(i,o){
       $(o).text($(o).data("msg"));
    });
    $(".has-error").removeClass("has-error");
    $.each($(".help-block"), function(i,o){
       $(o).text($(o).data("msg"));
    });
}

/**
 * Función para la apertura de nueva ficha de cliente
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function nuevaFicha(obj){
    LimpiarFicha(obj);
    $("input[name='Id']").val("");
    $("input[name='Name']").val("");
    $("input[name='Phone']").val("");
    $("input[name='Email']").val("");
    $("input[name='Vip']").prop("checked", false);
    $("textarea[name='Comments']").val("");
    $("#btnEliminarFicha").addClass("hide").data("id",0);
    $("input[name='Advertising']").prop("checked", false);
}

/**
 * Función para editar una ficha de cliente
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function editarFicha(obj){
    LimpiarFicha(obj);
    $("input[name='Id']").val($(obj).data("id"));
    $("input[name='Name']").val($(obj).data("name"));
    $("input[name='Phone']").val($(obj).data("phone"));
    $("input[name='Email']").val($(obj).data("email"));
    $("input[name='Vip']").prop("checked", $(obj).data("vip") === 1
            || $(obj).data("vip") === "1" );
    $("textarea[name='Comments']").val($(obj).data("comments"));
    $("#btnEliminarFicha").removeClass("hide").data("id",$(obj).data("id"));

    $("input[name='Advertising']").prop("checked",
        $(obj).data("advertising") === 1
            || $(obj).data("advertising") === "1" );
    $("#historial").html("");
    var url = $("#current_path").val() + "/Clients/GetClient/" + $(obj).data("id");
    $.get(url,function(data){
        if(data.Error === false){
            if(data.Result.Bookings !== undefined){
                $.each(data.Result.Bookings, function(i,o){
                    var state = $("<span />").attr("data-state", o.State).text(" | " + o.StateName);
                    var msg = o.Date + ", " + o.Diners + " comensales. ";
                    $("#historial").append($("<li />").text(msg).append(state));
                });
            }
        }
    });
}
