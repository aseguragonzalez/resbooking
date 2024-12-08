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
class HttpHandler implements \IHttpHandler{

    /**
     * Array con los datos de los controladores asociados
     * @var array $Controllers Colección de controladores registrados
     */
    private $Controllers = array();

    /**
     * Nombre del controlador a utilizar
     * @var string $Controller Nombre del controlador seleccionado
     */
    private $Controller="";

    /**
     * Nombre de la acción que se desea ejecutar
     * @var string $Action Nombre de la acción que se debe ejecutar
     */
    private $Action="";

    /**
     * Primer parámetros de la petición : /controller/action/First?asd=asd
     * @var object $First Argumento pasado por url
     */
    private $First=null;

    /**
     * Parámetros del QueryString
     * @var array $Params Parametros pasados por QueryString
     */
    private $Params = array();

    /**
     * Booleano que indica si la url contiene/o puede contener el lenguaje
     * @var boolean $GetLan Establece si se ha especificado un lenguaje
     */
    private $GetLan = FALSE;

    /**
     * Idioma seleccionado (si procede)
     * @var string $Language Idioma asociado
     */
    private $Language = "";

    /**
     * Cargar los datos de controladores del fichero de configuración
     * @var object $xml Nodo xml con la configuracion de controladores
     */
    protected function LoadControllers($xml=null){
        // Obtener la lista de Controladores
        $nodes = $xml->controllers->children();
        // Array de connectionString
        $this->Controllers = array();
        // Almacenar cada uno de los controladores
        foreach($nodes as $node){
            // Obtener cada acción del controlador
            $actions = $node->actions->children();
            // Array de acciones
            $actiones = array();
            // Agregar cada acción al array
            foreach($actions as $action){
                // Obtener los atributos del nodo
                $attr = $action->attributes();
                // Agregar la acción con sus roles asociados
                $actiones[(string)$attr->name] = array(
                    "roles" => (string)$attr->roles,
                    "params" =>  (string)$attr->params);
            }
            // Obtener los atributos del nodo
            $attributes = $node->attributes();
            // guardarlos en el array
            $this->Controllers[(string)$attributes->name] = array(
                            "actions" => $actiones,
                            "action" => (string)$attributes->action,
                            "roles" => (string)$attributes->roles
                    );
        }
    }

    /**
     * Establece los parametros pasados por url cuando se ha especificado
     * idioma
     * @param array $parts Partes de la url
     */
    private function SetUrlParts($parts = null){
        // Obtener el número de partes de la url
        $count = count($parts);
        // aplicar filtros
        switch($count){
            case 2:
                $this->Controller = $parts[1];
                $this->Action = "";
                break;

            case 3:
                $this->Controller = $parts[1];
                $this->Action = $parts[2];
                break;

            case 4:
                $this->Controller = $parts[1];
                $this->Action = $parts[2];
                $this->First = $parts[3];
                break;

            default:
                $this->Controller = "";
                $this->Action = "";
                break;
        }
    }

    /**
     * Establece los parametros pasados por url cuando se ha especificado
     * idioma
     * @param array $parts Partes de la url
     */
    private function SetUrlLanguageParts($parts = null){
        // Obtener el número de partes de la url
        $count = count($parts);
        // Establecer partes de la url
        if($count == 2){
            $this->SetLanguage($parts[1]);
            $this->Controller = "";
            $this->Action = "";
        }
        elseif($count == 3){
            $this->SetLanguage($parts[1]);
            $this->Controller = $parts[2];
            $this->Action = "";
        }
        elseif($count == 4){
            $this->SetLanguage($parts[1]);
            $this->Controller = $parts[2];
            $this->Action = $parts[3];
        }
        elseif($count == 5){
            $this->SetLanguage($parts[1]);
            $this->Controller = $parts[2];
            $this->Action = $parts[3];
            $this->First = $parts[4];
        }
    }


    /**
     * Procesa las partes de la url : /lan-lan/controller/action
     * @var array $parts Coleccion de partes de una url
     */
    protected function GetUrlParts($parts = null){
        if($this->GetLan){
            // Establece las secciones
            $this->SetUrlLanguageParts($parts);
        }
        else{
            $this->SetUrlParts($parts);
        }
    }

    /**
     * Constructor
     */
    public function __construct(){
        // Ruta de acceso al fichero de configuración
        $xmlstr = ConfigurationManager::GetKey( "controllers" );
        // Cargamos el contenido de la configuración desde el xml
        $configurator = simplexml_load_file($xmlstr);
        // Cargar los datos de configuración
        $this->LoadControllers($configurator);
        // Comprobar si está definido el parámetro multidioma
        $this->GetLan = isset($configurator->language);
        // Establecer el idioma por defecto si procede
        if($this->GetLan){
            $this->Language =
                    (string)$configurator->language->attribute["default"];
        }
    }

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido (existe).
     * @var string $controller Nombre del controlador solicitado
     * @return boolean Estado de la validacion
     */
    public function ValidateController($controller){
        return (array_key_exists ($controller , $this->Controllers ));
    }

    /**
     * Determina si el nombre del controlador pasado como argumento
     * se corresponde con un controlador válido y si existe alguna acción
     * en dicho controlador con el nombre pasado como argumento.
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la accion
     * @return boolean Estado de la validacion
     */
    public function Validate($controller, $action){
        return (array_key_exists ($controller , $this->Controllers ))
                    && (array_key_exists ($action ,
                            $this->Controllers[$controller]["actions"]));
    }

    /**
     * Establece el controlador y la acción por defecto si procede
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la accion
     * @return array Array con los parametros de la solicitud
     */
    public function SetDefault($controller, $action){
        if(array_key_exists ($controller , $this->Controllers)){
            return array("Controller" => $controller,
                "Action" => $this->Controllers[$controller]["action"]);
        }
        else{
            // Establecer el mensaje de error
            $message = "HttpHandler - SetDefault - Controller : "
                    .$controller.", Action : ".$action;
            // Lanzar excepción
            throw new UrlException( $message );
        }
    }

    /**
     * Procesa la url actual obteniendo el nombre del controlador,
     * la acción y los parámetros de la petición realizada. La información
     * obtenida se retornará mediante un array/diccionario donde se
     * especifique cada uno de las partes obtenidas. En el caso de que
     * la url no esté correctamente formada se lanzará una excepción.
     * @var string $urlRequest Url de la solicitud
     * @return array Array con los parametros para procesar la solicitud
     */
    public function ProcessUrl($urlRequest){
        // Extraer parámetros de la url ( si existen )
        $urlParts = explode("?", $urlRequest);
        // Establecer las partes de la url
        $urlPartsCount = count($urlParts);
        // Comprobar si había parámetros
        if($urlPartsCount == 1){
            // Extraemos la parte sin parámetros de la petición
            $url = $urlParts[0];
            // Fraccionamos la url para obtener controlador, acción e idioma(si procede)
            $parts = explode("/", $url);
            // Asignar cada parte de la url
            $this->GetUrlParts($parts);
        }
        else if($urlPartsCount > 1){
            // Extraemos la parte sin parámetros de la petición
            $url = $urlParts[0];
            // Fraccionamos la url para obtener controlador,
            // acción e idioma(si procede)
            $parts = explode("/", $url);
            // Asignar cada parte de la url
            $this->GetUrlParts($parts);
            // Asignar los parñametros dle querystring
            $this->Params = $urlParts[1];
        }
        else{
            throw new \UrlException("HttpHandler - ProcessUrl - "
                    . $urlRequest);
        }
        // Establecer los parámetros obtenidos
        $this->ProcessParameters($this->Params);
        // Establecer el resultado del análisis
        $result = array(
            "Language" => $this->Language,
            "Controller" => $this->Controller,
            "Action" => $this->Action,
            "First" => $this->First,
            "Params" => $this->Params
        );
        // Retornar la estructura de datos
        return $result;
    }

    /**
     * Debe encargarse de realizar el procesado de los parámetros de la
     * petición (get / post) según las especificaciones del proyecto.
     * Por lo general, se encargará de buscar estructuras de posibles
     * ataques de inyección.
     * @var array $parameters Array de parametros para la solicitud
     */
    public function ProcessParameters($parameters){
        // Estabelcer el array a devolver
        $params = array();
        // Establecer el parámetro pasado como parte de la acción
        if(isset($this->First)){
            $params["id"] = $this->First;
        }
        // Validación del argumento
        if(is_string($parameters)) {
            // Fraccionar la lista de parámetros
            $parameters = explode("&", $parameters);
            // Recorrer los parámetros
            //foreach($parameters as $key => $value){
            foreach($parameters as $value){
                $parts = explode("=", $value);
                if(count($parts) == 2){
                    $params[$parts[0]] = $parts[1];
                }
            }
        }
        // Almacenar los parámetros obtenidos
        $this->Params = $params;
    }

    /**
     * Se encarga de configurar el idioma de la petición/ ejecución en
     * los contextos donde es necesario( aplicaciones multilenguaje).
     * @var string $language Idioma seleccionado
     */
    public function SetLanguage($language){
        // Establecer el lenguaje como parte del handler
        $this->Language = $language;
        // Almacenar el valor de lenguaje en la sesión
        $_SESSION["language"] = $language;
    }

    /**
     * Obtiene el lenguaje del contexto actual si es que está
     * definido o una cadena vacía.
     */
    public function GetLanguage(){
        return (isset($_SESSION["language"]))
            ? $_SESSION["language"] : "";
    }

    /**
     * Ejecuta la acción del controlador definidos por la petición con
     * los parámetros fijados (si hay). Retorna la información que la
     * petición devolverá como respuesta a la petición del cliente.
     * @var string $sController Nombre del controlador
     * @var string $action Nombre de la accion
     * @var array $params Coleccion de parametros de la llamada
     */
    public function Run($sController, $action, $params = null){
        $name = $sController."Controller";
        require_once("controller/$name.php");
        $controller = new $name();
        return call_user_func_array(array($controller, $action), $params);
    }

    /**
     * Almacena en el contexto la colección de rutas establecidas
     * (controlador-acción) para poder validar la acción y el controlador
     * que se desean ejecutar.
     */
    public function RegisterRoutes($routes){
        throw new \NotImplementedException( "HttpHandler" );
    }

}
