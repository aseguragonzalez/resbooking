<?php

$dominio = ""; ///resbooking/services/anulaciones/";

$current = getcwd();

if(strpos($current, "/pro/") > -1){
    $entorno = "http://des-admin.resbooking.es";
}
elseif(strpos($current, "/pre/") > -1){
    $entorno = "http://des-admin.resbooking.es";
}
else{
    $entorno = "http://des-admin.resbooking.es";
}

// $url = str_replace($dominio, $entorno, $_SERVER["REQUEST_URI"]);

$url = $entorno.$_SERVER["REQUEST_URI"];

if($url == $entorno){
    exit();
}

require_once "mycurl.php";

$mycurl = new mycurl($url);

if(strpos($url, "/Booking/SetCancel") > -1){
    $mycurl->setPost($_POST);
}

$mycurl->createCurl();

echo $mycurl->_webpage;

exit();
