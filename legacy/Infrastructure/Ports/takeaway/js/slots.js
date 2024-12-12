

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
