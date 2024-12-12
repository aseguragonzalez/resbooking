

/**
 * Función para establecer el estado de la reserva como "aceptada"
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function aceptarReserva(obj){
   var url = $("#current_path").val() + "/Requests/Accept/"
           + $(obj).data("id");
   $.post(url, function(data){
       var message = "Se ha producido un problema. Inténtelo más tarde";
       var success = false;
       if(data !== undefined && data.Result === 0){
           message = "La operación se ha realizado correctamente.";
           success = true;
           eliminarFila(obj);
       }
       visualizarResultado(message, success);
   }).error(function(data){
       var message = "Se ha producido un error. Inténtelo más tarde";
       visualizarResultado(message, false);
   });
}

/**
 * Función para establecer el estado de la reserva como "anulada"
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function anularReserva(obj){
   var url = $("#current_path").val() + "/Requests/Cancel/"
           + $(obj).data("id");
   $.post(url, function(data){
       var message = "Se ha producido un problema. Inténtelo más tarde";
       var success = false;
       if(data !== undefined && data.Result === 0){
           message = "La operación se ha realizado correctamente.";
           success = true;
           eliminarFila($("a[data-id='"+ $(obj).data("id") +"']"));
       }
       $("#anular").modal("hide");
       visualizarResultado(message, success);
   }).error(function(data){
       var message = "Se ha producido un error. Inténtelo más tarde";
       visualizarResultado(message, false);
   });
}
