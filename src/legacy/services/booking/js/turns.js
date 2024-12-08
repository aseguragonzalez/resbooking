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
 * Cerrojo para indicar que estamos en actualización masiva
 * @type Boolean
 */
var _UPDATING_ = false;

/**
 * Número total de registros a actualizar
 * @type Number
 */
var _UPDATING_COUNT_ = 0;

/**
 * Número de registros actualizados
 * @type Number
 */
var _UPDATING_CURRENT_ = 0;

/**
 * Función para la configuración de los turnos establecidos
 * @returns {Void}
 */
function setConfigs(){
   var configs = $.parseJSON($("#configs").val());
   if($.isArray(configs)){
       $.each($(configs), function(i,o){
          var selector = "input[data-turn='" + o.Turn
                  + "'][data-dow='" + o.Day + "']" ;
          $(selector).data("id",o.Id).prop("checked", true);
       });
   }
}

/**
 * Función para almacear la información sobre la configuración de un turno
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function setTurn(obj){
   var url = $("#current_path").val() + "/Turns/SetTurn";
   var state = $(obj).is(":checked");
   var config = {
       Id :$(obj).data("id"),
       Day: $(obj).data("dow"),
       Turn: $(obj).data("turn"),
       Count: $(obj).data("count")
   };
   $.post(url, config, function(data){
      if(data !== undefined && data.Result !== undefined){
          var cancel = (data.Result < 0);
          if(cancel === true){
              $(obj).prop("checked", !state);
          }
          else{
              $(obj).data("id", data.Result);
          }
      }
   }).error(function(data){
       $(obj).prop("checked", !state);
   });
}

/**
 * Función para establecer el valor de todos los turnos de la tabla
 * @param {Object} obj Referencia al control
 * @param {Boolean} value Valor a establecer
 * @returns {undefined}
 */
function setAllTurns(obj, value){
   var selector = "input[type='checkbox']:checked";
   if(value === true){
       selector = "input[type='checkbox']:not(:checked)";
   }

   _UPDATING_COUNT_ = $(selector).length;

   if(_UPDATING_COUNT_ > 0){
       _UPDATING_ = true;
       _UPDATING_CURRENT_ = 0;
       $(".progress-bar").css('width', 0+'%').attr("aria-valuenow", 0);
       $("#percent").text(0);
       $("#updating").modal("show");
   }

   $.each($(selector),function(i,o){
       $(o).prop("checked", value);
       setTurn(o);
   });
}

/**
 * Evento onload
 * @returns {undefined}
 */
$(function(){

   setConfigs();

   $('#updating').modal({
       keyboard: false,
       show:false,
       backdrop: 'static'
   });

   $(document).ajaxComplete(function() {
       if(_UPDATING_ === true && _UPDATING_COUNT_ > 0){
           _UPDATING_CURRENT_++;
           var val = (_UPDATING_CURRENT_/_UPDATING_COUNT_) * 100;
           $(".progress-bar").css('width', val+'%').attr("aria-valuenow", val);
           $("#percent").text(Math.round(val));
           if(_UPDATING_COUNT_ === _UPDATING_CURRENT_){
               $("#updating").modal("hide");
               _UPDATING_ = false;
               _UPDATING_COUNT_ = 0;
           }
       }
   });

});
