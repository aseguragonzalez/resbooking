<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz de notificaciones
 *
 * @author alfonso
 */
class MailNotificator implements \INotificator{

    /**
     * Genera la notificación con los datos proporcionados
     * @var array $data Datos para la notificación
     * [ To : "...", From : "..." , Subject : "..." , IsHtml : true|false ]
     * @var string $content Contenido de la notificación
     */
    public function Send($data, $content){

        if(!is_array($data)){
            throw new \MailNotificatorException( "data - is not array" );
        }

        if (!array_key_exists( "To" , $data)){
            throw new \MailNotificatorException( "To - is not defined" );
        }

        if (!array_key_exists( "From" , $data)){
            throw new \MailNotificatorException( "From - is not defined" );
        }

        if (!array_key_exists( "Subject" , $data)){
            throw new \MailNotificatorException( "Subject - is not defined" );
        }

        if ( $content == "" ){
            throw new \MailNotificatorException( "Content - is empty" );
        }

        $contentType = "Content-type: text/html; charset=UTF-8\r\n";
        // Construir la cabecera del mensaje
        $headers = str_replace( "{FROM}", $data["From"], "From: {FROM}\r\n " );
        // Realizar envío
        mail($data["To"], $data["Subject"], $content, $contentType.$headers);
    }

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @var string $templateName nombre de la plantilla
     */
    public function GetTemplate($templateName){
        // Obtener la ruta de la plantilla
        $path = ConfigurationManager::GetKey( $templateName );
        // Obtener el contenido de la plantilla
        $result = file_exists( $path ) ? file_get_contents( $path ) : "";
        // retornar el contenido
        return $result;
    }
}
