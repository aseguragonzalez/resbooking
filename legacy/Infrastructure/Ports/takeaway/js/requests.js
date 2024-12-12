

function setTable(){

    var language = {
       "emptyTable":     "No hay pedidos",
       "info":           "Cargados del _START_ al _END_ de _TOTAL_ pedidos",
       "infoEmpty":      "0 de 0 de un total de 0 pedidos",
       "infoFiltered":   "(filtrados de un total de _MAX_ pedidos)",
       "infoPostFix":    "",
       "thousands":      ",",
       "lengthMenu":     "Ver   _MENU_ pedidos por página",
       "loadingRecords": "Cargando...",
       "processing":     "Procesando...",
       "search":         "Buscar:  ",
       "zeroRecords":    "La búsqueda no tiene resultados",
       "paginate": {
           "first":      "Primero",
           "last":       "Último",
           "next":       ">>",
           "previous":   "<<"
       },
       "aria": {
           "sortAscending":  ": activate to sort column ascending",
           "sortDescending": ": activate to sort column descending"
       }
   };
}

function updateRequestState(obj){
   if($(obj).val() !== "-1"){
       $.post($(obj).data("url"),
           { Id:$(obj).data("id"), State: $(obj).val()},
           function(data){
               if(data.Code === 200 && data.Result === true){
                   $("#result").parent().clone()
                           .appendTo("#resultados")
                           .addClass("alert-success")
                           .addClass("has-success")
                           .removeClass("hide")
                           .find("p").text(data.Message);
               }
               else{
                   $("#result").parent().clone()
                           .appendTo("#resultados")
                           .addClass("alert-danger")
                           .removeClass("hide").text(data.Message);
               }
               ocultarResultados();
       });
   }
}

$(function() {
   setTable();
});
