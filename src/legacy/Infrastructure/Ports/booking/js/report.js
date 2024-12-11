$(function(){
    // Configurar diagrama de barras reservas año en curso
    setDatos(function(resultados, step, max){
            setBarChart(resultados, step, max);
    });

    setPieClientes( "#clientes" );

    setDonutOcupacion( "#ocupacion" );
});

function setDatos(callback){

    var resultados=[];
    var max = 0;
    var step = 0;
    var barras = 0;

    $.each($(".rowdata"), function(index, item){

        if(barras < 8){
            var cursadas = $(item).data( "cursadas" );
            var perdidas = $(item).data( "perdidas" );
            if(cursadas >= perdidas){
                max = (cursadas > max) ? cursadas : max;
            }
            else{
                max = (perdidas > max) ? perdidas : max;
            }
            var reg = {
                    "Cursadas" : cursadas,
                    "Perdidas": perdidas,
                    "Mes":  $(item).data( "month" )
            };
            resultados.push(reg);
        }
        barras++;
    });

    resultados = resultados.reverse();

    var sigue = false;

    do{
        sigue = (max % 10) !== 0;
        if(sigue) {
            max++;
        }
    }while(sigue);

    step = Math.ceil(max / 10);

    if( $.isFunction(callback)){
        callback(resultados, step, max);
    }
}

function setDonutOcupacion(selector){

	var days = $(selector).data( "p" );

	var ocupacion = [
            { porcentaje: days[1], dia: "Lunes",  color:"#ee3639" },
            { porcentaje: days[2], dia: "Martes",  color:"#ee9e36" },
            { porcentaje: days[3], dia: "Miercoles",  color:"#eeea36" },
            { porcentaje: days[4], dia: "Jueves",  color:"#a9ee36" },
            { porcentaje: days[5], dia: "Viernes",  color:"#36d3ee" },
            { porcentaje: days[6], dia: "Sabado",  color:"#367fee" },
            { porcentaje: days[7], dia: "Domingo",  color:"#9b36ee" }
	];

	var pieChart = new dhtmlXChart({
        view: "donut",
        container: "ocupacion",
        value: "#porcentaje#",
        color: "#color#",
        tooltip: "<b>#porcentaje# % </b>",
        legend: {
            width: 75,
            align: "right",
            valign: "middle",
            template: "#dia#",
		  title: "Ocupación"
        },
        gradient: 1,
        shadow: false,
		  title: "Ocupación"
    });

    pieChart.parse(ocupacion, "json");
}

function setPieClientes(selector){

    var	 clientes = [
        { porcentaje:$(selector).data( "r" ), tipo:"Repite", color: "#ee9e36" },
        { porcentaje:$(selector).data( "a" ), tipo:"Asiduo", color: "#eeea36" },
        { porcentaje:$(selector).data( "n" ), tipo:"No repite", color: "#ee3639" }
    ];

    var chart = new dhtmlXChart({
        view: "pie3D",
        container: "clientes",
        value: "#porcentaje#",
        color: "#color#",
        label: "#tipo#",
        tooltip: "#porcentaje# %"
    });

    chart.parse(clientes, "json");
}

/**
 * Establecer diagrama en barras
 * @param {type} resultados
 * @param {type} step
 * @param {type} max
 * @returns {undefined}
 */
function setBarChart(resultados, step, max){

    if( resultados === undefined ) {
        return;
    }

    //resultados = resultados.reverse();

    var barChart1 = new dhtmlXChart({
        view: "bar",
        container: "reservas",
        value: "#Cursadas#",
        color: "#58dccd",
        radius: 0,
        gradient: "rising",
        tooltip: { template: "#Cursadas#" },
        width: 40,
        xAxis: { template: "#Mes#" },
        yAxis: {
            start: 0,
            step: step,
            end: max,
            title: "Reservas cursadas"
        },
    });

    barChart1.parse(resultados, "json");
}
