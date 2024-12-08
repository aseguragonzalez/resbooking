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

function utf8_to_b64(str) {
    return window.btoa(encodeURIComponent(escape(str)));
}

function b64_to_utf8(str) {
    return unescape(decodeURIComponent(window.atob(str)));
}

function processForm(){
    // Calcular el hash de la password en cliente
    var pass = $("input[name='tp_password']").val();
    if($("input[name='hash']").length === 0 && CryptoJS !== undefined){
        pass = CryptoJS.SHA512(pass);
    }
    if(pass !== undefined ){
        $("input[name='password']").val(pass);
    }

    if($("input[name='remember']").is(":checked")){
        var value = serializeInputsJSON("#form");
        setCookie("res-remember", value);
    }
}

function setForm(){

    var cookie = getCookie("res-remember");

    if(cookie !== ""){
        cookie =  b64_to_utf8(cookie);
        var o = $.parseJSON(cookie);
        $.makeArray(o);
        $.each(o, function(i, o){
           $("input[name='" + i + "']").val(o);
        });
    }
}

function serializeInputsJSON(selector){
    var json = "";
    var inputs = $(selector).find("input");
    var length = $(inputs).length;
    $.each(inputs,function(i,o){
        var types = [ "email", "text", "password", "hidden"];
        var type = $(o).attr("type");
        if($.inArray(type, types) > -1){
            var name = $(o).attr("name");
            if(name !== "" && name !== undefined){
                json += ' "' + $(o).attr("name") + '":"'
                        + $(o).val() + '"';
                if(i < length - 2){
                    json += ',';
                }
            }
        }
    });

    json =  "{" + json.substring(0, json.length - 1) + "}";

    return utf8_to_b64(json);
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function setCookie(cname, cvalue) {
    var d = new Date();
    d.setTime(d.getTime() + (24*60*60*365));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

$(function(){
    // Setear parámetros y eventos del formulario
    $( "#form" )
        .attr( "action", document.URL )
        .on( "submit", function(){
            processForm();
    });
    setForm();
});
