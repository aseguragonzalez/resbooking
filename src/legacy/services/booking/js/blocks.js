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
 * Proceso para actualizar la información de un bloqueo
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function setBlock(obj){
   var url = $("#current_path").val()
           + "/Blocks/SetBlock";
   var state = $(obj).is(":checked");
   var dto = {
       Year: $("#year").val(),
       Week: $("#week").val(),
       DayOfWeek: $(obj).data( "dayofweek" ),
       Turn: $(obj).data( "turn" ),
       Date: $(obj).data( "date" ),
       Id: $(obj).data("id"),
       Block: (state === true) ? 1:0
   };
   $.post(url, dto, function(data){
       if(data.Error === false){
           $(obj).data("id", data.Result);
       }
       else{
           $(obj).prop("checked", !state);
       }
   }).error(function(data){
       $(obj).prop("checked", !state);
   });
}

/**
 * Configuración de los bloqueos existentes
 * @param {Function} callback Función de callback
 * @returns {Void}
 */
function setCurrentBlocks(callback){
   var items = $.parseJSON($("#blocks").val());
   $.each($(items), function(i,o){
       var selector = "input[data-date='" + o.Date + "']"
           + "[data-turn='" + o.Turn + "']";
       $(selector).prop("checked", o.Block === 1);
       $(selector).data("id", o.Id);
   });

   if($.isFunction(callback)){
       callback();
   }
}

/**
 * Configuración de la línea base de turnos
 * @param {Function} callback Referencia a la función de regreso
 * @returns {Void}
 */
function setBaseLine(callback){
   var items = $.parseJSON($("#configs").val());
   $.each($(items), function(i,o){
       var selector = "input[data-dow='" + o.Day + "']"
           + "[data-turn='" + o.Turn + "']";
       $(selector).prop("checked", true);
   });
   if($.isFunction(callback)){
       callback();
   }
}

/**
 * Bloquear los días pasados
 * @returns {Void}
 */
function blockEvents(){
    var yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    $.each($("input[type='checkbox']"), function(i,o){
        var sdate = $( o ).data("date");
        var date = new Date(sdate);
        if(yesterday > date){
            $(o).prop("disabled", true);
        }

    });
}

/**
 * Manejador del evento onload
 * @returns {Void}
 */
$(function(){
   // Configurar la línea base
   setBaseLine(function(){
       // Configurar la línea de bloqueos
       setCurrentBlocks(function(){
           blockEvents();
       });
   });
});
