<?php

require_once "libs/mycurl.php";
$open = 1;
$url = "http://www.eltenedor.es/busqueda?searchText=Gran+V%C3%ADa%2C+Madrid%2C+"
        . "Espa%C3%B1a&coordinate=40.42101479999999%2C-3.707340199999976"
        . "&altDate=Fecha&date=noDate&time=&pax=&idRestaurant=&locality="
        . "&titleSubstitute=&localeCode=ES&idGooglePlace=52aa64ef6d1d6bf254e3dbb0ed9d3cc9a97cbd1a"
        . "&sb=1&is_restaurant_autocomplete=0#searchText=Gran\20%20V\ED%20a\2C%20\20%20Madrid\2C%20\20%20Espa\F1%20a"
        . "&coordinate=40.42101479999999,-3.707340199999976&date=noDate&time=&pax="
        . "&titleSubstitute=&idGooglePlace=&sort=QUALITY_DESC&filters%5BRADIUS%5D%5Bmax%5D=2"
        . "&filters%5BPROMOTION%5D%5B50_PERCENT%5D=on"
        . "&filters%5BPRICE%5D%5Bmin%5D=0&filters%5BPRICE%5D%5Bmax%5D=150&filters"
        . "%5BRATE%5D%5Bmin%5D=0";

$webpage = "";
if(isset($_GET["url"]) && !empty($_GET["url"])){
    /*
    $request = $_SERVER["REQUEST_URI"];
    $start = strpos($request, "?url=");
    $url = substr($request, $start + 5);
    */
    $url = $_GET["url"];
    $open = 0;
}

$mycurl = new mycurl($url);
$mycurl->createCurl();
$webpage = $mycurl->_webpage;
$ini = strpos($webpage, '<div id="results"');
$fin = strpos($webpage, '<div id="search_filter"');
$webpage = substr($webpage, $ini, $fin-$ini);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Restaurantes</title>
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <script type="text/javascript">

            function openPage(o){
                var href = $(o).find("a").attr("href");
                if(href === "" || href === undefined || href === "#"){
                    return;
                }
                var url = "http://localhost/resbooking/projects/crm/index.php?"
                 + "url=http://www.eltenedor.es" + href;
                window.open(url, "_blank");
            }

            function getData(o, callback){
                try{
                    if(o !== undefined ){
                        var rest = {
                            nombre: $(o).find(".resultItem-name").find("a").text().trim()
                            ,direccion:$(o).find(".resultItem-address").text().trim()
                            ,pp:$(o).find(".resultItem-price").text().replace("€", "").trim()
                            ,nota:$(o).find(".resultItem-rating").find("a").text().trim()
                            ,descuento:$(o).find(".l-f").text().trim().substr(0,5).trim()
                            ,tipo:$(o).find(".resultItem-speciality").text().trim()
                            ,enlace:$(o).find(".resultItem-name").find("a").attr("href").trim()
                        };
                        if($.isFunction(callback)){
                            callback(rest);
                        }
                    }
                }
                catch(err){

                }
            }

            function saveData(rest){

            }

            $(function(){
                $(".resultItem-thumbnail").remove();
                $.each($(".resultItem"), function(i,o){
                    getData(o,function(rest){
                        saveData(rest);
                    });
                });

                if($("#open").val() === "1"){

                $.each($(".pagination").find("li"),function(i,o){
                    openPage(o);
                });
            }
            });
        </script>

    </head>
    <body>
        <input type="hidden" id="open" value="<?php echo $open ?>" />
        <?php echo $webpage; ?>
    </body>
</html>
