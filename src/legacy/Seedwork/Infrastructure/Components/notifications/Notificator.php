<?php

declare(strict_types=1);

/**
 * Generador de notificaciones vía email
 * $data : [ To : "...", From : "..." , Subject : "..." , Info : object(...) ]
 * Formato de los parámetros en la plantilla: {paramName}
 *
 * @author alfonso
 */
class Notificator {

    /**
     * Content type de la notificación
     * @var string
     */
    public $ContentType = "Content-type: text/html; charset=UTF-8\r\n ";

    /**
     * Cabecera
     * @var string
     */
    public $Header = "From: {FROM}\r\n ";

    /**
     * Enviar notificación
     * @param array $data Array de información para la notificación
     * @param string $templateName Nombre de la plantilla a utilizar
     */
    public function Send( $data = null, $templateName = ""){
        if( $data == null || $templateName == "" ){
            return;
        }
        // Obtener el contenido del template
        $sContent = $this->GetTemplate( $templateName );
        // Validar datos
        $this->Validate( $data, $sContent );
        // Obtener info
        $object = (isset($data)) ? $data["Info"] : array();
        // Procesar el contenido de la notificación
        $content = $this->GetContent( $object, $content );
        // Enviar la notificación
        $this->SendMail($data, $content);
    }

    /**
     * Validación de todos los parámetros de la notificación
     * @var array $data Colección de parámetros
     * @var string $content Contenido de la notificación
     */
    private function Validate($data, $content){

        if(!is_array($data)){
            throw new \NotificatorException( "data - is not array" );
        }

        if (!array_key_exists( "To" , $data)){
            throw new \NotificatorException( "To - is not defined" );
        }

        if ( $data[ "To" ] == ""){
            throw new \NotificatorException( "To - is empty" );
        }

        if (!array_key_exists( "From" , $data)){
            throw new \NotificatorException( "From - is not defined" );
        }

        if ($data[ "From" ] == ""){
            throw new \NotificatorException( "From - is empty" );
        }

        if (!array_key_exists( "Subject" , $data)){
            throw new \NotificatorException( "Subject - is not defined" );
        }

        if ( $data[ "Subject" ] == "" ){
            throw new \NotificatorException( "Subject - is empty" );
        }

        if ( $content == "" ){
            throw new \NotificatorException( "Content - is empty" );
        }
    }

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @param string $templateName Nombre de la plantilla
     */
    private function GetTemplate($templateName){
        // Obtener la ruta de la plantilla
        $path = ConfigurationManager::GetKey( $templateName );
        // Obtener el contenido de la plantilla
        $result = file_exists( $path ) ? file_get_contents( $path ) : "";
        // retornar el contenido
        return $result;
    }

    /**
     * Procesado del contenido con el objeto de datos ( o array de datos)
     * @param object $object Referencia al objeto de información
     * @param string $content Contenido de la notificación
     */
    private function GetContent($object = null, $content = ""){
        // Validación de datos
        if( $object == null || $content == "" ){
            return $content;
        }
        // Convertir en array el objeto
        if(!is_array($object)){
            settype( $object, "array" );
        }
        // reemplazar todos los tags
        foreach($object as $key => $value){
            $content = str_replace( "{".$key."}", $value, $content);
        }
        return $content;
    }

    /**
     * Genera la notificación con los datos proporcionados
     * @var array $data Array con los parámetros de envío
     * [ To : "...", From : "..." , Subject : "..." , IsHtml : true|false ]
     * @var string $sContent Contenido de la notificación
     */
    private function SendMail($data, $sContent){
        // Validar los parámetros
        $this->Validate($data, $sContent);
        // Procesar contenido
        $content = $this->GetContent( $data[ "Info" ], $content);
        // Construir la cabecera del mensaje
        $headers = str_replace( "{FROM}", $data["From"],  $this->Header );
        // Realizar envío
        mail($data["To"], $data["Subject"], $content, $this->ContentType.$headers);
    }
}
