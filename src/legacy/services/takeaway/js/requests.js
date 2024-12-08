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

function setTable(){

    var language = {
       "emptyTable":     "No hay pedidos",
       "info":           "Cargados del _START_ al _END_ de _TOTAL_ pedidos",
       "infoEmpty":      "0 de 0 de un total de 0 pedidos",
       "infoFiltered":   "(filtrados de un total de _MAX_ pedidos)",
       "infoPostFix":    "",
       "thousands":      ",",
       "lengthMenu":     "Ver   _MENU_ pedidos por página",
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
}

function updateRequestState(obj){
   if($(obj).val() !== "-1"){
       $.post($(obj).data("url"),
           { Id:$(obj).data("id"), State: $(obj).val()},
           function(data){
               if(data.Code === 200 && data.Result === true){
                   $("#result").parent().clone()
                           .appendTo("#resultados")
                           .addClass("alert-success")
                           .addClass("has-success")
                           .removeClass("hide")
                           .find("p").text(data.Message);
               }
               else{
                   $("#result").parent().clone()
                           .appendTo("#resultados")
                           .addClass("alert-danger")
                           .removeClass("hide").text(data.Message);
               }
               ocultarResultados();
       });
   }
}

$(function() {
   setTable();
});
