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

function processError(data, ctrl){
    alert(data.Message);
    if($(ctrl).is(':checked')){
        $(ctrl).attr('checked', false);
    }
}

function setSlot(obj){
    $("[name='Id']").val($(obj).data("id"));
    $("[name='SlotOfDelivery']").val($(obj).data("slotofdelivery"));
    $("[name='DayOfWeek']").val($(obj).data("dayofweek"));
    $.post($("#frm").attr("action"),
        $("#frm").serialize(),function(data){
            if(data.Result === false ){
                processError(data, obj);
            }
            else{
                $(obj).data("id", data.Data)
            }
    });
}

function setTable(){

    var sSlots = $("#jsonslots").val();

    if(sSlots !== "" && sSlots !== undefined){
        var slots = $.parseJSON(sSlots);

        var label = $("<label />")
            .append($("<span />").text("|"))
            .append($("<span />").append($("<span />").text("/")))
            .append($("<span />").text("O"));

        $.each(slots,function(i,o){

            var id = "chk_" + o.SlotOfDelivery + "_" + o.DayOfWeek;

            var input = $("<input />")
                    .attr( "id", id )
                    .attr("type","checkbox")
                    .data("id", o.Id )
                    .data("dayofweek", o.DayOfWeek)
                    .data("slotofdelivery", o.SlotOfDelivery)
                    .attr("onclick", "setSlot(this);");
            if(o.Id !== 0){
                $(input).attr("checked", "checked");
            }

            var main_span = $("<span />")
                    .addClass("cool_checkbox")
                    .append(input)
                    .append($(label).clone().attr("for",id));


            var text = $("<span />")
                    .addClass("texto-turno")
                    .append($("#turn_" + o.Turn).data("text"));

            var td = $("<td />").append(text)
                    .append(main_span);

            $("[data-sod='" + o.SlotOfDelivery + "']").append(td);
        });
    }
}

$(function(){
   setTable();
});
