function setBtnSimVisible(){
    $.each($(".btn-sim-check"),function(index,item){
        if($(item).data("value") === 1){
            $(item).addClass("active");
            $(item).addClass("sim-checked");
        }
    });
}

function setVisible(obj){
    var visible = $(obj).hasClass("sim-checked");
    $("#Visible").prop("checked", !visible);
    if(visible){
        $(obj).removeClass("sim-checked");
        $(obj).removeClass("active");
    }
    else{
        $(obj).addClass("sim-checked");
        $(obj).addClass("active");
    }
}


function updatePage(obj){
    // Obtiene la url de la página actual
    var href = $(obj).prop("href") + $( "#page" ).val();
    // Navega a la página
    $(obj).prop( "href" , href );
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

function setCheckedItem(){
    $.each($("input[type='checkbox']"),function(index, item){
        var value = $(item).data( "checked" );
        if(value !== undefined
                && value !== ""
                && (value === true || value === 1)){
            $(item).prop( "checked", true );
        }
    });
}

/**
 * Función para la edición de imágenes
 * @param {Object} obj Referencia al control que lanza el evento
 * @returns {Void}
 */
function editImg(obj){
    $( "[name='Id']" ).val($(obj).data("id"));
    $( "[name='Name']" ).val($(obj).data("name"));
    $( "[name='Description']" ).val($(obj).data("desc"));
    $( "[name='ImgPath']" ).val($(obj).data("path"));
    $( "[name='Date']" ).val($(obj).data("date"));
    $( "[name='file']" ).removeAttr( "required" );
}

/**
 * Visualiza el modal dialog para advertir la eliminación
 * @param rowItem referencia al enlace eliminar de la fila
 * @returns void
 */
function deleteRowItem(rowItem){
    if(rowItem !== undefined){
        // obtener el selector
        var selector = $(rowItem).data( "selector" );
        // Obtener los datos
        $(selector).data( "id" , $(rowItem).data( "id" ));
    }
}

/**
 * Realizar la petición (get) para eliminar el item seleccionado
 * @param obj Referencia al botón de eliminar
 * @returns void
 */
function deleteButton(obj){
    if(obj !== undefined){
        // Obtener url
        var url = $( obj ).data( "url" );
        // Obtener id
        var id = $( obj ).data( "id" );
        // navegación
        window.location = url+id;
    }
}

/**
 *
 * @returns {undefined}
 */
function setFrmModalOpen(){
    if($( "#validate-error" ).val() === "1"){
        $( "#frmModal" ).modal( "show" );
    }
}


function setMessageResult(){
    $.each($(".alert"),function(i,o){
        var data_class = $(o).data("class");
        if(data_class !== ""){
            $(o).removeClass("hide");
        }
        if($(o).hasClass("has-success")){
            $(o).addClass("alert-success");
        }
        elseif($(o).hasClass("has-error")){
            $(o).addClass("alert-danger");
        }
    });
}

function ocultarResultados(){
    setTimeout(function() {
        $.each($(".alert"),function(i,o){
            if($(o).hasClass("hide") === false
                    && $(o).hasClass("has-success") === true) {
                $(o).fadeOut(1000, function() {
                    $(o).find(".close").click();
                });
            }
        });
    }, 4000);
}

function setCalendarDate(obj, date){
    if(obj !== undefined && date !== "" && date !== undefined){
        var fecha = date.toString().split('-');
        if(fecha.length === 3){
           var date = new Date(fecha[0], fecha[1]-1, fecha[2]);
           $(obj).datepicker('setDate', date);
        }
    }
}

function setCalendar(options){

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
                $(item).datepicker('setDate', date);
            }
        }
        else{
            $(item).datepicker('setDate', new Date());
        }
    });
}

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
    var items = $(".menu-opt");
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

function getRequestCount(){
    var url = $("#current_path").val() + "/Pending/GetRequestCount/"
        + $("#_project").val();
    $.get(url,function(data){
        if(data.Result === true && parseInt(data.Data) > 0){
            $("#Pendientes").find(".badge").text(data.Data);
        }
        else{
            $("#Pendientes").find(".badge").text("");
        }
    });
}

$(function(){

    cambiarMenu(function(){

        getRequestCount();

        window.setInterval("getRequestCount()", 60000);

        setSelectedItem();

        setCheckedItem();

        setFrmModalOpen();

        setBtnSimVisible();

        setMessageResult();

        setCalendar({dateFormat: 'DD, d MM yy', firstDay: 1, minDate: 0 });

        ocultarResultados();

        setActivo();

        setSubMenu();


    });

});
