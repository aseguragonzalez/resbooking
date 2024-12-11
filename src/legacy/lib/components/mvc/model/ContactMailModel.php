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
 * Model para la gestión email de contacto
 *
 * @author alfonso
 */
class ContactMailModel extends \SaasModel{

    /**
     * Constructor
     */
    public function __construct(){
        // Llamar al constructor padre
        parent::__construct();
    }

    /**
     * Método que guarda la información relativa a la entidad
     * Para el envío es necesario que exista la clave en webconfig
     * para obtener la plantilla a utilizar : <add key="mailContacto"
     * value="notifications/nombreplantilla.html" />
     * @param array $data Array de datos
     */
    public function Send($data = null){
        // Gestor de dependencias
        $injector = Injector::GetInstance();
        // Referencia al notificador
        $notificator = $injector->Resolve( "INotificator" );
        // Obtener template
        $template = $notificator->GetTemplate( "mailContacto" );
        // Procesado de la notificación
        foreach($data as $key => $value){
            $template = str_replace( "{".$key."}", $value, $template );
        }
        // Setear parámetros
        $toFrom = ConfigurationManager::GetKey( "mail-from" );
        // Establecer título
        $Subject = "Solicitud de Contacto";
        // Configuración de la notificación
        $config = array( "To" => $toFrom,
                "From" => $toFrom ,
                "Subject" => $Subject ,
                "IsHtml" => true
            );
        // Generar notificación
        $notificator->Send($config, $template);
    }

}
