/**
 * Proceso de inicialización del formulario
 * @returns void
 */
$(function(){
    // Ocultar todos los campos
    $( ".attr-validation" ).hide();
    // Adaptar el formulario de validación según el tipo de dato
    $("#nodetype").change(function(){
        var value = parseInt($(this).val());
        if(value !== NaN){
            setAttributes(value);
        }
    });
    // cargar xml
    loadXml();

    if($( "#attrs" ).length === 1 && $( "#attrs" ).find("tbody").length > 0){
        $( "#attrs" ).find("tbody").sortable().disableSelection();
    }
});

/**
 * Agrega los atributos heredados de la categoría padre
 * @param {object} obj Referencia al combo de selección
 * @returns {void}
 */
function onChangeSelectCategory(obj){
    var opt = $(obj).find(":selected");
    if($(opt).data("xml") !== undefined
            && $(opt).data("xml") !== ""){
        $("#xmldoc").val($(opt).data("xml"));
        loadXml();
    }
    else{

        clearAttrTable();
    }
}

/**
 * Elimina las celdas de atributos y el campo de descripción xml
 * de la categoría
 * @returns {void}
 */
function clearAttrTable(){
    $("#xmldoc").val("");
    $("#attrs").find("tbody tr").remove();
}

/**
 * Validación de las palabras reservadas
 * @param obj Referencia al control del formulario
 * @returns boolean
 */
function ValidateName(obj){
    var result = true;
    // Eliminar el posible mensaje de error
    $(obj).next( ".help-block" ).text( "" );
    // Eliminar la clase de error
    $(obj).parent().removeClass( "has-error" );
    // Obtener el valor actual
    var value = $(obj).val();

    if(value !== undefined && value !== "" ){
        // Extraer palabras reservadas.
        var keywords = $(obj).data( "keywords" ).toString();
        // Comprobar que está contenida
        if(keywords.indexOf("-" + $(obj).val() + "-") > -1){
            // Asignar error
            $(obj).parent().addClass( "has-error" );
            // Obtener el mensaje de error
            var msg = $(obj).data( "errormsg" );
            // Asignar mensaje
            $(obj).next( "p" ).text(msg);
            // Advertir el error
            result = false;
        }
    }

    return result;
}

/**
 * Obtiene el índice del tipo de control para el formulario
 * @param sType Tipo de control html
 * @returns índice del control en el combo de formulario
 */
function getControlIndex(sType){
    // Establecer el valor por defecto
    var iType = 1;
    // Comprobar el valor
    switch(sType){
        case "text":
            iType = 1;
            break;
        case "number":
            iType = 2;
            break;
        case "date":
            iType = 3;
            break;
        case "email":
            iType = 4;
            break;
        case "checkbox":
            iType = 5;
            break;
        case "radio":
            iType = 6;
            break;
        case "select":
            iType = 7;
            break;
    }
    // Retornar el valor del combo
    return iType;
}

/**
 * Obtiene el tipo de control html a utilizar
 * @param type Índice del combo de selección
 * @returns tipo de control html
 */
function getControlType(type){
    // Valor por defecto
    var sType = "text";
    // Conversión del dato a número
    var iType = parseInt(type);
    // Validar que el índice es válido y obtener el tipo de control
    if(iType !== NaN){
        switch(iType){
            case 1:
                sType = "text";
                break;
            case 2:
                sType = "number";
                break;
            case 3:
                sType = "date";
                break;
            case 4:
                sType = "email";
                break;
            case 5:
                sType = "checkbox";
                break;
            case 6:
                sType = "radio";
                break;
            case 7:
                sType = "select";
                break;
        }
    }
    // Retornar el tipo de control
    return sType;
}

/**
 * Configurar el formulario con todos los datos de la propiedad a caracterizar
 * @param obj Referencia al objeto
 * @returns void
 */
function setData(obj){
    if(obj !== undefined ){
        // Obtener el índice para el combo
        var iType = getControlIndex(obj.type);
        // Establecer visibilidad
        setAttributes(iType);

        $( "#name" ).val(obj.name);
        $( "#label" ).val(obj.label);
        $( "#maxvalue" ).val(obj.max);
        $( "#minvalue" ).val(obj.min);
        $( "#maxlength" ).val(obj.maxlength);
        $( "#regex" ).val(obj.regex);
        $( "#title" ).val(obj.title);
        $( "#error" ).val(obj.error);
        $( "#help" ).val(obj.help);
        $( "#placeholder" ).val(obj.placeHolder);
        $( "#list" ).val(obj.list);
        // Configurar el combo
        $( "#nodetype" ).val(iType);
        // Establecer el check de required
        if(obj.required === true){
            $( "#required" ).prop( "checked", true );
        }
    }
}

/**
 * Obtiene todos los datos del formulario para caracterizar
 * una propiedad del objeto maestro
 * @returns Objeto con la información recopilada
 */
function getData(){
    // Declaración del objeto
    var obj = {
        name: $( "#name" ).val()
        ,label:$( "#label" ).val()
        ,type: getControlType($( "#nodetype" ).val())
        ,max: ($( "#maxvalue" ).val() === "") ? "" : $( "#maxvalue" ).val()
        ,min: ($( "#minvalue" ).val() === "") ? "" : $( "#minvalue" ).val()
        ,maxlength: ($( "#maxlength" ).val() === "") ? "" : $( "#maxlength" ).val()
        ,regex: ($( "#regex" ).val() === "") ? "" : $( "#regex" ).val()
        ,required:($( "#required" ).is( ":checked" )) ? true : false
        ,title:($( "#title" ).val() === "") ? "" : $( "#title" ).val()
        ,error:($( "#error" ).val() === "") ? "" : $( "#error" ).val()
        ,help:($( "#help" ).val() === "") ? "" : $( "#help" ).val()
        ,placeHolder:($( "#placeholder" ).val() === "") ? "" : $( "#placeholder" ).val()
        ,list: ($( "#list" ).val() === "") ? "" : $( "#list" ).val()
    };
    // Retornamos la caracterización
    return obj;
}

/**
 * Establece los campos de validación que se deben ver
 * @param option Tipo de dato (int)
 * @returns void
 */
function setAttributes(option){
    // Bloquear visibilidad
    $( ".attr-validation" ).css( "display", "none" );
    // Poner a 0 todas las opciones
    $( ".attr-validation" ).val( "" );
    $( ".attr-validation" ).removeAttr( "checked" );
    // Conversión del tipo de dato
    var iOption = parseInt(option);
    // Validación del índice
    if(iOption !== NaN){
        switch (iOption){
            // Texto
            case 1:
                $( "#maxlength" ).val("").show();
                $( "#regex" ).val("").show();
                break;
            // Número
            case 2:
                $( "#maxvalue" ).val("").show();
                $( "#minvalue" ).val("").show();
                break;
            // Fecha
            case 3:
                break;
            // Email
            case 4:
                $( "#maxlength" ).val("").show();
                break;
            // checklist
            case 5:
                // $( "#list" ).val("").parent().show();
                break;
             // radio buttons
            case 6:
                $( "#list" ).val("").parent().show();
                break;
            // select
            case 7:
                $( "#list" ).val("").parent().show();
                break;
        }
    }
}

/**
 * Gestión del evento submit del formulario
 * @returns {Boolean}
 */
function onSubmit(){
    // Bloquear submit
    event.preventDefault();

    if(ValidateName($( "#name" ))){
        // Obtener datos
        var obj = getData();

        if($( "#mode" ).val()  === "edit"){
            // Obtener la referencia a la fila a modificar
            var row = $( "#frm" ).data( "row" );
            // Actualizar fila
            updateRow(row, obj);
        }
        else{
            // Agregar fila
            addRow(obj);
        }
        // Limpiar el formulario
        clearForm();
        // Cerrar el modal
        $( "#dialogform" ).modal( "hide" );
    }
}

/**
 * Cargar formulario de creación
 * @returns void
 */
function newForm(){
    // Resetear el formulario
    clearForm();
    // Establecer el modo
    $( "#mode" ).val( "new" );
}

/**
 * Resetear el formulario
 * @returns void
 */
function clearForm(){
    $( "#mode" ).val( "new" );
    $( "#name" ).val( "" );
    $( "#label" ).val( "" );
    $( "#maxvalue" ).val( "" );
    $( "#minvalue" ).val( "" );
    $( "#maxlength" ).val( "" );
    $( "#regex" ).val( "" );
    $( "#title" ).val( "" );
    $( "#error" ).val( "" );
    $( "#help" ).val( "" );
    $( "#placeholder" ).val( "" );
    $( "#list" ).val( "" );
    $( "#nodetype" ).val( "" );
    $( "#required" ).removeAttr( "checked" );
    setAttributes(0);
}

/**
 * Establece los datos de la fila pasada como argumento
 * @param row referencia a la fila
 * @param obj referencia al objeto origen de los datos
 * @returns void
 */
function setRow(row, obj){
    $( row ).data( "name" , obj.name );
    $( row ).data( "label" , obj.label );
    $( row ).data( "type" , obj.type );
    $( row ).data( "max" , obj.max );
    $( row ).data( "min" , obj.min );
    $( row ).data( "maxlength" , obj.maxlength );
    $( row ).data( "regex" , obj.regex );
    $( row ).data( "required" , obj.required );
    $( row ).data( "title" , obj.title );
    $( row ).data( "error" , obj.error );
    $( row ).data( "help" , obj.help );
    $( row ).data( "placeHolder" , obj.placeHolder );
    $( row ).data( "list" , obj.list );
}

/**
  * Obtiene todos los datos de la entidad desde el objeto data de la fila
  * @param row Referencia a la fila
  * @returns Objeto con la información recopilada
  */
function readRow(row){
    // Declaración del objeto
    var obj = {
        name:$( row ).data( "name" )
        ,label:$( row ).data( "label" )
        ,type:$( row ).data( "type" )
        ,max:$( row ).data( "max" )
        ,min:$( row ).data( "min" )
        ,maxlength:$( row ).data( "maxlength" )
        ,regex:$( row ).data( "regex" )
        ,required:$( row ).data( "required" )
        ,title:$( row ).data( "title" )
        ,error:$( row ).data( "error" )
        ,help:$( row ).data( "help" )
        ,placeHolder:$( row ).data( "placeHolder" )
        ,list:$( row ).data( "list" )
    };
    // Retornamos la caracterización
    return obj;
}

/**
 * Agrega la caracterización de la propiedad a la tabla de propiedades
 * @param obj referencia al objeto con los datos
 * @returns void
 */
function addRow(obj){
    if(obj !== undefined){
        var row = $( "<tr />" )
            .data( "name", obj.name)
            .data( "type", obj.type)
            .data( "required", obj.required)
            .data( "label", obj.label)
            .data( "max", obj.max)
            .data( "min", obj.min)
            .data( "maxlength", obj.maxlength)
            .data( "regex", obj.regex)
            .data( "title", obj.title)
            .data( "error", obj.error)
            .data( "help", obj.help)
            .data( "placeHolder", obj.placeHolder)
            .data( "list", obj.list)
            .append(
                $( "<td />" ).text(obj.name)
            )
            .append(
                $( "<td />" ).text(obj.label)
            )
            .append(
                $( "<td />" ).text(obj.type)
            )
            .append(
                $( "<td />" ).text(obj.required)
            )
            .append(
                $( "<td />" ).append(
                    $( "<a />" )
                    .addClass( "btn btn-primary btn-xs" )
                    .attr( "onclick", "javascript:editRow(this);" )
                    .append($("<span />").addClass("glyphicon glyphicon-pencil"))
                )
                .append(" ")
                .append(
                    $( "<a />" )
                    .addClass( "btn btn-danger btn-xs" )
                    .attr( "onclick", "javascript:removeRow(this);" )
                    .append($("<span />").addClass("glyphicon glyphicon-remove"))
                )
            );

        $( "#attrs" ).find( "tbody" ).append(row);
    }
}

/**
 * Proceso de edición del contenido de una fila
 * @param {type} obj
 * @returns {undefined}
 */
function editRow(obj){
    // Limpiar el formulario
    clearForm();
    // Obtener la fila
    var row = $(obj).parent().parent();
    // Obtener los datos
    var data = readRow(row);
    // Setear el formulario
    setData(data);
    // Guardar referencia a la fila
    $( "#frm" ).data( "row" , row );
    // Establecer el modo
    $( "#mode" ).val( "edit" );
    // Abrir form
    $( "#dialogform" ).modal( "show" );
}

/**
 * Actualiza el contenido de la fila referenciada con los datos del objeto
 * @param row Referencia a la fila que debe actualizarse
 * @param obj Referencia al objeto de información
 * @returns void
 */
function updateRow(row, obj){
    // Vaciar el contenido de la fila
    $( row ).html( "" );
    // Completar la fila
    $( row )
        .data( "name", obj.name)
        .data( "type", obj.type)
        .data( "required", obj.required)
        .data( "label", obj.label)
        .data( "max", obj.max)
        .data( "min", obj.min)
        .data( "maxlength", obj.maxlength)
        .data( "regex", obj.regex)
        .data( "title", obj.title)
        .data( "error", obj.error)
        .data( "help", obj.help)
        .data( "placeHolder", obj.placeHolder)
        .data( "list", obj.list)
        .append(
            $( "<td />" ).text(obj.name)
        )
        .append(
            $( "<td />" ).text(obj.label)
        )
        .append(
            $( "<td />" ).text(obj.type)
        )
        .append(
            $( "<td />" ).text(obj.required)
        )
        .append(
            $( "<td />" ).append(
                $( "<a />" )
                .addClass( "btn btn-primary btn-xs" )
                .attr( "onclick", "javascript:editRow(this);" )
                .append($("<span />").addClass("glyphicon glyphicon-pencil"))
            )
            .append(" ")
            .append(
                $( "<a />" )
                .addClass( "btn btn-danger btn-xs" )
                .attr( "onclick", "javascript:removeRow(this);" )
                .append($("<span />").addClass("glyphicon glyphicon-remove"))
            )
        );
}

/**
 * Elimina la fila seleccionada y los datos asociados
 * @param obj Referencia al botón que envía la eliminación
 * @returns void
 */
function removeRow(obj){
  if(obj !== undefined){
      $(obj).parent().parent().remove();
  }
}

/**
 * Obtiene la cadena de texto correspondiente con la estructura
 * xml de todas las propiedades configuradas.
 * @returns string
 */
function getXml(){
    // Obtener las filas
    var rows = $( "#attrs" ).find( "tbody tr" );
    // Iniciar nodo de controles
    var controls = $( "<controls />" );
    // Generar los elementos del xml a partir de los datos de cada fila
    $.each(rows, function(index, item){
        // Extraer la info de la fila
        var obj = readRow(item);
        // Construir nodo xml (cadena de texto)
        var node = getXmlNode(obj);
        // Agregar nodo
        $(controls).append(node);
    });
    // Estructura xml
    var xml = $( "<doc />" )
            .append($( "<data />" )
                .attr( "vers" , "1.0")
                .append($(controls))
            );

    // parsear a string
    $( "#xmldoc" ).val(xml.html());
}

/**
 * Obtiene el nodo xml correspondiente a la información del objeto pasado
 * como argumento
 * @param obj Referencia al objeto con la información
 * @returns serialización xml de la fila
 */
function getXmlNode(obj){
    return $( "<control />" )
        .attr( "name", obj.name )
        .attr( "label", obj.label )
        .attr( "type", obj.type )
        .attr( "min", obj.min )
        .attr( "max", obj.max )
        .attr( "maxlength", obj.maxlength )
        .attr( "regex", obj.regex )
        .attr( "required", obj.required )
        .attr( "title", obj.title )
        .attr( "help", obj.help )
        .attr( "error", obj.error )
        .attr( "placeholder", obj.placeHolder )
        .attr( "list", obj.list );
}

/**
 * Cargar tabla con los nodos xml
 * @returns void
 */
function loadXml(){
    // string xml doc
    var sXmlDoc = $( "#xmldoc" ).val();
    // Validar
    if(sXmlDoc !== ""){
        // Parsear a objeto
        var xmlObj = $.parseXML(sXmlDoc);
        // Eliminar todas las filas
        $( "#attrs" ).find( "tbody" ).html( "" );

        $.each($(xmlObj).find( "control" ), function(index, item){
            // obtener la info del objeto
            var obj = getObjectFromXml(item) ;
            // Agregar fila
            addRow(obj);
        });
    }
}

/**
 * Obtener objeto desde un nodo xml
 * @param xmlNode
 * @returns object
 */
function getObjectFromXml(xmlNode){
    return {
        name:$(xmlNode).attr( "name" )
        ,label:$(xmlNode).attr( "label" )
        ,type:$(xmlNode).attr( "type" )
        ,max:$(xmlNode).attr( "max" )
        ,min:$(xmlNode).attr( "min" )
        ,maxlength:$(xmlNode).attr( "maxlength" )
        ,regex:$(xmlNode).attr( "regex" )
        ,required: ($(xmlNode).attr( "required" ) === "required" ) ? true : false
        ,title:$(xmlNode).attr( "title" )
        ,error:$(xmlNode).attr( "error" )
        ,help:$(xmlNode).attr( "help" )
        ,placeHolder:$(xmlNode).attr( "placeholder" )
        ,list:$(xmlNode).attr( "list" )
    };
}
