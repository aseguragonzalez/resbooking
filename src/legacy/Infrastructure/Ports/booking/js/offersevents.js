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
 * Proceso para guardar un evento
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function setEvent(obj){
   var url = $("#current_path").val()
           + "/OffersEvents/SetEvent";
   var state = $(obj).is(":checked");
   var dto = {
       Date: $(obj).data("date"),
       Year: $(obj).data("year"),
       Week: $(obj).data("week"),
       DayOfWeek: $(obj).data("dow"),
       Turn: $(obj).data("turn"),
       Offer:$(obj).data("offer"),
       Config:$(obj).data("conf"),
       Id: $(obj).data( "id" ),
       State: (state === true) ? 1: 0
   };
   $.post(url, dto, function(data){
        if(data.Error === true){
            $(obj).prop("checked", !state);
        }
   }).error(function(data){
       $(obj).prop("checked", !state);
   });
}

/**
 * Configuración de los eventos
 * @param {Function} callback Referencia a la función de retorno
 * @returns {void}
 */
function setCurrentEvents(callback){
    var items = $.parseJSON($("#events").val());
    $.each($(items), function(i,o){
        var selector = "input[data-dow='" + o.DayOfWeek + "']"
            + "[data-date='" + o.Date + "']"
            + "[data-year='" + o.Year + "']"
            + "[data-week='" + o.Week + "']"
            + "[data-turn='" + o.Turn + "']"
            + "[data-offer='" + o.Offer + "']";
        $(selector).prop("checked", o.State === 1);
        $(selector).data("id", o.Id);
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
 * Configuración de la línea base
 * @param {Function} callback Referencia a la función de retorno
 * @returns {void}
 */
function setBaseLine(callback){
   var items = $.parseJSON($("#configs").val());
   $.each($(items), function(i,o){
       var selector = "input[data-dow='" + o.Day + "']"
           + "[data-turn='" + o.Turn + "']"
           + "[data-offer='" + o.Offer + "']";
       $(selector).data("conf", o.Id);
       $(selector).prop("checked", true);
   });
   if($.isFunction(callback)){
       callback();
   }
}

/**
 * Manejador del evento onload
 * @returns {Void}
 */
$(function(){
    // Configurar la línea base
    setBaseLine(function(){
        // Configurar eventos
        setCurrentEvents(function(){
            blockEvents();
        });
    });
});
