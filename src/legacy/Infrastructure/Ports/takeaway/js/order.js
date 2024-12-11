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

function getItems(callback){
    var products = new Array();
    $.each($(".producto"), function(index, item){
        var o = {
            Id:0,
            Request:0,
            Product: $(item).data("id"),
            Count:$(item).data("count"),
            Data:""
        };
        products.push(o);
    });

    if($.isFunction(callback)){
        callback(products);
    }

    return products;
}

function setItems(callback){
    if($("#Items").val() !== undefined
            && $("#Items").val() !== ""){
        var items = $.parseJSON($("#Items").val());
        $.each($(items), function(i,o){
            var btn = $("#btn_add_" + o.Product);
            if(btn !== undefined){
                addProduct(btn, o.Count);
            }
        });
        setDetails();
    }
}

function onSubmit(obj){

    if($("#postCode").val() !== ""){
        var code = getPostcode($("#postCode").val());
        $("input[name='PostCode']").val(code.Code);
    }
    else{
        $("input[name='PostCode']").val("00000");
    }


    getItems(function(products){
        var json = JSON.stringify(products);
        $("#Items").val(json);
    });
}

function setDetails(obj){

    $("#detalles-pedido tbody tr").remove();
    $("#detalles-pedido tfoot tr").remove();

    $.each($(".producto"), function(index, item){
        var tr = $("<tr />")
            .data("id", $(item).data("id"))
            .data("ref", $(item).data("ref"))
            .data("name", $(item).data("name"))
            .data("price",$(item).data("price"))
            .data("count",$(item).data("count"));

        var price = parseFloat($(item).data("price")).toFixed(2);

        $(tr).append($("<td />").text($(item).data("ref")))
                .append($("<td />").text($(item).data("name")))
                .append($("<td />").text($(item).data("count")))
                .append($("<td />").text(price + " €/ud"));

        $("#detalles-pedido tbody").append(tr);

    });

    var msg = "";
    if($("#desc").text() !== ""){

        var total = (parseFloat($("#total").data("value"))
                * (1- ($("#desc").data("value")/100))).toFixed(2);

        msg = "Descuento: " + $("#desc").data("text")
            + " - Total: " + total + " €";
    }
    else{
        msg = "Total: " + parseFloat($("#total").data("value")).toFixed(2) + " €";
    }

    var tr = $("<tr />").append(
            $("<td />").attr("colspan", 4)
            .text(msg));

    $("#detalles-pedido tfoot").append(tr);

}

function changeDelivery(obj){
    if($(obj).val() === "2"){

        if($("[name='Address']").data("value") !== undefined){
            $("[name='Address']").text(
                    $("[name='Address']").data("value"));
        }
        else{
            $("[name='Address']").text("");
        }
    }
    else{
        $("[name='Address']").text("No procede");
    }
}

function updateTotal(){
    var total = 0;
    $.each($(".producto"), function(index, item){
        total += ($(item).data("count") * $(item).data("price"));
    });
    var desc = "";
    var discount = false;
    $.each($(".discount"),function(i,o){
       if($(o).data("min") < total && $(o).data("max") >= total && discount === false) {
           desc += " -" + $(o).data("value") + "%";
           $("#desc").data("min", $(o).data("min"))
                   .data("max", $(o).data("max"))
                   .data("value", $(o).data("value"))
                    .data("text", desc);
            $("input[name='Discount']").val($(o).data("id"));
            discount = true;
       }
    });
    total = total.toFixed(2);
    $("#total").text(total).data("value", total);
    $("#desc").text(desc);
}

function updateAddress(obj){
    $("input[name='Address']").val($(obj).val());
}

function updatePostCode(obj){
    $("input[name='PostCode']").val($(obj).val());
}

function addProduct(obj, count){

    if(obj === undefined){
        return;
    }

    if($("#detalles .empty").length === 1){
        $("#detalles .empty").remove();
    }

    var items = $("#detalles li").filter(function() {
        return $(this).data("id") === $(obj).data("id");
    });

    if($(items).length === 0){
        var tx = " 1x";

        if(count !== undefined){
            tx = " " + count +"x";
        }
        else{
            count = 1;
        }

        var text = $(obj).data("name") + tx
                + $(obj).data("price") + " €/ud";

        var price = parseFloat($(obj).data("price")).toFixed(2);

        var li = $("<li />")
                .addClass("producto")
                .data("id", $(obj).data("id"))
                .data("ref", $(obj).data("ref"))
                .data("name", $(obj).data("name"))
                .data("price", price)
                .data("count", count).text(text);
        $("#detalles").append(li);
    }
    else{
        var item = $(items)[0];
        var count = $(item).data("count") + 1;
        $(item).data("count", count);
        var price = parseFloat($(obj).data("price")).toFixed(2);
        var text = $(obj).data("name") + " "+  count +"x"
                + price + " €/ud";
        $(item).text(text);
    }

    updateTotal();
}

function removeProduct(obj){
    if(obj === undefined){
        return;
    }

    var items = $("li").filter(function() {
        return $(this).data("id") === $(obj).data("id");
    });


    if($(items).length > 0){
        var item = $(items)[0];
        var count = $(item).data("count") - 1;
        var text = $(item).data("name") + " "+  count +"x"
                + $(item).data("price") + " €/ud";
        if(count > 0){
            $(item).data("count", count);
            $(item).text(text);
        }
        else{
            $(item).remove();
        }
    }
    if($("#detalles li").length === 0){
        var li = $("<li />").addClass("empty")
                .text($("#detalles").data("msg"));
        $("#detalles").append(li);
    }

    updateTotal();
}

function setSelectedItem(){
    $.each($("select"), function(index, item){
        var isel = parseInt($(item).data("selected"));
        if(isel !== NaN && isel > 0){
           $(item).val(isel);
        }
        else{
            var opt = $(item).find("option:first");
            if($(opt).length === 1){
                $(item).val($(opt).val());
            }
        }
    });
}

function nextStep(obj){
    validatePostCode(obj, function(result){
        var next = false;
        if(result === true){
            $("#ePostCode").text($("#ePostCode").data("msg"));
            $("#ePostCode").parent().removeClass("has-error");
            if($("#address").val() !== ""){
                next = true;
                $("#eAddress").text($("#eAddress").data("msg"));
                $("#eAddress").parent().removeClass("has-error");
            }
            else{
                $("#eAddress").text($("#eAddress").data("err"));
                $("#eAddress").parent().addClass("has-error");
            }
        }
        else{
            $("#ePostCode").text($("#ePostCode").data("err"));
            $("#ePostCode").parent().addClass("has-error");
        }

        if(next===true){
            openCatalog();
        }
    });
}

function getPostcode(code){
    var sCodes = $("#postCode").data("codes");
    var codes = (sCodes !== undefined && $.isArray(sCodes))
        ? sCodes : new Array();

    var arr = $.grep(codes,function(item){
       return item.PostCode === code ;
    });

    return arr[0];
}

function validatePostCode(obj, callback){
    var sCodes = $(obj).data("codes");
    var codes = (sCodes !== undefined && $.isArray(sCodes))
        ? sCodes : new Array();
    var code = $(obj).val();
    var arr = $.grep(codes,function(item){
       return item.PostCode === code ;
    });
    var result = ($(arr).length === 1);
    if($.isFunction(callback)){
        callback(result);
    }
}

function clearAddrForm(){
    $("#ePostCode").text($("#ePostCode").data("msg"));
    $("#ePostCode").parent().removeClass("has-error");
    $("#eAddress").text($("#eAddress").data("msg"));
    $("#eAddress").parent().removeClass("has-error");
    $("#postCode").val("");
    $("#address").val("");
}

function setDelivery(obj){
    clearAddrForm();
    $("input[name='DeliveryMethod']").val($(obj).data("delivery"));
    if($(obj).data("delivery") === 2){
        $("#cod-post").removeClass("hide");
    }
    else{
        $("#cod-post").addClass("hide");
        $("input[name='Address']").val("No procede");
        openCatalog();
    }
}

function openCatalog(callback){
    var options = {};
    var time = 600;
    if($( "#catalogo" ).css("display") === "none"){
        $( "#landing" ).toggle( "blind", options, time );
        $( "#catalogo" ).css("display","none")
            .removeClass("hide")
            .toggle( "blind", options, time );
    }
    else{
        $( "#catalogo" ).css("display","")
            .toggle( "blind", options, time );
        $( "#landing" ).toggle( "blind", options, time );
    }
}

function openForm(){
    var options = {};
    var time = 600;
    if($( "#formulario" ).css("display") === "none"){
        $( "#catalogo" ).toggle( "blind", options, time );
        $( "#formulario" ).css("display","none")
                .removeClass("hide")
                .toggle( "blind", options, time);
    }
    else{
        $( "#formulario" ).css("display","")
                .toggle( "blind", options, time);
        $( "#catalogo" ).toggle( "blind", options, time);
    }
}

$(function(){

    setItems();

    setSelectedItem();

    if($("#error").val()==="1"){
        var options = {};
        var time = 600;
        $( "#landing" ).css("display","none")
                .removeClass("hide");
        $( "#catalogo" ).css("display","none")
                .removeClass("hide");
        $( "#formulario" ).css("display","none")
                .removeClass("hide")
                .toggle( "blind", options, time);
    }

});
