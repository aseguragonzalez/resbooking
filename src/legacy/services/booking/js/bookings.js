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

var arr = [];

function abrirPrePedido(obj){
    $("#pedido-texto").html("");
    var preorder = $(obj).data("preorder").toString();
    if(preorder !== "" && preorder !== undefined && preorder !== "undefined"){
        var prods = preorder.split(";");
        $.each($(prods),function(i,o){
            $("#pedido-texto").append($("<p />").text(o));
        });
    }
}

/**
 * Configuración el calendario de navegación
 * @returns {void}
 */
function setCalendarioNavegacion(){
    if($( "#datepicker" ) !== undefined
            && $( "#datepicker" ).length === 1){
        // Configurar el control de calendario
        $( "#datepicker" ).datepicker({
            showOn: "button",
            buttonImage: $( "#url-img" ).val(),
            buttonImageOnly: true,
            dateFormat: "yy-mm-dd",
            firstDay: 1
        });
        // Establecer evento del calendario
        $( "#datepicker" ).on( "change", function(){
            document.location = $(this).data( "url" ) + $(this).val();
        });
    }
}

/**
 * Función para cargar en el formulario de anotaciones la información actual de la reserva
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function abrirNotas(obj){
    $("#notas").val($(obj).data("notes"));
    $("#notas_id").val($(obj).data("id"));
}

/**
 * Función para almacenar las notas asociadas a una reserva
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function actualizarNotas(obj){
    var url = $("#current_path").val() + "/Booking/SetNotes/";
    var dto = {id:$("#notas_id").val(), notes:$("#notas").val()};
    $.post(url, dto, function(data){
        var message = "Se ha producido un problema. Inténtelo más tarde";
        var success = false;
        if(data !== undefined && data.Result >= 0){
            message = "Las anotaciones han sido guardadas correctamente.";
            success = true;
            $("#notas_" + dto.id).data("notes", dto.notes)
                    .attr("data-notes", dto.notes);
        }
        visualizarResultado(message, success);
    }).error(function(data){
        var message = "Se ha producido un error. Inténtelo más tarde";
        visualizarResultado(message, false);
    });
    $("#frmNotas").modal("hide");
}

/**
 * Función para cargar la información del usuario en el formulario de mensaje
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function abrirEmail(obj){
    $("#nombre-msg-cliente").text($(obj).data("nombre"));
    $("#email-msg-cliente").text($(obj).data("email"));
    $("#message_to").val($(obj).data("email"));
    $("#message").val("");
    $("#message").parent().removeClass("has-error");
}

/**
 * Función para realizar el envío del mensaje
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function enviarMensaje(obj){
    var url = $("#current_path").val() + "/Booking/SendMessage/";
    if($("#message").val() === ""){
        $("#message").parent().addClass("has-error");
        return;
    }
    var dto = {to:$("#message_to").val(), message:$("#message").val()};
    $.post(url, dto, function(data){
        var message = "Se ha producido un problema. Inténtelo más tarde";
        var success = false;
        if(data !== undefined && data.Result >= 0){
            message = "El mensaje ha sido enviado con éxito";
            success = true;
        }
        visualizarResultado(message, success);
    }).error(function(data){
        var message = "Se ha producido un error. Inténtelo más tarde";
        visualizarResultado(message, false);
    });
    $("#frmEmail").modal("hide");
}

/**
 * Función para cargar la información del recordatorio en el modal de confirmación
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function abrirRecordatorio(obj){
    $("#recordatorio_id").val($(obj).data("id"));
    $("#recordatorio_to").text($(obj).data("to"));
}

/**
 * Función para realizar el envío del recordatorio
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function enviarRecordatorio(obj){
    var url = $("#current_path").val() + "/Booking/SendReminder/"
            + $("#recordatorio_id").val();
    $.post(url, function(data){
        var message = "Se ha producido un problema. Inténtelo más tarde";
        var success = false;
        if(data !== undefined && data.Result >= 0){
            message = "El recordatorio se ha enviado satisfactoriamente.";
            success = true;
        }
        visualizarResultado(message, success);
    }).error(function(data){
        var message = "Se ha producido un error. Inténtelo más tarde";
        visualizarResultado(message, false);
    });
    $("#frmRecordatorio").modal("hide");
}

/**
 * Función de rollback para el cambio de comensales cuando se produce un error
 * @param {Object} obj Referencia al control
 * @param {String} message Mensaje de error
 * @returns {Void}
 */
function rollbackComensales(obj, message){
    visualizarResultado(message, false);
    var oldValue = obj.defaultValue;
    $(obj).val(oldValue);
}

/**
 * Función para la validación del número de comensales fijado
 * @param {Object} obj Referencia al control
 * @returns {Boolean}
 */
function validarComensales(obj){
    var min = parseInt($(obj).attr("min"));
    var max = parseInt($(obj).attr("max"));
    // Parsear el valor actual para validarlo.
    var newValue = parseInt($(obj).val());
    // Parsear el id del elemento
    var id = parseInt($(obj).data( "id" ));
    // Validación de los parámetros
    if( isNaN(newValue) || isNaN(id) || newValue < min
            || newValue > max || id < 1 ){
        // Obtener valor anterior
        var oldValue = obj.defaultValue;
        // Setear el valor anterior
        $(obj).val(oldValue);
        // Establecer el foco en la caja de texto
        $(obj).focus();

        return false;
    }
    return true;
}

/**
 * Función para realizar la actualización
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function actualizarComensales(obj){
    if(validarComensales(obj) === true){
        var url = $("#current_path").val() + "/Booking/SetDiners/";
        var dto = {
            id: parseInt($(obj).data("id")),
            value: parseInt($(obj).val())
        };
        $.post(url, dto, function(data){
            if(data.Result >= 0){
                obj.defaultValue = dto.value;
                return;
            }
            var message = "No se ha podido actualizar el número de comensales.";
            if(data.Result === -1){
                message = "El número de comensales debe ser superior al mínimo";
            }
            else if(data.Result === -2){
                message = "El número de comensales debe ser inferior al máximo";
            }
            rollbackComensales(obj, message);
        }).error(function(data){
            var message = "Se ha producido un error interno. Inténtelo más tarde.";
            rollbackComensales(obj, message);
        });
    }
}


function actualizarInfoTabla(obj){
    var url = $("#current_path").val() + "/Booking/SetTable/";
    var dto = {
        id: parseInt($(obj).data("id")),
        table: $(obj).val()
    };
    $.post(url, dto, function(data){
        if(data.Result >= 0){
            obj.defaultValue = dto.value;
            return;
        }
        var message = "No se ha podido actualizar la información.";

        rollbackComensales(obj, message);
    }).error(function(data){
        var message = "Se ha producido un error interno. Inténtelo más tarde.";
        rollbackComensales(obj, message);
    });
}

/**
 * Manejador del evento "OnChange" de los desplegables de estado de reserva
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function onChangeEstado(obj){
    if($("#anulado").val() === $(obj).val()){
        $("#btnAnulacion").data("id", $(obj).data("id"));
        $("#btnAnulacion").val($(obj).val());
        $("#aviso").modal('show');
    }
    else{
        actualizarEstado(obj);
    }
}

/**
 * Función para deshabilitar los controles de una fila
 * @param {Object} obj Referencia a un control contenido en la fila
 * @returns {Void}
 */
function deshabilitarOperaciones(obj){
    var tr = $(obj).parentsUntil("tbody");
    var estado = parseInt($(tr).find("select").data("value"));
    if(estado === parseInt($("#anulado").val())){
        $(tr).find("input").prop("disabled", true);
        $(tr).find("select").prop("disabled", true);
        $(tr).find("a").attr("data-disabled", 1).removeAttr("onclick").on("click", function(){
            event.preventDefault();
            return false;
        });
        $(tr).attr("data-disabled", 1);
    }
}

function deshabilitarControlesPorFecha(){
    var actual = new Date();
    var anterior = new Date(actual);
    anterior.setDate(anterior.getDate()-1);
    var fechaReservas = new Date($("#fechaReservas").val());
    if (fechaReservas <= anterior){
        $(".controles a").attr("data-disabled", 1).removeAttr("onclick")
                .on("click", function(){
            event.preventDefault();
            return false;
        });
    }
}

/**
 * Función de rollback para el control de estado
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function rollbackEstado(obj){
    $(obj).val($(obj).data("value"));
}

/**
 * Función para actualizar el estado de una reserva
 * @param {Object} obj Referencia al control
 * @param {Boolean} isModal Flag para indicar si se llama desde una ventana modal
 * @returns {Void}
 */
function actualizarEstado(obj, isModal){
    var url = $("#current_path").val() + "/Booking/SetState/";
    var dto = {id:$(obj).data("id"), state:$(obj).val()};
    $.post(url, dto, function(data){
        if(isModal === true){
            obj = $("#estado_" + dto.id);
            $("#aviso").modal("hide");
        }
        var message = "Se ha producido un problema. Inténtelo más tarde";
        var success = false;
        if(data !== undefined && data.Result >= 0){
            message = "El estado se ha modificado con éxito.";
            success = true;
            $("#estado_" + dto.id).data("value", dto.state);
            deshabilitarOperaciones($("#estado_" + dto.id));
            return;
        }
        visualizarResultado(message, success);
        rollbackEstado(obj);
    }).error(function(data){
        var message = "Se ha producido un error. Inténtelo más tarde";
        visualizarResultado(message, false);
        rollbackEstado(obj);
    });
}

/**
 * Función rara cargar la información de cliente en la ficha
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function abrirFicha(obj){
    var id = parseInt($(obj).data("id"));
    if(isNaN(id) || id < 1){
        $("#frmFicha").modal("hide");
        return;
    }
    var url = $("#current_path").val() + "/Clients/GetClient/" + id;
    $.get(url,function(data){
        if(data.Error === false){
            $("#frmFicha").find("#cliente-vip")
                    .data("id", data.Result.Id).prop("checked",data.Result.Vip);
            $("#frmFicha").find("#nombre-cliente").text(data.Result.Name);
            $("#frmFicha").find("#telefono-cliente").text(data.Result.Phone);
            $("#frmFicha").find("#email-cliente").text(data.Result.Email);
            $("#frmFicha").find("#visitas-cliente").text(data.Result.Total);
            if(data.Result.Comments !== null){
                $("#frmFicha").find("#comentarios-cliente")
                        .text(data.Result.Comments);
            }
            if(data.Result.UltimaFecha !== ""){
                $("#frmFicha").find("#ultima-fecha")
                        .text("(última vez: " + data.Result.UltimaFecha + ")");
            }
            $("#frmFicha").find("#comentarios-cliente").data("id",data.Result.Id);
            $("#frmFicha").find("#comentarios-cliente");

            $("#frmFicha").find("#historial").html("");

            if(data.Result.Bookings !== undefined){
                $.each(data.Result.Bookings, function(i,o){
                    var state = $("<span />").attr("data-state", o.State).text(" | " + o.StateName);
                    var msg = o.Date + ", " + o.Diners + " comensales. ";
                    $("#historial").append($("<li />").text(msg).append(state));
                });
            }

            $("#frmFicha").modal("show");
        }
    });
}

/**
 * Función para actualizar la tipificación vip de los clientes
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function actualizarVip(obj){
    var url = $("#current_path").val() + "/Clients/SetVip/"
            + $("#cliente-vip").data("id");
    $.post(url, function(data){
        if(data.Result >= 0){
            $("#frmFicha").modal("hide");
        }
        else{
            $("#cliente-vip").prop("checked",
                !$("#cliente-vip").prop("checked"));
        }
    });
}

/**
 * Función para la actualización de los comentarios del cliente
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function actualizarComentarios(obj){
    var url = $("#current_path").val() + "/Clients/SetNotes/"
            + $("#comentarios-cliente").data("id");
    var dto = {
        id:$("#comentarios-cliente").data("id"),
        notes:$("#comentarios-cliente").val()
    };
    $.post(url, dto, function(data){
        if(data.Result >= 0){
            var comments = $("#comentarios-cliente").val();
            $(".comments[data-id='" + $("#comentarios-cliente").data("id") + "']")
                .attr("data-comments", comments)
                .attr("data-original-title", comments);
            $("#frmFicha").modal("hide");
        }
    });
}

/**
 * Manejador del formulario de reservas manuales
 * @param {Object} obj Referencia al control
 * @returns {Boolean}
 */
function enviarReserva(obj){
    var url = $("#current_path").val() + "/BookForm/Save/1" ;
    event.preventDefault();
    $(".calendar[name='Date']").datepicker( "option", "dateFormat","yy-m-d");
    var frm = $(obj).serialize();
    $.post(url, frm, function(data){
        if(data.Result === false){
            $("#modal-body").html(data.Content);
            configurarFormulario();
        }
        else{
            window.location = window.location;
            $("#frmReservas").modal("hide");
        }
    });
    return false;
}

function setAutocompleteClientName(obj){
    $("input[name='ClientName']").typeahead({
        source: function(query, process){
            if(query.toString().length > 2){
                var url = $("#current_path").val() +'/Clients/FindClientByName/' + query;
                $.get(url,function(data){
                    arr = [];
                    $.each(data.Result, function(i,o){
                        if($.inArray(o, arr) < 0){
                            arr.push(o);
                        }
                    });
                    process(arr);
                });
            }
        },
        displayText:function(item){
            return item.Name || item;
        },
        matcher:function(item){
            return true;
        },
        sorter:function(items){
            return items;
        },
        highlighter: function (item) {
            if(typeof item !== 'string') {
                return;
            }
            var html = $('<div></div>');
            var query = this.query;
            var i = item.toLowerCase().indexOf(query.toLowerCase());
            var len, leftPart, middlePart, rightPart, strong;
            len = query.length;
            if(len === 0){
                return html.text(item).html();
            }
            while (i > -1) {
                leftPart = item.substr(0, i);
                middlePart = item.substr(i, len);
                rightPart = item.substr(i + len);
                strong = $('<strong></strong>').text(middlePart);
                html
                    .append(document.createTextNode(leftPart))
                    .append(strong);
                item = rightPart;
                i = item.toLowerCase().indexOf(query.toLowerCase());
            }
            return html.append(document.createTextNode(item)).html();
        },
        render: function (items) {
            var that = this;
            var self = this;
            var activeFound = false;
            items = $(items).map(function (i, item) {
              var text = self.displayText(item);
              i = $(that.options.item).data('value', item);
              var str = "<br />" + item.Phone + "<br />" + item.Email;
              i.find('a').html(that.highlighter(text)).append(str);
              if (text === self.$element.val()) {
                  i.addClass('active');
                  self.$element.data('active', item);
                  activeFound = true;
              }
              return i[0];
            });

            if (this.autoSelect && !activeFound) {
              items.first().addClass('active');
              this.$element.data('active', items.first().data('value'));
            }
            this.$menu.html(items);
            return this;
        },
        afterSelect: function(item){
            $("input[name='Email']").val(item.Email);
            $("input[name='Phone']").val(item.Phone);
        }
    });
}

function setAutocompleteClientPhone(obj){
    $("input[name='Phone']").typeahead({
        source: function(query, process){
            if(query.toString().length > 2){
                var url = $("#current_path").val() +'/Clients/FindClientByPhone/' + query;
                $.get(url,function(data){
                    arr = [];
                    $.each(data.Result, function(i,o){
                        if($.inArray(o, arr) < 0){
                            arr.push(o);
                        }
                    });
                    process(arr);
                });
            }
        },
        displayText:function(item){
            return item.Phone || item;
        },
        matcher:function(item){
            return true;
        },
        sorter:function(items){
            return items;
        },
        highlighter: function (item) {
            if(typeof item !== 'string') {
                return;
            }
            var html = $('<div></div>');
            var query = this.query;
            var i = item.toLowerCase().indexOf(query.toLowerCase());
            var len, leftPart, middlePart, rightPart, strong;
            len = query.length;
            if(len === 0){
                return html.text(item).html();
            }
            while (i > -1) {
                leftPart = item.substr(0, i);
                middlePart = item.substr(i, len);
                rightPart = item.substr(i + len);
                strong = $('<strong></strong>').text(middlePart);
                html
                    .append(document.createTextNode(leftPart))
                    .append(strong);
                item = rightPart;
                i = item.toLowerCase().indexOf(query.toLowerCase());
            }
            return html.append(document.createTextNode(item)).html();
        },
        render: function (items) {
            var that = this;
            var self = this;
            var activeFound = false;
            items = $(items).map(function (i, item) {
              var text = item.Name;//self.displayText(item);
              i = $(that.options.item).data('value', item);
              var str = "<br />" + item.Phone + "<br />" + item.Email;
              i.find('a').html(that.highlighter(text)).append(str);
              if (text === self.$element.val()) {
                  i.addClass('active');
                  self.$element.data('active', item);
                  activeFound = true;
              }
              return i[0];
            });

            if (this.autoSelect && !activeFound) {
              items.first().addClass('active');
              this.$element.data('active', items.first().data('value'));
            }
            this.$menu.html(items);
            return this;
        },
        afterSelect: function(item){
            $("input[name='Email']").val(item.Email);
            $("input[name='ClientName']").val(item.Name);
        }
    });
}

function setAutocompleteClientEmail(obj){
    $("input[name='Email']").typeahead({
        source: function(query, process){
            if(query.toString().length > 2){
                var url = $("#current_path").val() +'/Clients/FindClientByEmail/' + query;
                $.get(url,function(data){
                    arr = [];
                    $.each(data.Result, function(i,o){
                        if($.inArray(o, arr) < 0){
                            arr.push(o);
                        }
                    });
                    process(arr);
                });
            }
        },
        displayText:function(item){
            return item.Email || item;
        },
        matcher:function(item){
            return true;
        },
        sorter:function(items){
            return items;
        },
        highlighter: function (item) {
            if(typeof item !== 'string') {
                return;
            }
            var html = $('<div></div>');
            var query = this.query;
            var i = item.toLowerCase().indexOf(query.toLowerCase());
            var len, leftPart, middlePart, rightPart, strong;
            len = query.length;
            if(len === 0){
                return html.text(item).html();
            }
            while (i > -1) {
                leftPart = item.substr(0, i);
                middlePart = item.substr(i, len);
                rightPart = item.substr(i + len);
                strong = $('<strong></strong>').text(middlePart);
                html
                    .append(document.createTextNode(leftPart))
                    .append(strong);
                item = rightPart;
                i = item.toLowerCase().indexOf(query.toLowerCase());
            }
            return html.append(document.createTextNode(item)).html();
        },
        render: function (items) {
            var that = this;
            var self = this;
            var activeFound = false;
            items = $(items).map(function (i, item) {
              var text = item.Name; //self.displayText(item);
              i = $(that.options.item).data('value', item);
              var str = "<br />" + item.Phone + "<br />" + item.Email;
              i.find('a').html(that.highlighter(text)).append(str);
              if (text === self.$element.val()) {
                  i.addClass('active');
                  self.$element.data('active', item);
                  activeFound = true;
              }
              return i[0];
            });

            if (this.autoSelect && !activeFound) {
              items.first().addClass('active');
              this.$element.data('active', items.first().data('value'));
            }
            this.$menu.html(items);
            return this;
        },
        afterSelect: function(item){
            $("input[name='ClientName']").val(item.Name);
            $("input[name='Phone']").val(item.Phone);
        }
    });
}

/**
 * Función para configurar los controles del formulario
 * @returns {Void}
 */
function configurarFormulario(){
    // Configurar los arrays de bloqueos y aperturas
    setBlocksArray();
    // Configurar el array de turnos dispobles
    setTurnsArray();
    // Iniciar la colección de eventos
    setOffersEventsArray();
    // Configurar evento onChange del calentadrio
    setOnChangeCalendar();
    // Configurar las fechas seleccionadas
    setCalendars({dateFormat: 'DD, d MM yy', firstDay: 1, minDate: 0 });
    // Establecer los combos seleccionados
    setSelectedItem();
    // Configurar evento submit del formulario
    setOnSubmit();

    $('#frmReserva').on('show.bs.modal	', function (e) {
        $( "[name='Offer'] option" ).prop("selected",false);
        filterOffersByDateAndTurn();
    });

    $("#frmBooking").find("[name='Date']").on("change", function(){
        $( "[name='Offer'] option" ).prop("selected",false);
        filterOffersByDateAndTurn();
    });

    $("#frmBooking").find("[name='Turn']").on("change", function(){
        $( "[name='Offer'] option" ).prop("selected",false);
        filterOffersByDateAndTurn();
    });

    $("#frmBooking").find("[name='Offer']").on("click", function(){
        filterOffersByDateAndTurn();
    });

    filterOffersByDateAndTurn();

    setAutocompleteClientName();

    setAutocompleteClientPhone();

    setAutocompleteClientEmail();

}

/**
 * Función para cargar formulario de reservas
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function cargarFormulario(obj){
    var url = $("#current_path").val() + "/BookForm/Index/1" ;
    $.get(url,function(data){
        if(data.Result === true){
            $("#modal-body").html(data.Content);
            $("#frmReservas").modal("show");
            configurarFormulario();
        }
    });
}


$(function(){

    setCalendarioNavegacion();

    setSelectedItem();

    $.each($(".state-select"), function(i,o){
        deshabilitarOperaciones(o);
    });

    deshabilitarControlesPorFecha();

    $("[data-toggle='tooltip']").tooltip();
});
