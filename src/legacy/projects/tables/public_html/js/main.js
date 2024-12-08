/*
 * Copyright (C) 2015 manager
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
 * Colección de identificadores bloqueados
 * @type Array
 */
var blocked = new Array();

/**
 * Colección de identificadores generados
 * @type Array
 */
var keys = new Array();

/**
 * Último identificador generado
 * @type Number
 */
var lastKey = 0;


var _MAX_ = 8;

/**
 * Generación de identificadores "temporales"
 * @returns {Number|lastKey}
 */
function getKey(){
    lastKey++;
    keys.push(lastKey);
    return lastKey;
}

/**
 * Generación de un objeto del tipo indicado
 * @param {Number} type Tipo de objeto a generar
 * @param {Object} cssPosition Posición relativa en el marco
 * @param {Function} callback Callback
 * @returns {Void}
 */
function setTable(type, cssPosition,  callback){
    var div = $("<div />")
        .data("type", type)
        .data("key", getKey())
        .addClass("type-" + type)
        .addClass("object")
        .draggable({revert:onDragRevert})
        .droppable({drop:ondrop});
    $(div).css(cssPosition);
    if($.isFunction(callback)){
        callback(div);
    }
}

/**
 * Obtiene la posición relativa del objeto respecto al workarea
 * @param {Object} obj Objeto
 * @returns {Object}
 */
function getCssPosition(obj){
    var pos = $(obj).offset();
    var target = $( ".workarea" ).offset();
    return { "left": pos.left - target.left,
        "top": pos.top - target.top,
        "position":"absolute"};
}

function validateCssPosition(event, obj){
    var width = $(obj).width()/2;
    var height = $(obj).height()/2;
    var target = $( ".workarea" ).offset();
    return (event.pageX - width < target.left
            || event.pageX + width < target.left
            || event.pageY + height < target.top
            || event.pageY - height < target.top );
}

function onDragRevert(event){
    var val = validateCssPosition(event, this);
    return (val === true) ? event : !event;
}

function ondrop( event, ui ){
    if($(event.target).hasClass("object")
            && $(ui.draggable).hasClass("object")){

        var targetType = parseInt($(event.target).data("type"));
        var sourceType = parseInt($(ui.draggable).data("type"));
        var targetKey = $(event.target).data("key");
        var sourceKey = $(ui.draggable).data("key");

        if(sourceType !== NaN
            && sourceKey !== undefined
            && targetType !== NaN
            && targetKey !== undefined
            && $.inArray(targetKey, blocked) === -1){

            var sum = targetType + sourceType;

            if(sum < _MAX_){
                $(event.target).data("type", sum);
                $(event.target)
                        .removeClass("type-" + targetType)
                        .addClass("type-" + sum);
                blocked.push(sourceKey);
                $(ui.draggable).remove();
            }
        }
    }
}

$(function(){
    // Configurar draggables
    $(".toolbox-btn").draggable({ revert: true });
    // Configurar drop
    $(".workarea").droppable({
        drop: function( event, ui ) {
            if($(ui.draggable).hasClass("toolbox-btn")){
                var type = $(ui.draggable).data("type");
                var position = getCssPosition(ui.helper);
                setTable(type, position, function(div){
                    $(event.target).append(div);
                });
            }

            return false;
        }
    });
});
