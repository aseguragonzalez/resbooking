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

function clearError(){
    $(".has-error").removeClass("has-error");
    $.each($(".help-block"),function(i,item){
       if($(item).data("msg") !== undefined
               && $(item).data("msg") !== ""){
           $(item).text($(item).data("msg"));
       }
    });
}

function newDeliveryTime(obj){
    $("[name='Id']").val("");
    $("[name='Name']").val("");
    $("[name='Start']").val("");
    $("[name='End']").val("");
    $("[name='IcoName']").val("");
    clearError();
}

function editDeliveryTime(obj){
    $("[name='Id']").val($(obj).data("id"));
    $("[name='Name']").val($(obj).data("name"));
    $("[name='Start']").val($(obj).data("start"));
    $("[name='End']").val($(obj).data("end"));
    $("[name='IcoName']").val($(obj).data("iconame"));
    clearError();
}

function deleteDeliveryTime(obj){
    $("#btnDelete").attr("href", $(obj).data("url"));
}

$(function(){

    if($("#error").val()==="1"){
        $("#frm-edicion").modal("show");
    }

});
