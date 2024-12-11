

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
