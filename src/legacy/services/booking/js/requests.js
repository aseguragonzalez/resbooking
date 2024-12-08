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
