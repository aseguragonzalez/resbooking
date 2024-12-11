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
 * Función para eliminar la fila contenedora del control
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function eliminarFila(obj){
    var parent = $(obj).parentsUntil("tbody");
    var tbody = $(parent).parent();
    var options = {};
    $(parent).hide( "highlight", options, 1000, function(){
        $(parent).remove();
        var count = $(tbody).find("tr").length;
        $("#total_filas").text(count);
    });
}

/**
 * Función para visualizar el resultado de la operación
 * en el contenedor de mensajes alert
 * @param {String} message Mensaje de la operación
 * @param {Boolean} success Flag sobre el resultado de la operación
 * @returns {Void}
 */
function visualizarResultado(message, success){
    var cssClass = (success === true)
       ? "alert-success" : "alert-danger";
    var d = new Date();
    var data_time = "alert_" + d.getTime();
    $("#alert").clone().attr("data-time", data_time).appendTo("#resultado");
    var alert = $("#resultado").find("[data-time='" + data_time + "']");
    $(alert).removeClass("hide").addClass(cssClass);
    $(alert).find("p").text(message);
    if(success === true){
        $(alert).hide( "highlight", {}, 2000, function(){
            $(alert).remove();
        });
    }
}

/**
 * Override del selector contains para busqueda de ubicación case insensitive
 * @returns {undefined}
 */
function overridejQueryContains() {
    $.expr[':'].contains = function (a, i, m) {
        return $(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };
}

/**
 * Establece el elemento seleccionado en los objetos select
 * @returns {void}
 */
function setSelectedItem(){
    $.each($( "select" ), function(index, item){
        var a = $(item).data( "value" );
        if( a !== undefined && a !== "" && a !== "0"){
            $( item ).val( a );
            $( item ).find( "option[value='" + a + "']" )
                    .attr( "selected", "selected" );
        }
    });
}

/**
 * Filtro de filas (selector) en tabla por texto
 * @param {type} obj
 * @returns {undefined}
 */
function filter(obj){
    // Validar el objeto
    if( obj === undefined ){
        return;
    }
    // Obtener el valor actual
    var value = $(obj).val().toString().trim();
    // Obtener el selector a utilizar
    var selector = $(obj).data( "selector" );

    if(value === "" ){
        $( selector ).show();
    }
    else{
        $( selector ).hide();
        var filtro = "td:Contains(" + value + ")";
        $( selector ).find(filtro).parent().show();
    }
    // Actualizar el número de resultados si es necesario
    $( "#rowCount" ).text( $( selector ).filter( ":visible" ).length );
}


/**************************************************************************/
/**
 * Establece los atributos del control del dropdown
 * @param {object} obj Referencia al control
 * @returns {void}
 */
function setDropDownToggle(obj){
    if(obj !== undefined){
        var span = $("<span />")
                .addClass("caret");
        $(obj).addClass("dropdown-toggle")
                .attr("data-toggle","dropdown")
                .attr("data-target","#")
                .append(span);
    }
}

/**
 * Configura todos los elementos del dropdown
 * @param {object} obj Referencia al control
 * @param {Array} arr Colección de elementos de la lista
 * @returns {void}
 */
function setDropDownItems(obj, arr){
    // Referencial al elemento li contenedor
    var parent = $(obj).parent();
    // Referencia a la sublista
    var $ul = $( "<ul />" )
            .addClass( "dropdown-menu" )
            .attr( "role", "menu" );
    // Agregar clases y sublista al Li padre
    $(parent).addClass("dropdown").append($ul);
    // Agregar cada uno de los enlaces
    $.map(arr, function(i){
        var url = $("#current_path").val() + "/"
                + i.controller + "/" + i.action;
        var anchor = $("<a />")
                .attr("href", url)
                .attr("title", i.title)
                .text(i.text);
        var $elem = $( "<li />" ).append(anchor);
        $($ul).append($elem);
    });
}

/**
 * Generar el contenedor del dropdow
 * @param {object} obj Referencia al enlace
 * @returns {object} obj Referencia al nuevo enlace
 */
function cloneListItem(obj){
    // Referencia al nuevo list item
    var li = $("<li />");
    // Referencia al nuevo enlace
    var anchor = $("<a />")
            .attr("id", $(obj).attr("id"))
            .attr("href", "#")
            .attr("role", "button")
            .text($(obj).text());

    if($(obj).parent().hasClass("active")===true){
        $(li).addClass("active");
    }
    // Agregar nuevo enlace
    $(li).append(anchor);
    // Agregar nuevo list item al contenedor principal
    $(li).insertAfter($(obj).parent());
    // Eliminar el li actual
    $(obj).parent().remove();
    // retornar la referencia al nuevo enlace
    return anchor;
}

/**
 * Configuración de los posibles submenus
 * @returns {void}
 */
function setSubMenu(){
    // Obtener los enlaces
    var items = $("#menu-resbooking").find( "a" );
    // Recorrer todos los enlaces
    $.each($(items), function(index, obj){
        var arr = $(obj).data( "actions" );
        var colection = $.makeArray(arr);
        if(arr !== undefined
                && colection !== undefined
                && colection.length > 0){
            // clonar enlace
            var newObj = cloneListItem(obj);
            // Configurar el item como menú
            setDropDownToggle(newObj);
            // Configurar las opciones del menú
            setDropDownItems(newObj, arr);
        }
    });
    $('.dropdown-toggle').dropdown();
}

/**
 * Actualiza el número de solicitudes pendientes
 * @returns {void}
 */
function setCount(){
    var url = $( "#current_path" ).val() + '/Requests/GetCount';
    $.ajax({
        url: url,
        dataType: "json",
        contentType : "application/json",
        success: function(data){
            $( "#Pendientes" ).text( "Pendientes" );
            if(data.Error===false){
                if(data.Count > 0){
                    var span = $( "<span />" )
                            .addClass( "advertencia" )
                            .text( "(" + data.Count + ")" );
                    $( "#Pendientes" ).append( span  );
                }
            }
        }
    });
}

/**
 * Configura la pestaña activa del menú
 * @returns {void}
 */
function setActivo(){
    // comprobar campo
    if( $( "#activo" ).length !==1 ){
        return;
    }
    // recuperar id activo
    var id = "#" + $( "#activo" )
            .val().toString()
            .replace( "{", "").replace( "}", "");
    // validar
    if($(id).length === 0){
        return;
    }
    // setear
    $( id ).parent().addClass( "active" );
}

/**
 * Configura la pestaña activa del submenú
 * @returns {void}
 */
function setSubMenuActivo(){
    // comprobar campo
    if( $( "#submenuactivo" ).length !==1 ){
        return;
    }
    // recuperar id activo
    var id = "#" + $( "#submenuactivo" ).val()
            .toString().replace( "{", "").replace( "}", "");
    // validar
    if($(id).length === 0){
        return;
    }
    // setear
    $( id ).addClass( "active" );
}

function cambiarMenu(callback){
    if(self !== top){
        $("#logo").remove();
        $("#menu-principal").removeClass("navbar-inverse")
                .addClass("navbar-default");
        $("#logout-btn").parent().remove();
        $("#Perfil").parent().remove();
    }

    if($.isFunction(callback)){
        callback();
    }
}

/**
 * Evento load del formulario
 * @returns {void}
 */
$(function(){
    cambiarMenu(function(){
        setCount();
        window.setInterval("setCount()", 120000);
        setActivo();
        setSubMenuActivo();
        setSubMenu();
    });
});
