<?php

define("_ERROR_MESSAGE_FROM_", "inet@try-catch.es");
define("_ERROR_MESSAGE_TO_", "inet@try-catch.es");

/*
    set_handlers(E_ALL, "rb_application_error_handler",
            "rb_application_exception_handler");
*/

/**
 * Visualización del mensaje de error por pantalla
 */
function rb_print_error_screen(){
    $html =
    '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error Interno</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"
    rel="stylesheet" media="screen">
    <!--[if lt IE 9]><script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body><div class="container"><div class="page-header">
    <h1>OUupss! <small> esto es algo embarazoso..</small></h1></div>
    <p>
        Se ha producido un error interno. Por favor reinténtelo pasados unos minutos.
        Disculpe las molestias.
    </p></div><script src="http://code.jquery.com/jquery.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    </body></html>';

    echo $html;
}

/**
* Manejador de errores de aplicación
* @param number $err_severity The level or error
* @param string $err_msg The message of the error
* @param string $err_file The file where the error has occured
* @param number $err_line The line where the error has occured
* @param array $err_context Detailed error description
*/
function rb_application_error_handler($err_severity, $err_msg,
        $err_file, $err_line, array $err_context) {
    $data = ["Level" => $err_severity, "File" => $err_file,
        "Line" => $err_line, "Message" => $err_msg,
        "Context" => $err_context, "Request" => $_REQUEST,
        "Session" => $_SESSION ];
    // Proceso de log del error
    rb_log_error($data);
    // Envío de la notificación con la información del error
    rb_send_error_mail($data);

                            rb_print_error_screen();

                            exit();
    // Lanzar excepción
    //throw new \Exception("File: $err_file; Line: $err_line; Message: $err_msg", $err_severity);
}

/**
 * Generar el mensaje de error
 * @param array $data Información del error a loguear
 * @return string Mensaje de error
 */
function rb_get_error_message(array $data = NULL){
    try{
        if($data != NULL){
            //$str = var_export($data, TRUE)."\n";
            $str = json_encode($data);
        }
        else{
            $str = "rb_get_error_message: La variable data es NULL\n";
        }
    }
    catch(Exception $e){
        $str = "rb_get_error_message: Excepción al obtener "
                . "el mensaje del error."
                . "Detalles: ".$e->getMessage()."\n";
    }
    return $str;
}

/**
 * Proceso de registro en log del error
 * @param array $data Colección de información a loguear
 */
function rb_log_error(array $data = NULL){
    $date = new DateTime("NOW");
    $file = "logs/".$date->format("Ymd")."-rb_log_error.log";
    $str = rb_get_error_message($data);
    file_put_contents($file, $str, FILE_APPEND);
}

/**
 * Proceso de envío de mensajes para errores capturados
 * @param array $data Colección de información a enviar
 */
function rb_send_error_mail(array $data = NULL){
    $message = rb_get_error_message($data);
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= "From: "._ERROR_MESSAGE_FROM_. "\r\n";
    $subject = "Error en la plataforma";
    mail(_ERROR_MESSAGE_TO_, $subject, $message, $headers);
}

/**
 * Manejador de excepciones de aplicación
 * @param \Exception $e Excepción lanzada
 */
function rb_application_exception_handler($e = NULL){
    try{
        // Generar traza de error
        rb_log_exception($e);
        // Generar notificación
        rb_send_exception_mail($e);
    }
    catch(Exception $ex){
        // Generar traza de error
        rb_log_exception($ex);
        // Generar notificación
        rb_send_exception_mail($ex);
    }
    // Visualizar pantalla de error
    rb_print_error_screen();
    // Finalizar la ejecución
    exit();
}

/**
 * Generar el mensaje de excepción
 * @param Exception $e Referencia a la excepción capturada
 * @return string Mensaje de excepción
 */
function rb_get_exception_message($e = NULL){
    try{
        if($e != NULL){
            $str = var_export($e, TRUE)."\n";
            $str .= "\nSolicitud: ".var_export($_REQUEST, TRUE)."\n";
            $str .= "\nSession: ".var_export($_SESSION, TRUE)."\n";
            //$str = json_encode($e);
        }
        else{
            $str = "rb_get_exception_message: La variable e es NULL\n";
        }
    }
    catch(Exception $ex){
        $str = "rb_get_exception_message: Excepción al generar el "
                . "mensaje de la excepción."
                . " Detalles: ".$ex->getMessage()."\n";
                                            $str .= "\nExcepción Original:".$e->getMessage()."\n";
                                            $str .= "\nSolicitud:".json_encode($_REQUEST)."\n";
                                            $str .= "\nSession:".json_encode($_SESSION)."\n";

    }
    return $str;
}

/**
 * Proceso para el log de excepciones capturadas
 * @param \Exception $e Referencia a la excepción capturada
 */
function rb_log_exception($e = NULL){
    $date = new DateTime("NOW");
    $file = "logs/".$date->format("Ymd")."-rb_log_exception.log";
    $str = rb_get_exception_message($e);
    file_put_contents($file, $str, FILE_APPEND);
}

/**
 * Proceso de envío de mensajes para excepciones capturados
 * @param \Exception $e Excepción capturada
 */
function rb_send_exception_mail($e = NULL){
    $message =  rb_get_exception_message($e);
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= "From: "._ERROR_MESSAGE_FROM_. "\r\n";
    $subject = "Excepción en la plataforma";
    mail(_ERROR_MESSAGE_TO_, $subject, $message, $headers);
}
