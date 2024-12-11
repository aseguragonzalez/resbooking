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
 * Implementación de la interfaz IHttpHandler
 *
 * @author alfonso
 */
class ClientCMSHttpHandler extends \HttpHandler implements \IHttpHandler{

    public function GetLanguage(){

    }

    /**
     * Establece el flag de "borrador" en función de la url origen.
     *  DRAFT Mode
     */
    protected function SetDraftModel(){
        // Obtener la url del entorno del cms
        $urlCms = ConfigurationManager::GetKey( "urlReferer" );
        // Establecer si cargar entidades en borrador o no
        $url = $_SERVER["HTTP_REFERER"];
        // Fijar el valor
        $draft = (strpos($url, $urlCms) === false) ? 0 : 1;
        // Comprobar parámetro
        $draft = (isset($_REQUEST["draft"])
                && $_REQUEST["draft"] == 1 ) ? 1 : $draft;
        // Definir el modo de ver
        define( "_DRAFT_", $draft );
    }

    /**
     * Constructor de la clase
     */
    public function __construct(){
        // Llamar al constructor padre
        parent::__construct();
        // Establece el modo borrador
        $this->SetDraftModel();
    }

    /**
     * Determina si el nombre del controlador pasado como argumento
     * se corresponde con un controlador válido (existe).
     * @param string Nombre del controlador
     * @return boolean
     */
    public function ValidateController($controller = ""){
        return true;
    }

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido y si existe alguna acción
     * en dicho controlador con el nombre pasado como argumento.
     * @param string Nombre del controlador
     * @param string Nombre de la acción
     * @return boolean
     */
    public function Validate($controller = "", $action = ""){
        return true;
    }

    /**
     * Establece el controlador y la acción por defecto si procede
     * @param string Nombre del controlador
     * @param string Nombre de la acción
     * @return type
     */
    public function SetDefault($controller = "", $action = ""){
        return array( "Controller" => $controller, "Action" => "" );
    }

    /**
     * Ejecuta la acción del controlador definidos por la petición con
     * los parámetros fijados (si hay). Retorna la información que la
     * petición devolverá como respuesta a la petición del cliente.
     * @param string Nombre del controlador
     * @param string Nombre de la acción
     * @param array Parámetros de la llamada
     * @return string
     */
    public function Run($controllerName = "" , $action = "", $params = null){
        // Instanciar Controlador
        $controller = new \ClientCMSController();
        // Procesar parámetros de la url
        return ($action == "")
                ? $controller->Section($controllerName)
                : $controller->Content($controllerName, $action);
    }

    /**
     * Almacena en el contexto la colección de rutas establecidas
     * (controlador-acción) para poder validar la acción y el controlador
     *  que se desean ejecutar
     * @param array Colección de rutas
     * @throws NotImplementedException
     */
    public function RegisterRoutes($routes = null){
        throw new \NotImplementedException( "ClientCMSHttpHandler" );
    }

}
