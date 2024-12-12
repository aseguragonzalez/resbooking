$(function(){
    var select = $("select[name='Category']");
    onChange(select);
});

/**
 * Obtiene la serialización json del formulario de atributos
 * @returns string
 */
function getFormData(){
    var str = "";
    // Extraer controles básicos
    var length = $( "#frmXml" ).find( ".form-control" ).length;
    $.each($( "#frmXml" ).find( ".form-control" ),function(index, item){
        str += '"' + $(item).attr( "name" )
                + '":"' + $(item).val() + '"';
        if(index < (length -1)){
           str += ",";
        }
    });

    // Extraer checkbox
    var length = $( "#frmXml" ).find( "[type='checkbox']" ).length;
    $.each($( "#frmXml" ).find( "[type='checkbox']" ),function(index, item){
        if(length > 0 && str !== ""){
            str += ", ";
        }
        str += '"' + $(item).attr( "name" )
                + '":"' + $(item).is( ":checked" ) + '"';
        if(index < (length -1)){
           str += ",";
        }
    });

    // Extraer radio
    var length = $( "#frmXml" ).find( "[type='radio']:checked" ).length;
    $.each($( "#frmXml" ).find( "[type='radio']:checked" ),function(index, item){
        if(length > 0 && str !== ""){
            str += ", ";
        }
        str += '"' + $(item).attr( "name" )
                + '":"' + $(item).val() + '"';
        if(index < (length -1)){
           str += ",";
        }
    });

    return "{" + str + "}";
}

/**
 * Establece los valores del formulario de atributos con el objeto con los
 * datos de atributos
 * @param obj Referencia al objeto de atributos del producto
 * @returns void
 */
function setFormData(obj){
    if(obj !== undefined){
        $.map(obj, function(value, index) {

            var control = $( "[name='" + index + "']" );

            var length = control.length;

            if(length === 1){
                if($(control).attr( "type" ) === "checkbox"){
                    $(control).prop( "checked" , (value === "true" ) );
                }
                else{
                    $( control ).val(value);
                }
            }
            elseif(length > 1){

                var tagName = $(control).prop( "tagName" );

                var type = $(control).attr( "type" );

                if(tagName === "INPUT" && type === "radio" ){
                    $("input[name='" + index + "'][value='" + value + "']")
                            .prop( "checked", true );
                }
            }
        });
    }
}

/**
 * Cargar todos los datos de los atributos en el formulario
 * @returns void
 */
function loadAttr(){
  // Obtener la serialización actual
    var strAttr = $( "#attr" ).val();
    // Parsear si corresponde
    if(strAttr !== ""){
        // Parsear objeto
        var attr = $.parseJSON(strAttr);
        // Establecer los valores en la ficha
        setFormData(attr);
    }
}

/**
 * Procesado previo al envío del formulario
 * @returns void
 */
function onSubmit(){
    // Obtener la serialización de atributos
    var strAttr = getFormData();
    // Setear el campo con la serialización
    $( "#attr" ).val(strAttr);
}

/**
 * Manejador para la selección de la categoría
 * @param obj referencia al combo de selección de la categoría
 * @returns void
 */
function onChange(obj){
    setFormByOptionSelected(obj, function(){
        loadAttr();
    });
}

/**
 *
 * @param {type} code
 * @returns {undefined}
 */
function getReference(code){
    var date = new Date();
    var ref = date.getFullYear();
    var month = date.getMonth();
    var day = date.getDay();
    var hours = date.getHours();
    var min = date.getMinutes();
    var sec = date.getSeconds();
    var temp = "";
    month++; day++;
    ref += (month < 10) ? "0" + month : month;
    ref += (day < 10) ? "0" + day : day;
    ref += (hours < 10) ? "0" + hours : hours;
    temp = ref;
    ref += (min < 10) ? "0" + min : min;
    ref += (sec < 10) ? "0" + sec : sec;
    var reference = $("[name='Reference']").val().toString();
    if(reference === "" || reference.indexOf(temp) > -1){
        $("[name='Reference']").val(code + ref);
    }
}

/**
 * Establece el formulario de la categoría seleccionada
 * @param {Object} obj Referencia al control de selección de categoría
 * @param {Function} callback Función de callback
 * @returns {Void}
 */
function setFormByOptionSelected(obj, callback){
    var option = $(obj).find("option:selected");
    if($(option).length === 1){
        var code = $(option).data("code");
        getReference(code);
        var xml = $(option).data("xml");
        if(xml !== "" && xml !== undefined){
            setForm(xml, $("#frmXml"));
        }
    }

    if($.isFunction(callback)){
        callback();
    }
}

/**
 * Configuración del formulario de atributos
 * @param {String} xml Xml de la definición de la categoría
 * @param {Object} target Contenedor objetivo del formulario
 * @returns {Void}
 */
function setForm(xml, target){
    // Vaciar el contenedor objetivo
    $(target).html("");
    // Parsear el xml a objetos javascript
    var xmlData = $.parseXML(xml);
    // Buscar los controles
    var controls = $(xmlData).find( "control" );
    // Número de controles en cada celda
    var nControls = Math.floor($(controls).length / 2);
    var celda = $("<div />").addClass("col-xs-12 col-sm-6 col-md-6 col-lg-6");
    $(target).append(celda);
    // Crear los controles
    $.each($(controls), function(index, item){
        if(index === nControls){
            celda =
                $("<div />").addClass("col-xs-12 col-sm-6 col-md-6 col-lg-6");
            $(target).append(celda);
        }
        setControl(getObjectFromXml(item), celda);
    });
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
        ,private:($(xmlNode).attr( "private" ) === "true") ? true : false
    };
}

/*
 * <control name="attr1" label="Silhueta" type="text" min="" max=""
 *  maxlength="25" regex="" required="required" title="" help="" error=""
 *  placeholder="Linha-A" list=""></control>
 */

/**
 * Instancia el control html descrito en el objeto pasado por referencia y lo
 * agrega al contenedor objetivo
 * @param {Object} control Referencia al objeto control (xml)
 * @param {Object} target Referencia al controlador principal
 * @param {Function} callback función de retorno
 * @returns {Void}
 */
function setControl(control, target, callback){
    var container = $("<div />")
        .addClass("form-group")
        .attr("data-error", control.error);
    var label = $("<label />").text(control.label);
    var help = $("<p />").addClass( "help-block" ).text(control.help);
    var ctrl = $("<input />");

    if(control.type === "select"){
        ctrl = $("<select />");
        if(control.list !== "" ){
            var items = control.list.toString().split(",");
            $.each($(items),function(index,item){
                var value = item.toString().toLowerCase();
                $(ctrl).append($("<option />")
                        .attr("value", value)
                        .text(item));
            });
        }
    }
    elseif(control.type === "text"
            &&(  control.maxlength === ""
            || parseInt(control.maxlength) > 50)){
        ctrl = $("<textarea />")
                .attr("rows", 3)
                .attr("maxlength", control.maxlength);
    }
    else{
        $(ctrl).attr("type", control.type)
                .attr("maxlength", control.maxlength)
                .attr("max", control.max)
                .attr("min", control.min)
                .attr("regex", control.regex);
    }

    $(ctrl).attr("name", control.name)
            .attr("title", control.title)
            .attr("placeholder",control.placeholder);

    if(control.required === true){
        $(ctrl).attr("required", "required");
    }

    if(control.type === "checkbox" || control.type === " radio"){
        var div = $("<div />")
                .addClass("checkbox")
                .append($(label).append($(ctrl)));
        $(container).append(div).append(help);
    }
    else{
        $(ctrl).addClass("form-control");
        $(container).append(label).append(ctrl).append(help);
    }

    $(target).append(container);

    if($.isFunction(callback)){
        callback(target);
    }
}
