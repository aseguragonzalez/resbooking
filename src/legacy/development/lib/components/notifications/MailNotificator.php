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
