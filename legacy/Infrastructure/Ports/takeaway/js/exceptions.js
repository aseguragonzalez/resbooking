


function setDiscountEvent(obj){
    var filter = "th[data-id='" + $(obj).data("day") + "']";
    var date = $( filter ).data("date");
    var url = $("#current_path").val() + "/Discounts/SetEvent";
    $.post(url,{
            DiscountOn:$(obj).data("discounton"),
            Week:$(obj).data("week"),
            Year:$(obj).data("year"),
            DayOfWeek:$(obj).data("day"),
            SlotOfDelivery:$(obj).data("slot"),
            Date:date,
            State: $(obj).is(":checked") ? 1 : 0
        },function(data){
            if(data.Result !== true){
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
            $(selector).prop("checked", o.State === 1);
        });
    }
}

function blockEvents(){
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
}

$(function(){

    setEvents();

    blockEvents();
});
