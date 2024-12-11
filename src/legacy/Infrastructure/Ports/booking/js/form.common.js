

/**
 * Colección de turnos disponibles
 * @type Array
 */
var $turns = Array();

/**
 * Colección de cuotas por turnos
 * @type Array
 */
var $turnsShare = Array();

/**
 * Colección de bloqueos existentes
 * @type Array
 */
var $blocks = Array();

/**
 * Colección de aperturas
 * @type Array
 */
var $opened = Array();

/**
 * Colección de eventos de ofertas para fechas y turnos concretos
 * @type Array
 */
var $offersEvents = Array();

/**
 * Colección de cupos por oferta
 * @type Array
 */
var $offersShare = Array();


/************************** INI FUNCIONES CONFIGURACIóN ***********************/

/**
 * Filtra las ofertas disponibles según la fecha y el turno
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function filterOffersByDateAndTurn(callback){
    var date = getBookingDate();
    var dayOfWeek = date.getUTCDay();
    var turn = $( "[name='Turn'] option:selected" ).val();
    if(!isNaN(parseInt(dayOfWeek)) &&  !isNaN(parseInt(turn))){
        dayOfWeek++;
        $( "[name='Offer'] option" ).show();
        $.each( $( "[name='Offer'] option" ), function(index, item){
            var value = parseInt($(item).val());
            if($.isNumeric(value) && value > 0){
                var config = $(item).data( "config" );
                var start = $(item).data( "start" );
                var end = $(item).data( "end" );
                var $visible = false;
                var fechas = validateDatesOfOffers(date, start, end);
                $.each(config, function(i,c){
                    if(c.Day === dayOfWeek && c.Turn === parseInt(turn)){
                        $visible = true;
                    }
                });
                if($visible && fechas){
                    $(item).show();
                }
                else{
                    $(item).hide();
                }
            }
        });
        filterOffersEvents(date, turn, true);
    }
    if($.isFunction(callback)){
        callback();
    }
}

/**
 * Proceso para establecer las ofertas disponibles en función de
 * la fecha que se ha selecionado.
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function setOfferByDateAndTurn(callback){
    var $date = getBookingDate();
    var $dayOfWeek = $date.getUTCDay();
    var $turn = $( "[name='Turn'] option:selected" ).val();
    if(!isNaN(parseInt($dayOfWeek)) && !isNaN(parseInt($turn))){
        $dayOfWeek++;
        $.each( $( ".offer-item" ), function(index, item){
            var config = $(item).data( "config" );
            var start = $(item).data( "start" );
            var end = $(item).data( "end" );
            setOffer(config, $dayOfWeek, $turn, item, $date, start, end);
        });
    }
    if($.isFunction(callback)){
        filterOffersEvents($date, $turn);
        callback();
    }
}

/**
 * Obtiene una instancia Date a partir de una fecha en formato Y-m-d
 * @param {string} sDate fecha a parsear
 * @returns {Date} Referencia al objeto date
 */
function getDateByString(sDate){
    var date = parseDateString(sDate);
    return new Date(date.Year, date.Month, date.Day );
}

/**
 * Agrega los turnos que han sido "abiertos" por el restaurante
 * @param {Date} date Fecha para la que configurar los turnos
 * @param {Array} temp Array de turnos disponibles
 * @param {Function} callback Función de regreso
 * @returns {Array} Array de turnos actualizados
 */
function addOpenedTurns(date, temp, callback){
    // Filtrar los turnos por bloqueos
    $.each($opened, function(index, item){
        var openedDate = getDateByString(item.Date);
        if((item.Block === 1)
                && date.getTime() === openedDate.getTime()) {
            var result = $.grep(temp, function(e){
                return parseInt(e.id) === item.Turn;
            });
            // Si no se ha encontrado turno, lo buscamos y asignamos
            if(result.length === 0){
                var turns = $.grep($turns,function(e){
                    return parseInt(e.id) === item.Turn;
                });
                if(turns.length !== 0){
                    temp.push(turns[0]);
                }
            }
        }
    });
    if($.isFunction(callback)){
        callback(temp);
    }
    else{
        return temp;
    }
}

/**
 * Filtra la lista de turnos disponibles con los bloques
 * @param {Date} date Referencia a la fecha para filtrar
 * @param {Array} temp Colección de turnos disponibles
 * @param {Function} callback Función de retorno
 * @returns {Array} Colección de turnos actualizados
 */
function filterBlockedTurns(date, temp, callback){
    $.each($blocks, function(index, item){
        var blockDate = getDateByString(item.Date);
        if(( item.Block === 0 )
                && date.getTime() === blockDate.getTime()){
            var result = $.grep(temp, function(e){
                return parseInt(e.id) === parseInt(item.Turn);
            });
            $.each(result, function(i, o){
                var indexItem = temp.indexOf(o);
                if(indexItem > -1){
                    temp.splice(indexItem, 1);
                }
            });
        }
    });
    if($.isFunction(callback)){
        callback(temp);
    }
    else{
        return temp;
    }
}

/**
 * Función para ordenar los turnos por su propiedad Start
 * @param {type} a
 * @param {type} b
 * @returns {Number}
 */
function SortByStart(a, b){
  var aName = a.text.toString().toLowerCase();
  var bName = b.text.toString().toLowerCase();
  return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
}

/**
 * Establece la colección de objetos Option en el combo de turnos
 * @param {Array} temp Array de turnos a configurar
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function setOptions(temp, callback){

    $( "[name='Turn'] option" ).remove();

    temp.sort(SortByStart);

    var date = getBookingDate();
    var current = new Date();

    $.each(temp, function(index, item){
        if(validateTurnShare(date, item, current) === true){
            if(validateTime(date, item, current) === true){
                var opt = $( "<option />" )
                .data( "slot", item.slot)
                .data( "days", item.days)
                .text(item.text)
                .val(item.id);
                $( "[name='Turn']" ).append(opt);
            }
        }
    });

    if($( "[name='Turn'] option" ).length === 0){
        var opt = $( "<option />" )
                .data( "slot", -1)
                .data( "days", -1)
                .text("No hay turnos disponibles")
                .prop("disabled", "disabled")
                .val(-1);
        $( "[name='Turn']" ).append(opt);
    }

    var first = $($( "[name='Turn'] option" )[0]).val();

    $( "[name='Turn']" ).val(first).data("value",first);

    if($.isFunction(callback)){
        callback();
    }
}

/**
 * Establecer|Configurar el combo de turnos con la fecha seleccionada
 * @param {Function} callbacks Función de retorno
 * @returns {void}
 */
function setTurns(callbacks){
    var date = getBookingDate();
    var $dow = date.getUTCDay() + 1;
    var temp = Array();
    var current = new Date();
    $.each($turns, function(index, item){
        if($.inArray($dow, item.days)!== -1){
            if(validateTurnShare(date, item, current) === true){
                if(validateTime(date, item, current) === true){
                    temp.push(item);
                }
            }
        }
    });
    if($.isFunction(callbacks)){
        callbacks(temp);
    }
}

/**
 * Handler para la selección de fecha en el calendario
 * @returns {void}
 */
function setOnChangeCalendar(){
    $( ".calendar" ).on( "change", function(){
        setTurns(function(temp){
            var date = getBookingDate();
            filterBlockedTurns(date, temp, function(temp){
                addOpenedTurns(date, temp, function(temp){
                    setOptions(temp);
                });
            });
        });
    });
}

/**
 * Proceso previo al submit
 * @returns {void}
 */
function setOnSubmit(){
    $("form").on( "submit", function(){
        // formatear fecha
        $(".calendar").datepicker( "option", "dateFormat","yy-m-d");
        $(".has-error .help-block").html("&nbsp;");
        $(".has-error").removeClass("has-error");
    });
}

/**
 * Establece los bloqueos y aperturas disponibles en
 * los arrays correspondientes
 * @returns {void}
 */
function setBlocksArray(){
    $blocks = Array();
    $opened = Array();
    if($("#blocks_array") !== undefined
            && $("#blocks_array").length === 1
            && $("#blocks_array").val() !== "" ){
        var blocks = $.parseJSON($("#blocks_array").val());
        $blocks = $.grep(blocks, function(e){ return e.Block === 0; });
        $opened = $.grep(blocks, function(e){ return e.Block === 1; });
    }
}

/**
 *
 * @param {DateTime} date
 * @returns {undefined}
 */
function formatDate(date){
    var y = date.getFullYear();
    var m = (date.getMonth() + 1);
    m = ( m < 9) ? "0" + m : m;
    var d = (date.getDate());
    d = ( d < 9) ? "0" + d : d;
    return y + "-" + m + "-" + d;
}

/**
 * Proceso que realiza el filtrado de ofertas según la lista
 * de eventos asociados que se han generado
 * @param {DateTime} date Fecha de la reserva
 * @param {String} turn Turno asociado a la reserva
 * @param {Boolean} select Flag para indicar que formulario se está utilizando
 * @returns {Void}
 */
function filterOffersEvents(date, turn, select){
    date = formatDate(date);
    turn = parseInt(turn);
    var eventos = $.grep($offersEvents, function(e){
        return e.Turn === turn && e.Date === date; });
    $.each(eventos, function(i,o){
        var item = null;
        if(select === true){
            item = $("select[name='Offer'] option[value='" + o.Offer + "']");

        }else{
            item = $("input[name='Offer'][value='" + o.Offer + "']")
                    .parent().parent();
        }
        if(o.State === 1){
            $(item).show();
        }
        else{
            $(item).hide();
        }
    });
}

/**
 * Configuración de los eventos de ofertas
 * @returns {void}
 */
function setOffersEventsArray(){
    $offersEvents = Array();
    if($("#offers_events_array") !== undefined
            && $("#offers_events_array").length === 1
            && $("#offers_events_array").val() !== "" ){
        $offersEvents = $.parseJSON($("#offers_events_array").val());
    }
}

function setOffersShareArray(){
    $offersShare = Array();
    if($("#offers_share_array") !== undefined
            && $("#offers_share_array").length === 1
            && $("#offers_share_array").val() !== "" ){
        $offersShare = $.parseJSON($("#offers_share_array").val());
    }
}

/**
 * Configura el array de turnos disponibles
 * @returns {void}
 */
function setTurnsArray(){
    $turns = Array();
    // Iniciar array de turnos
    $.each( $( "[name='Turn'] option" ), function(index, item){
        var obj = {
            id: $(item).val(),
            slot:$(item).data( "slot" ),
            days: $(item).data( "days" ),
            text: $(item).text()
        };
        $turns.push(obj);
    });
}

/**
 * Configurar las colecciones de cuotas
 * @returns {Void}
 */
function setTurnsShareArray(){
    $turnsShare = Array();
    if($("#turns_share_array") !== undefined
            && $("#turns_share_array").length === 1
            && $("#turns_share_array").val() !== "" ){
        $turnsShare = $.parseJSON($("#turns_share_array").val());
    }
}

/**
 * Configuración de las ofertas disponibles
 * @param {Array} data Colección de objetos de configuración
 * @param {int} day
 * @param {int} turn
 * @param {object} item Referencia al objeto html de oferta
 * @param {object} date Referencia al objeto Date de la fecha de reserva
 * @param {string} start Fecha de inicio de la oferta
 * @param {string} end Fecha de finalización de la oferta
 * @returns {void}
 */
function setOffer(data, day, turn, item, date, start, end){
    // Por defecto las ofertas no se ven
    var $visible = false;
    // Validación de la fecha
    var fechas = validateDatesOfOffers(date, start, end);
    // Validación de configuración
    $.each( data, function(i,c){
        if(c.Day === day && c.Turn === parseInt(turn)){
            $visible = true;
        }
    });

    if($visible && fechas){
        $(item).parent().parent().show();
    }
    else{
        $(item).parent().parent().hide();
    }

    filterOffersShare(date, turn);
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
 * Configurar la fecha preestablecida de los controles calendario
 * @param {object} options Referencia a las opciones
 * de configuración del datepicker
 * @returns {void}
 */
function setCalendars(options){

    $.extend({dateFormat: 'dd-mm-yy',
        firstDay: 1, minDate: 0}, options);

    // Configuración del calendario
    $( ".calendar" ).datepicker(options);
    // Setear la fecha establecida
    $.each($( ".calendar" ), function(index, item){
        if($( item ).data( "value" ) !== undefined
                && $( item ).data( "value" ) !== ""){
             var fecha = $( item ).data( "value" )
                     .toString().split('-');
            if(fecha.length === 3){
                var date = new Date(fecha[0], fecha[1]-1, fecha[2]);
                $( item ).datepicker('setDate', date);
            }
        }
        else{
            $( item ).datepicker('setDate', new Date());
        }
    });

    setTurns(function(temp){
        var date = getBookingDate();
        filterBlockedTurns(date, temp, function(temp){
            addOpenedTurns(date, temp, function(temp){
                setOptions(temp, function(){
                    if($( "[name='Turn'] option" ).length > 0){
                        $($( "[name='Turn'] option" )[0])
                                .prop("selected", true);
                    }
                });
            });
        });
    });
}

/**
 * Obtiene una instancia Date con la fecha seleccionada del calendario
 * @returns {Date} Referencia a la instancia Date
 */
function getBookingDate(){
    try{
        $(".calendar").datepicker( "option", "dateFormat", "dd-mm-yy");
        var sdate = $( "[name='Date']" ).val();
        $(".calendar").datepicker( "option", "dateFormat", "DD, d MM yy");
        if(sdate !== "" && sdate !== undefined){
            var o = {
                Year: parseInt(sdate.substr(6,4)),
                Month: parseInt(sdate.substr(3,2))-1,
                Day: parseInt(sdate.substr(0,2))
            };
            return new Date(o.Year, o.Month, o.Day);
        }
        return null;
    }
    catch(err){
        return null;
    }
}

/**
 * Obtiene un objeto con las propiedades Year, Month y Day configuradas con
 * la información de la fecha utilizada como argumento(en formato Y-m-d)
 * @param {string} sDate Fecha con formato Y-m-d
 * @returns {object} Referencia al objeto con los datos
 */
function parseDateString(sDate){
    try{
        if(sDate !== "" && sDate !== undefined){
            return {
                Year:parseInt(sDate.substr(0,4)),
                Month:parseInt(sDate.substr(5,2))-1,
                Day:parseInt(sDate.substr(8,2))
            };
        }
        return null;
    }
    catch(err){
        return null;
    }
}

/**
 * Proceso de validación de los cupos por turno
 * @param {type} date Referencia a la Fecha de la reserva
 * @param {type} item Referencia al turno
 * @returns {Boolean} Resultado de la validación
 */
function validateTurnShare(date, item){
    // obtener cadena de búsqueda
    var sdate = date.getFullYear() + "-"
            + (date.getMonth()+1) + "-" + date.getDate();
    var share = $.grep($turnsShare, function(ts){
        return ts.Date.toString().replace("-0","-").replace("-0","-") === sdate
                && parseInt(ts.Turn) === parseInt(item.id)
                && (parseInt(ts.DinersFree) <= 0 );
    });
    return $(share).length === 0;
}

/**
 * Comprobación de la hora de la reserva y el turno
 * @param {Object} date Referencia a la fecha de la reserva
 * @param {Object} item Referencia al turno
 * * @param {Date} currentDate Referencia a la fecha actual
 * @returns {Boolean}
 */
function validateTime(date, item, currentDate){
    // Ventana de tiempo en minutos para validar el turno
    var timeSpan = 20;
    //var currentDate = new Date();
    if(currentDate.getDate() === date.getDate()
            && currentDate.getMonth() === date.getMonth()
            && currentDate.getYear() === date.getYear()){
        var currentHour = currentDate.getHours();
        var currentMinutes = currentDate.getMinutes();

        if(currentMinutes >= 60 - timeSpan){
            currentHour++;
            currentMinutes = currentMinutes + timeSpan - 60;
        }
        else{
            currentMinutes = currentMinutes + timeSpan;
        }

        var time = item.text.toString().split(":");

        if($(time).length === 2){
            var result = (currentHour < parseInt(time[0])
                    || (currentHour === parseInt(time[0])
                        && currentMinutes < parseInt(time[1])));
            return result;
        }
    }

    return true;
}


/**
 * Proceso de validación del nombre de cliente
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function validate_client(error, callback){
    var client_error_str = $( "#ClientName-Error" ).text();
    // Comprobar nombre
    if($( "[name='ClientName']" ).val() === undefined
            || $( "[name='ClientName']" ).val() === "" ){
        $( "#ClientName-Error" ).parent().addClass( "has-error" );
        $( "#ClientName-Error" ).text( client_error_str
                + "Debe especificar una nombre para la reserva." );
        error = true;
    }
    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Proceso de validación del e-mail de contacto
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function validate_email(error, callback){
    var email_error_str = $( "#Email-Error" ).text();
    // Comprobar email
    if($( "[name='Email']" ).val() === undefined
            || $( "[name='Email']" ).val() === "" ){
        $( "#Email-Error" ).parent().addClass( "has-error" );
        $( "#Email-Error" ).text( email_error_str
                + "Debe especificar una dirección de contacto." );
        error = true;
    }
    else if(!validateEmail( $( "[name='Email']" ).val())){
        $( "#Email-Error" ).parent().addClass( "has-error" );
        $( "#Email-Error" ).text( email_error_str
                + "La dirección no corresponde con formato e-mail." );
        error = true;
    }
    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Proceso de validación del teléfono de contacto
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function validate_phone(error, callback){
    var phone_error_str = $( "#Phone-Error" ).text();
    if($( "[name='Phone']" ).val() === undefined
            || $( "[name='Phone']" ).val() === "" ){
        $( "#Phone-Error" ).parent().addClass( "has-error" );
        $( "#Phone-Error" ).text( phone_error_str
                + "Debe especificar un teléfono de contacto." );
        error = true;
    }
    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Validación del formato e-mail
 * @param {string} email Dirección de e-mail a validar
 * @returns {Boolean} Resultado de la validación
 */
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function filterOffersShare(oDate, turn){

    turn = parseInt(turn);
    var turns = $.grep($turns, function(e){
        return parseInt(e.id) === turn;
    })
    if($(turns).length === 0){
        return;
    }
    var slot = parseInt(turns[0].slot);

    // obtener cadena de búsqueda
    var sdate = oDate.getFullYear() + "-" + (oDate.getMonth()+1) + "-" + oDate.getDate();

    // cuotas a filtrar
    var share = $.grep($offersShare, function(e){
        return e.Date.toString().replace("-0","-").replace("-0","-") === sdate
                && parseInt(e.Slot) === slot;
    });

    $("[name='Offer']").removeAttr("disabled").prop("disabled", false);
    $("label[for*='rb_offer_']").css("text-decoration","");
    $.each(share, function(i, o){
        if(parseInt(o.DinersFree) <= 0){
            $( "#rb_offer_" + o.Offer)
                    .attr("disabled", "disabled")
                    .prop("disabled", true);
            $("label[for*='rb_offer_" + o.Offer + "']")
                    .css("text-decoration","line-through");
        }
    });
}

/**
 * Proceso de validación de oferta
 * @param {object} oDate Fecha de reserva
 * @param {string} start Fecha de inicio de oferta
 * @param {string} end Fecha de fin de oferta
 * @returns {Boolean} Estado de la validación
 */
function validateDatesOfOffers(oDate, start, end){
    try{
        if(oDate !== null && oDate !== undefined){

            var startDate = null;
            var endDate = null;

            var oStartDate = parseDateString(start);
            if(start !== "0000-00-00 00:00:00"
                    && start !== undefined
                    && oStartDate !== null){
                startDate = new Date(oStartDate.Year,
                    oStartDate.Month,oStartDate.Day );
            }

            var oEndDate = parseDateString(end);
            if(end !== "0000-00-00 00:00:00"
                    && end !== undefined
                    && oEndDate !== null){
                endDate = new Date(oEndDate.Year,
                    oEndDate.Month,oEndDate.Day );
            }

            var startOK = (startDate === null)
                    || (startDate !== null
                    && startDate.getTime() <= oDate.getTime() );

            var endOK = (endDate === null)
                    || (endDate !== null
                    && endDate.getTime() >= oDate.getTime());

            return (startOK && endOK);
        }
    }
    catch(err){
        return false;
    }
    return false;
}

/**
 * Proceso de validación de la fecha seleccionada
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback función de retorno
 * @returns {void}
 */
function validate_date(error, callback){
    // Obtener los mensajes de erro
    var date_error_str = $( "#Date-Error" ).text();
    // Comprobar fecha
    if($( "[name='Date']" ).val() === undefined
            || $( "[name='Date']" ).val() === "" ){
        $( "#Date-Error" ).parent().addClass( "has-error" );
        $( "#Date-Error" )
                .text( date_error_str + "Debe especificar una fecha." );
            error = true;
    }
    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Proceso de validación del número de comensales
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function validate_diners(error, callback){

    var diners_error_str = $( "#Diners-Error" ).text();
    // Comprobar comensales
    if($( "[name='Diners']" ).val() === undefined
            || $( "[name='Diners']" ).val() === ""
            || isNaN($( "[name='Diners']" ).val())
            || (!isNaN($( "[name='Diners']" ).val())
                    && $( "[name='Diners']" ).val() < 1)){
        $( "#Diners-Error" ).parent().addClass( "has-error" );
        $( "#Diners-Error" )
                .text( diners_error_str + "Debe especificar los comensales." );
        error = true;
    }
    // Comprobar que el número de comensales es un entero
    else if( isNaN(parseInt($( "[name='Diners']" ).val()))){
        $( "#Diners-Error" ).parent().addClass( "has-error" );
        $( "#Diners-Error" ).text( diners_error_str +
                "El tipo de dato introducido es incorrecto. P.e.: 4 " );
        error = true;
    }

    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Proceso de validación del turno seleccionado
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function validate_turn(error, callback){
    var turn_error_str = $( "#Turn-Error" ).text();
    if($( "select[name='Turn']" ).val() === undefined
            || $( "select[name='Turn']" ).val() === null
            || $( "select[name='Turn']" ).val() === ""
            || $( "select[name='Turn']" ).val() === "-1"
            || $( "select[name='Turn']" ).val() === -1 ){
        $( "#Turn-Error" ).parent().addClass( "has-error" );
        $( "#Turn-Error" )
                .text( turn_error_str + "Debe seleccionar un turno." );
        error = true;
    }
    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Proceso de validación del lugar seleccionado
 * @param {Boolean} error Estado de la validación actual
 * @param {Function} callback Función de retorno
 * @returns {void}
 */
function validate_place(error, callback){
    var places_error_str = $( "#Place-Error" ).text();
    if($( "select[name='Place']" ).val() === undefined
            || $( "select[name='Place']" ).val() === null
            || $( "select[name='Place']" ).val() === ""
            || $( "select[name='Place']" ).val() === "-1"
            || $( "select[name='Place']" ).val() === -1 ){
        $( "#Place-Error" ).parent().addClass( "has-error" );
        $( "#Place-Error" )
                .text( places_error_str + "Debe seleccionar un lugar." );
        error = true;
    }
    if($.isFunction(callback)){
        callback(error);
    }
}

/**
 * Función para obtener los datos del cliente a partir de su teléfono
 * @param {Object} obj Referencia al control
 * @returns {Void}
 */
function getClientData(obj){

    if($(obj).val().toString().length > 3){
    var url = $("#current_path").val() + "/Clients/FindClient/" + $(obj).val();
        $.get(url, function(data){
           if(data.Result !== undefined && $.isArray(data.Result)){
               if($(data.Result).length > 0){
                   var client = data.Result[0];
                   $("input[name='ClientName']").val(client.Name);
                   $("input[name='Email']").val(client.Email);
               }
           }
        });
    }
}
