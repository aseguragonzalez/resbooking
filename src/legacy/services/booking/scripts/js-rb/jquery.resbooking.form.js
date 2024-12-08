/************************** INI FUNCIONES VALIDACIóN **************************/

/**
 * Reinicia los mensajes de error de la pantalla 1 del formulario
 * @returns {void}
 */
function resetForm1(){

    var date_error_str = $( "#Date-Error" )
            .text()
            .toString()
            .replace( "Debe especificar una fecha." , "" );

    var diners_error_str = $( "#Diners-Error" )
            .text()
            .toString()
            .replace( "Debe especificar los comensales." , "" )
            .replace( "El tipo de dato introducido es incorrecto. P.e.: 4 " , "" );

    var turn_error_str = $( "#Turn-Error" )
            .text()
            .toString()
            .replace( "Debe seleccionar un turno." , "" );

    var places_error_str = $( "#Place-Error" )
            .text()
            .toString()
            .replace( "Debe seleccionar un lugar." , "" );

    $( "#Date-Error" ).text(date_error_str);
    $( "#Diners-Error" ).text(diners_error_str);
    $( "#Turn-Error" ).text(turn_error_str);
    $( "#Place-Error" ).text(places_error_str);
    $( "#Date-Error" ).parent().removeClass( "has-error" );
    $( "#Diners-Error" ).parent().removeClass( "has-error" );
    $( "#Turn-Error" ).parent().removeClass( "has-error" );
    $( "#Place-Error" ).parent().removeClass( "has-error" );
}

/**
 * Reinicia los mensajes de error de la pantalla 2 del formulario
 * @returns {undefined}
 */
function resetForm2(){

    var client_error_str = $( "#ClientName-Error" )
            .text()
            .toString()
            .replace( "Debe especificar una nombre para la reserva." , "" );

    var email_error_str = $( "#Email-Error" )
            .text()
            .toString()
            .replace( "Debe especificar una dirección de contacto." , "" )
            .replace( "La dirección no corresponde con formato e-mail." , "" );

    var phone_error_str = $( "#Phone-Error" )
            .text()
            .toString()
            .replace( "Debe especificar un teléfono de contacto." , "" );

    $( "#ClientName-Error" ).text(client_error_str);
    $( "#Email-Error" ).text(email_error_str);
    $( "#Phone-Error" ).text(phone_error_str);
    $( "#ClientName-Error" ).parent().removeClass( "has-error" );
    $( "#Email-Error" ).parent().removeClass( "has-error" );
    $( "#Phone-Error" ).parent().removeClass( "has-error" );
}

/**
 * Validación de la pantalla 1 del formulario
 * "Datos de la reserva"
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function valStep1(callback){
    // Reiniciar los mensajes de error
    resetForm1();
    // Proceso de validación
    validate_date(false, function(error){
        validate_diners(error,function(error){
            validate_turn(error, function(error){
                validate_place(error, function(error){
                    if(!error && $.isFunction(callback)) {
                        callback();
                    }
                });
            });
        });
    });
}

/**
 * Validación de la pantalla 2 del formulario
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function valStep2(callback){
    resetForm2();
    validate_client(false,function(error){
        validate_email(error, function(error){
            validate_phone(error, function(error){
                if(!error && $.isFunction(callback)){
                    callback();
                }
            });
        });
    });
}

/**
 * Proceso de validación de la pantalla 3
 * "Selección de oferta y comentario"
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function valStep3(callback){
    var error = false;
    if(!error && $.isFunction(callback)){
        callback();
    }
}

/**
 * Proceso de validación de la ventana de confirmación
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function valStep4(callback){
    var error = false;
    if(!error && $.isFunction(callback)){
        callback();
    }
}

/**
 * Callback del paso 1
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function callbackStep1(callback){
    setOfferByDateAndTurn(function(){
       if($.isFunction(callback)){
            callback();
        }
    });
}

/**
 *
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function callbackStep2(callback){
    if($.isFunction(callback)){
        callback();
    }
}

/**
 *
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function callbackStep3(callback){
    // var date = getBookingDate();
    // Obtiene la fecha en formato texto
    var date = $( "[name='Date']" ).val();
    // Extraer el texo de la oferta seleccionada
    try{
        var oferta = $( "[name='Offer']:checked" )
                .parent().text().toString().trim();
    }
    catch(err){
        oferta = "Sin oferta" ;
    }

    showZapperPrePay();

    oferta = ( oferta !== "" ) ?  oferta : "Sin oferta" ;

    $( "#nombre" ).text( $( "[name='ClientName']" ).val());
    $( "#email" ).text( $( "[name='Email']" ).val());
    $( "#telefono" ).text( $( "[name='Phone']" ).val());
    $( "#fecha" ).text(date);
    $( "#hora" ).text( $( "[name='Turn'] option:selected" ).text());
    $( "#comensales" ).text( $( "[name='Diners']" ).val());
    $( "#lugar" ).text( $( "[name='Place'] option:selected" ).text());
    $( "#oferta" ).text( oferta );
    if($.isFunction(callback)){
        callback();
    }
}

function showZapperPrePay(){

    var url = "/Zapper/RequiredPrePay";
    var date = getBookingDate();
    var sdate = date.getFullYear() + "-"
            + (date.getMonth() + 1) + "-"
            + date.getDate();
    var dto = {
        project : $("input[name='Project']").val(),
        offer:$("input[name='Offer']:checked").val(),
        diners:$("[name='Diners']").val(),
        date:sdate
    };
	//Carlos
	try{
        var oferta = $( "[name='Offer']:checked" )
                .parent().text().toString().trim();
    }
    catch(err){
        oferta = "Sin oferta" ;
    }
	//Fin Carlos

    $("#zapper-container").addClass("hide");
    $.get(url, dto, function(response){
        if(response.Result === true){
            $("#standard").addClass("hide");
            $("#zapper").removeClass("hide");
            $("#zapper-container").removeClass("hide");
            $("#sName").text($("[name='ClientName']").val());
            $("#sEmail").text($("[name='Email']").val());
            $("#sPhone").text($("[name='Phone']").val());
            $("#sDate").text($("[name='Date']").val());
			// Carlos
			$("#sTime").text($( "[name='Turn'] option:selected" ).text());
			$("#sOffer").text(oferta);
            // Fin Carlos
			$("#sDiners").text($("[name='Diners']").val());

            var diners = parseInt($("[name='Diners']").val());

            $("#sAmount").text(response.Amount.AmountByDiner*diners);
        }
        else{
            $("#zapper-container").addClass("hide");
            $("#zapper").addClass("hide");
            $("#standard").removeClass("hide");
        }
    });
}

/**
 *
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function callbackStep4(callback){
    if($.isFunction(callback)){
        callback();
    }
}


/************************** FIN FUNCIONES VALIDACIóN **************************/

/************************** INI FUNCIONES NAVEGACIóN **************************/

/**
 * Carga el siguiente paso(pantalla)
 * @param {Object} obj Referencia al objeto pulsado
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function next(obj, callback){
    var step = $(obj).parent().parent().data( "step" );
    // var step = $($obj).parent().parent().data( "step" );
    if(step === undefined || step === ""){
        return;
    }
    step = parseInt(step) +1;
    $( ".rb-module" ).hide();
    $( "div[data-step='" + step + "']" ).show();
    if($.isFunction(callback)){
        callback();
    }
}

/**
 * Navegación al paso(pantalla) siguiente
 * @param {Object} obj Referencia al objeto pulsado
 * @returns {void}
 */
function nextStep(obj){
    var validation = $(obj).parent().parent().data( "validate" );
    var $callback = $(obj).parent().parent().data( "callback" );
    var setVal = validation !== undefined && validation !== "";
    var setCB = $callback !== undefined && $callback !== "";
    $obj = obj;
    if(setVal && !setCB){
        window[validation](function(){
            next($obj);
       });
    }
    else if(setVal && setCB){
        window[validation](function(){
            next($obj, function(){
                window[$callback]();
            });
       });
    }
    else if(!setVal && setCB){
        next($obj, function(){
            window[$callback]();
        });
    }
    else{
        next($obj);
    }
}

/**
 * Navegación al paso(pantalla) anterior
 * @param {object} obj Referencia al objeto pulsado
 * @returns {void}
 */
function prevStep(obj){
    var step = $(obj).parent().parent().data( "step" );
    if(step === undefined || step === ""){
        return;
    }
    step = parseInt(step) - 1;
    $( ".rb-module" ).hide();
    $( "div[data-step='" + step + "']" ).show();
}

/************************** FIN FUNCIONES NAVEGACIóN **************************/

/**
 * Establece el método indexOf para la clase Array si es que no
 * está definido
 * @returns {void}
 */
function SetArrayIndexOfMethod(){
    if (!Array.prototype.indexOf){
        Array.prototype.indexOf = function(elt){
            var len = this.length >>> 0;
            var from = Number(arguments[1]) || 0;
            from = (from < 0) ? Math.ceil(from) : Math.floor(from);
            if (from < 0){
                from += len;
            }
            for (; from < len; from++){
                if (from in this && this[from] === elt){
                    return from;
                }
            }
            return -1;
      };
    }
}

/**
 * Handler del evento Onload
 * @returns {void}
 */
$(function(){
    // Configurar los arrays de bloqueos y aperturas
    setBlocksArray();
    // Configurar el array de turnos dispobles
    setTurnsArray();
    // Iniciar la colección de eventos
    setOffersEventsArray();
    // Cargar la colección de cuotas
    setOffersShareArray();

    setTurnsShareArray();
    // Configurar evento onChange del calentadrio
    setOnChangeCalendar();
    // Configurar las fechas seleccionadas
    setCalendars({dateFormat: 'DD, d MM yy', firstDay: 1, minDate: 0 });
    // Establecer los combos seleccionados
    setSelectedItem();

    // Configurar evento submit del formulario
    setOnSubmit();
    // Configuración del evento
    SetArrayIndexOfMethod();
});
