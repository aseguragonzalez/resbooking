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

/*
    Dependencias :
    - Clase base HttpModule y todas sus dependencias
    - Interfaz IHttpModule
    - Componentes definidos : [ IDataAccessObject ]
    - Claves de config.xml : [ connectionString ]
    - Entidad de base de datos : Service
*/

/**
 * Implementación de la interfaz IHttpModule para aplicaciones Saas
 *
 * @author alfonso
 */
class SaasHttpModule extends \HttpModule implements \IHttpModule{

    /**
     * Obtiene el nombre del servicio actual a partir de la ruta
     * actual de ejecución
     */
    protected function GetServiceName(){
        // Obtener ruta actual
        $path = getcwd();
        // Buscamos la posición del último directorio
        $pos = strrpos ( $path , "/" );
        // Si no encontramos el caracter error
        if( $pos === false ){
            throw new UrlException( "GetServiceName - ".$path );
        }
        // Extraemos el último directorio
        $path = substr( $path, $pos);
        // Eliminar el caracter /
        $name = str_replace( "/" , "" , $path);

        return $name;
    }

    /**
     * Configurar los datos del servicio actual a partir del nombre
     * @param string Nombre del servicio
     * @throws UrlException
     */
    protected function SetServiceData( $name = "" ){
        // Buscar datos del servicio
        $services = $this->Dao->GetByFilter( "Service",
                array ( "Name" => $name ));
        // Comprobar si hay resultados
        if(count($services) == 0){
            throw new UrlException( "BeginRequest - ".$name );
        }
        // Almacenar en sesión los datos del primer servicio obtenido
        $_SESSION["serviceId"] = $services[0]->Id;
        $_SESSION["serviceName"] = $services[0]->Name;
    }

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar el constructor padre
        parent::__construct();
        // Cargar el objeto de acceso a datos
        $this->Dao = $this->Injector->Resolve( "IDataAccessObject" );
        // Obtener la clave de cadena de conexión
        $connectionString =
                ConfigurationManager::GetKey( "connectionString" );
        // Obtener los parámetros de conexión a bbdd
        $oConnString =
                ConfigurationManager::GetConnectionStr($connectionString);
        // Configurar Objeto de acceso a datos
        $this->Dao->Configure($oConnString);
    }

    /**
     * Se encarga de realizar las tareas comunes a cualquier petición de
     * cliente como generar una traza, comprobar si existe sesión...
     */
    public function BeginRequest(){
        // Obtener el nombre del servicio actual
        $name = $this->GetServiceName();
        // Setear los datos del servicio activo
        $this->SetServiceData( $name );
        // Cargar dependencias de seguridad
        $this->Security = $this->Injector->Resolve( "ISecurity" );
    }

    /**
     * Proceso de autenticación del usuario
     * @throws UnAuthenticateException
     */
    protected function Authentication(){
        if($this->Security->GetUserName() != ""){
            return;
        }
        // Comprobación si se utiliza un ticket de autenticación
        $ticket = filter_input(INPUT_GET, "ticket");
        // Proceso de validación del ticket
        if($ticket != FALSE && $ticket != NULL){
            if(!$this->Security->AuthenticateTicket($ticket)){
                // Establecer el mensaje de error
                $message = get_class()." - Authentication - ticket no validado";
                // Lanzar excepción
                throw new UnAuthenticateException( $message );
            }
        }
        parent::Authentication();
    }
}
