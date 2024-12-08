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
 * Función para limpiar los mensajes de error generados previamente
 * @returns {Void}
 */
function limpiarErrores(){
    $.each($(".has-error").find(".help-block"), function(i,o){
       $(o).text($(o).data("msg"));
    });
    $(".has-error").removeClass("has-error");
}

/**
 * Función para preparar el formulario para una nueva sala
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function nuevoEspacio(obj){
    limpiarErrores();
    $("[name='Id']").val(0);
    $("[name='Name']").val("");
    $("[name='Description']").val("");
}

/**
 * Función para cargar los datos de la sala en el formulario de edición
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function editarEspacio(obj){
    limpiarErrores();
    $("[name='Id']").val($(obj).data("id"));
    $("[name='Name']").val($(obj).data("name"));
    $("[name='Description']").val($(obj).data("desc"));
}

/**
 * Función para generar la petición para eliminación de una sala
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function eliminarEspacio(obj){
    window.location=$(obj).data("url") + $(obj).data("id");
}

/**
 * Manejador del evento onload
 * @returns {Void}
 */
$(function(){
    if($("#err").val() === "1"){
        $("#formulario").modal("show");
    }

    if($("#resultado").text() !== ""){
        $("#resultado").parent().removeClass("hide");
    }
});
