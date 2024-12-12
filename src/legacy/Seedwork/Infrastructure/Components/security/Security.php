<?php

declare(strict_types=1);

/**
 * Implementación de la interfáz de seguridad basado en un repositorio
 *
 * @author alfonso
 */
class Security implements \ISecurity{

    /**
     * Array con los datos de los controladores asociados
     * @var array $Controller Array de controladores
     */
    protected $Controllers = null;

    /**
     * Obtiene la información de un nodo action en un array
     * @param object $action Referencia al XmlNode con la info
     * @return mixed Array con la información de la acción o false
     */
    private function ReadChildren($action = null){
        if($action != null){
            $attr = $action->attributes();

            $visible = (isset($attr->visible)
                && (strtolower((string)$attr->visible) == "true" ));

            return [
                "name" => (string)$attr->name,
                "action"=> (string)$attr->action,
                "roles" => (string)$attr->roles,
                "param" => (string)$attr->param,
                "controller" => (string)$attr->controller,
                "title" =>  (string)$attr->title,
                "text" =>  (string)$attr->text,
                "visible" =>  $visible
                    ];
        }
        return false;
    }

    /**
     * Obtiene el array de acciones de segundo nivel
     * @param array $actions Array de acciones
     * @return array Array de acciones actualizado
     */
    private function GetChildrens($actions = null){
        $acciones = [];
        if($actions != null && is_object($actions)){
            foreach($actions as $action){
                $item = $this->ReadChildren($action);
                if($item != false && $item["visible"] === true){
                    $acciones[$item["name"]] = $item;
                }
            }
        }
        return $acciones;
    }

    /**
     * Obtiene el array de acciones a partir de la colección de nodos XML
     * @param object $actions Referencia a los nodos con las acciones
     * @return array Colección de arrays con la infor de las acciones
     */
    private function GetActions($actions = null){
        $acciones = [];
        if($actions != null && is_object($actions)){
            foreach($actions as $action){
                $attr = $action->attributes();
                $acciones[(string)$attr->name] = (string)$attr->roles;
            }
        }
        return $acciones;
    }

    /**
     * Procesar la información de cada controlador configurado
     * @param type $node
     */
    private function ProcessControllerEntry($node = null){
        $actions = $node->actions->children();
        $acciones = $this->GetActions($actions);
        $childrens = $this->GetChildrens($actions);
        $attributes = $node->attributes();
        $name = (string)$attributes->name;
        $visible = (isset($attributes->visible)
                && (strtolower((string)$attributes->visible) == "true" ));
        $this->Controllers[$name] =
                [
                    "name" => $name,
                    "actions" => $acciones,
                    "childrens" => $childrens,
                    "action" => (string)$attributes->action,
                    "roles" => (string)$attributes->roles,
                    "title" => (string)$attributes->title,
                    "text" => (string)$attributes->text,
                    "visible" => $visible
                ];
    }

    /**
     * Cargar los datos de controladores del fichero de configuración
     * @var object $xml Nodo xml de configuración
     */
    protected function LoadControllers($xml){
        // Obtener la lista de Controladores
        $nodes = $xml->controllers->children();
        // Array de connectionString
        $this->Controllers = [];
        // Almacenar cada uno de los controladores
        foreach($nodes as $node){
            $this->ProcessControllerEntry($node);
        }
    }

    /**
     * Se encarga de realizar el proceso de autenticación del usuario
     * que accede a la aplicación mediante un ticket de acceso. En caso
     * de ser validado el ticket, se debe establecer el usuario como
     * autenticado en el contexto. Devuelve el resultado de la
     * autenticación como un valor booleano.
     * @var string $ticket ticket de autenticación de usuario
     * @return boolean Estado de la validación
     */
    public function AuthenticateTicket($ticket){
        throw new \NotImplementedException( "AuthenticateTicket" );
    }

    /**
     * Valida los datos de usuario contra el repositorio actual
     * (xml, bbdd, webservice..) y establece en la sesión los datos
     * de usuario [ $_SESSION["user"], $_SESSION["userid"] ]
     * @var string $username Nombre de usuario
     * @var string $password Contraseña de usuario
     * @return boolean Estado de la validación
     */
    protected function ValidateUser($username, $password){
        throw new \NotImplementedException( "ValidateUser" );
    }

    /**
     * Establece los parámetros de contexto cuando no se verifica
     * el proceso de autenticación
     */
    protected function SetAuthenticateFail(){
        $count = (isset($_SESSION["auth_count"]))
                ? intval($_SESSION["auth_count"]) : 0;
        $count++;
        $_SESSION["auth_count"] = $count;
    }

    /**
     * Comprobación si el usuario ya está autenticado
     * @return boolean Estado de la autenticación
     */
    protected function IsAuthenticate(){
        // Comprobamos si ya hay una sessión activa
        return (isset($_SESSION["user"]) && ($_SESSION["user"] != ""));
    }

    /**
     * Comprobación si el acceso está bloqueado por exceso de
     * intentos de autenticación
     * @return boolean Estado del bloqueo
     */
    protected function IsBlocked(){
        // Comprobamos si se ha definido el contador de intentos
        if(isset($_SESSION["auth_count"])){
            // Obtenemos el valor del contador de bloqueos
            $count = intval($_SESSION["auth_count"]);
            // validar si se se han superado el máximo de intentos
            return $count >= 5;
        }
        return false;
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
    }

    /**
     * Se encarga de realizar el proceso de autenticación del usuario
     * a partir del nombre de usuario y el password utilizado.
     * En el caso de ser válidas las credenciales, se debe establecer
     * el usuario como autenticado en el contexto.
     * Devuelve el resultado de la autenticación como un valor booleano.
     * @var string $username Nombre de usuario
     * @var string $password Contraseña de usuario
     * @return boolean Estado de la validación
     */
    public function Authenticate($username, $password){
        // Validar que no está bloqueado el acceso
        if($this->IsBlocked()){
            return false;
        }
        // Validación de la sesión actual
        if($this->IsAuthenticate()){
            return true;
        }
        // Validar la información de usuario
        $auth = $this->ValidateUser($username, $password);
        // Comprobar el proceso de validación
        if(!$auth){
            $this->SetAuthenticateFail();
        }
        // retornar el resultado de la validación
        return $auth;
    }

    /**
     * Comprueba si la acción a ejecutar requiere que el usuario
     * esté autenticado o no
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la acción
     * @return boolean Indica si requiere autenticación
     */
    public function RequiredAuthentication($controller, $action){
        $required = false;
        if(array_key_exists ($controller, $this->Controllers)){
            $item = $this->Controllers[$controller];
            if(array_key_exists($action, $item["actions"])){
                $required = ($item["actions"][$action] != "");
            }
        }
        return $required;
    }

    /**
     * Obtener array de roles
     * @param string $strRoles Lista de roles separados por ','
     * @return array Colección de roles
     */
    private function GetArrayRoles($strRoles = ""){
        $roles = [];
        if($strRoles != ""){
            // Obtener vector de roles
            $roles = explode(",", $strRoles);
            // Liminar caracter espacio al principio y fin de cada role
            foreach($roles as $key => $role){
                $roles[$key] = trim($role);
            }
        }
        return $roles;
    }

    /**
     * Comprueba si el usuario tiene alguno de los roles
     * establecidos para la ejecución de la acción
     * @param array $roles Colección de roles de la acción
     * @return boolean Estado de la validación
     */
    private function ValidateUserRole($roles = null){
        // Error en los parámetros
        if(!is_array($roles)){
            return false;
        }
        // No require roles
        if(count($roles) == 0){
            return true;
        }

        // Obtener los roles del usuario
        $userRoles = $this->GetUserRoles();

        if(!is_array($userRoles)){
            return false;
        }

        $authorize = false;
        // Comprobar los roles del usuario
        foreach($userRoles as $rol){
            if(in_array($rol, $roles)){
                $authorize = true;
            }
        }

        return $authorize;
    }

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para ejecutar la acción del controlador.
     * Los criterios que determinan si el usuario debe ser autorizado
     * dependen de la aplicación donde el componente se integra.
     * Devuelve el resultado de la autorización como un valor booleano.
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la acción
     * @return boolean Estado de la autorización
     */
    public function Authorize($controller, $action){
        // Validar la existencia del control solicitado
        if(array_key_exists($controller, $this->Controllers)){

            $item = $this->Controllers[$controller];

            if(!array_key_exists($action, $item["actions"])){
                throw new \ResourceNotFoundException("Action not found: ".$action);
            }

            $sRoles = $item["actions"][$action];

            $roles = $this->GetArrayRoles($sRoles);

            return $this->ValidateUserRole($roles);
        }
        return true;
    }

    /**
     * Procesa las subcadenas de los roles
     * @param object $roles Colección de roles
     * @return object c
     */
    private function ProcessRoles($roles){
        // Validar el parámetro pasado
        if(!is_array($roles)){
            $roles = explode(",", $roles);
        }
        // Eliminar espacios en blanco de los nombres de role
        foreach($roles as $key => $role){
            $roles[$key] = trim($role);
        }
        return $roles;
    }

    /**
     * Filtro de acciones por roles de usuario
     * @param array $actions Colección de acciones disponibles
     * @param array $roles Colección de roles de usuario
     * @return array Colección de arrays accesibles
     */
    private function FilterActionsByRole($actions = null, $roles = null){

        $childrens = [];

        if($roles == null || $actions == null){
            return $childrens;
        }
        foreach($actions as $item){
            if($item["roles"] == ""
                    && $item["visible"] == true){
                $childrens[$item["name"]] = $item;
                continue;
            }
            $sRoles = explode(",", $item["roles"]);
            foreach($sRoles as $role){
                if(in_array(trim($role), $roles)
                        && $item["visible"] == true){
                    $childrens[$item["name"]] = $item;
                }
            }
        }
        return $childrens;
    }

    /**
     * Validación del controlador por roles de usuario
     * @param object $item Referencia a la información del controlador
     * @param array $roles Colección de roles disponibles
     * @return mixed Referencia al controlador filtrado o false
     */
    private function FilterControllerByRole($item = null, $roles = null){
        if($item != null){
            $childrens = $this->FilterActionsByRole(
                    $item["childrens"], $roles);
            settype($childrens, "array");
            $item["childrens"] = json_encode($childrens);

            if($item["roles"] == "" && $item["visible"] == true){
                return $item;
            }

            $sRoles = explode(",", $item["roles"]);
            foreach($sRoles as $role){
                if(in_array(trim($role), $roles)
                        && $item["visible"] == true){
                    return $item;
                }
            }
        }
        return false;
    }

    /**
     * Obtiene el array de controladores disponibles para el conjunto
     * de roles pasados como parámetros.
     * @var array $sRoles Colección de controladores
     */
    public function GetControllersByRol( $sRoles ){
        // Definir array de controladores disponibles
        $controllers = [];
        // Procesar roles
        $roles = $this->ProcessRoles($sRoles);
        // Filtrar la información de los controladores según el role
        foreach($this->Controllers as $value){
            if(($controller = $this->FilterControllerByRole(
                    $value, $roles)) == true){
                $controllers[] = $controller;
            }
        }
        return $controllers;
    }

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para acceder al controlador.
     * Los criterios que determinan si el usuario debe ser autorizado
     * dependen de la aplicación donde deba integrarse.
     * Devuelve el resultado de la autorización como un valor booleano.
     */
    public function AuthorizeController($controller){
        throw new \NotImplementedException( "AuthorizeController" );
    }

    /**
     * Obtiene el nombre del usuario autenticado en el contexto. En caso
     * de no haber usuario autenticado, el método devolverá una
     * cadena vacía.
     */
    public function GetUserName(){
        return (isset($_SESSION["user"])) ? $_SESSION["user"] : "";
    }

    /**
     * Obtiene un array con el/los roles asociados al usuario autenticado
     * en el contexto. En caso de no estar autenticado el usuario, debe
     * retornar un array vacío.
     */
    public function GetUserRoles(){
        throw new \NotImplementedException( "GetUserRoles" );
    }

    /**
     * Obtiene un objeto con la información del usuario almacenada
     * en el contexto. En caso de no estar el usuario autenticado,
     * se retornará el valor null.
     */
    public function GetUserData(){
        throw new \NotImplementedException( "GetUserData" );
    }

    /**
     * Obtiene un ticket de autenticación a partir de la información del
     * usuario autenticado. En caso de no estar el usuario autenticado,
     * se retornará una cadena vacía.
     */
    public function GetTicket(){
        throw new \NotImplementedException( "GetTicket" );
    }

    /**
     * Obtiene el nombre de la vista a utilizar para la acción, el
     * controlador y el usuario autenticado.
     * En el caso de no ser necesario (no hay filtro de contenidos),
     * retornará el nombre de la vista por defecto (mismo nombre
     * que la acción).
     */
    public function GetViewName($controller, $action){
        throw new \NotImplementedException( "GetViewName" );
    }
}
