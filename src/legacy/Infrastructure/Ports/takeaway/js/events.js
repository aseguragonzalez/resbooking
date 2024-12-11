

function setEvent(obj){
    var filter = "th[data-id='" + $(obj).data("day") + "']";
    var date = $( filter ).data("date");
    $.post(
        $("#url").val(),{
            Id:$(obj).data("id"),
            SlotOfDelivery:$(obj).data("slot"),
            Date:date,
            Open: $(obj).is(":checked") ? 1 : 0
        },function(data){
            if(data.Result === true){
                if(data.Data === undefined ||data.Data === null){
                    $(obj).data("id", "0");
                }else{
                    $(obj).data("id", data.Data);
                }
            }
            else{
                $(obj).prop("checked", !$(obj).is(":checked"));
            }
    });
}

function setEvents(){
    var json = $("#events").val();
    if(json !== "" && json !== undefined){
        var events = $.parseJSON(json);
        $.each($(events), function(i,o){
            var selector= "#chk_" +  o.DayOfWeek
                    + "_" + o.SlotOfDelivery;
            $(selector).data("id", o.Id);
            $(selector).prop("checked", o.Open === 1);
        });
    }
}

function setSlots(callback){
    var json = $("#slots").val();
    if(json !== "" && json !== undefined){
        var slots = $.parseJSON(json);
        $.each($(slots), function(i,o){
            var selector= "#chk_" +  o.DayOfWeek
                    + "_" + o.SlotOfDelivery;
            $(selector).prop("checked", true);
        });
    }

    if($.isFunction(callback)){
        callback();
    }
}

$(function(){
    setSlots(function(){
        setEvents();
    });

    var yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    $.each($("input[type='checkbox']"), function(i,o){
        var filter = "th[data-id='" + $(o).data("day") + "']";
        var sdate = $( filter ).data("date");
        var date = new Date(sdate);
        if(yesterday > date){
            $(o).prop("disabled", true);
        }

    });
});
