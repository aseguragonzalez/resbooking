<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Generador de notificaciones vía email
 * $data : [ To : "...", From : "..." , Subject : "..." , Info : object(...) ]
 * Formato de los parámetros en la plantilla: {paramName}
 *
 * @author alfonso
 */
class NotificatorDB {

    /**
     * Cabecera tipo de contenido de la notificación
     * @var string
     */
    public $ContentType = "Content-type: text/html; charset=UTF-8\r\n ";

    /**
     * Cabecera dirigido "a"
     * @var string
     */
    public $Header = "From: {FROM}\r\n ";

    /**
     * Enviar notificación
     * @param array $data Parámetros de envío
     * @param string $templateName nombre de la plantilla
     * @return void
     */
    public function Send( $data = null, $templateName = ""){
        // Validación de datos
        if( $data == null || $templateName == "" ){
            return;
        }
        // Obtener el contenido del template
        $sContent = $this->GetTemplate( $templateName );
        // Obtener info
        $object = (isset($data)) ? $data["Info"] : array();
        // Procesar el contenido de la notificación
        $content = $this->GetContent( $object, $sContent );
        // Registrar el nombre de la plantilla para la notificación
        $data["template"] = $templateName;
        // Enviar la notificación
        $this->SendMail($data, $content);
    }

    /**
     * Validación de todos los parámetros de la notificación
     * @param array $data Parámetros de envío
     * @param string $content Contenido de la plantilla
     * @throws NotificatorDBException
     */
    private function Validate( $data, $content){
        if(!is_array($data)){
            throw new \NotificatorDBException( "data - is not array" );
        }

        if (!array_key_exists( "To" , $data)){
            throw new \NotificatorDBException( "To - is not defined" );
        }

        if ( $data[ "To" ] == ""){
            throw new \NotificatorDBException( "To - is empty" );
        }

        if (!array_key_exists( "From" , $data)){
            throw new \NotificatorDBException( "From - is not defined" );
        }

        if ($data[ "From" ] == ""){
            throw new \NotificatorDBException( "From - is empty" );
        }

        if (!array_key_exists( "Subject" , $data)){
            throw new \NotificatorDBException( "Subject - is not defined" );
        }

        if ( $data[ "Subject" ] == "" ){
            throw new \NotificatorDBException( "Subject - is empty" );
        }

        if ( $content == "" ){
            throw new \NotificatorDBException( "Content - is empty" );
        }
    }

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @param string Nombre de la plantilla
     * @return string Plantilla
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
     * @param object $object Objeto a procesar
     * @param string $content Contenido de la notificación
     * @return string Contenido procesado
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
     * @param array $data Parámetros de la notificación. $data :
     * [ To : "...", From : "..." , Subject : "..." , IsHtml : true|false ]
     * @param string $sContent Contenido de la notificación
     */
    private function SendMail($data, $sContent){
        // Procesar contenido
        $content = $this->GetContent( $data[ "Info" ], $sContent);
        // Validar los parámetros
        $this->Validate($data, $content);
        // Construir la cabecera del mensaje
        $headers = str_replace( "{FROM}", $data["From"],  $this->Header );
        // Instanciar dto de la notificación
        $dto = new Notification();
        $dto->Project = $data["project"];
        $dto->Service = $data["service"];
        $dto->To = $data["To"];
        $dto->From = $data["From"];
        $dto->Subject = $data["Subject"];
        $dto->Header = $this->ContentType.$headers;
        $dto->Content = $content;
        $dto->Template = $data["template"];
        $date = new DateTime( "NOW" );
        $dto->Date = $date->format( "y-m-d" );
        // Obtener referencia al DAO
        $dao = Injector::GetInstance();
        // Crear registro
        $dao->Create( $dto );
    }
}
