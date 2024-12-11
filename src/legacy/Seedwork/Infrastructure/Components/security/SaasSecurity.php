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
    - Clase base Security y todas sus dependencias
    - Interfaz ISecurity
    - Componentes definidos : [ IDataAccessObject ]
    - Claves de config.xml : [ connectionString, urlReferer ]
    - Entidad de base de datos : Service, Project, AuthEntity
    - Variables de sessión (Opcional) : projectId, serviceId
*/

/**
 * Implementación de la interfáz de seguridad
 */
class SaasSecurity extends \Security implements \ISecurity{

    /**
     * Referencia al Objeto de acceso a datos
     * @var \IDataAccessObject
     */
    protected $Dao = NULL;

    /**
     * ID del servicio en ejecución
     * @var int
     */
    protected $Service = 0;

    /**
     * ID del proyecto actual
     * @var int
     */
    protected $Project = 0;

    /**
     * Obtiene el ID de servicio del contexto
     */
    protected function GetServiceID(){
        return (isset($_SESSION["serviceId"]))
            ? $_SESSION["serviceId"] : 0;
    }

    /**
     * Obtiene el ID de proyecto del contexto
     */
    protected function GetProjectID(){
        return (isset($_SESSION["projectId"]))
            ? $_SESSION["projectId"] : 0;
    }

    /**
     * Constructor por defecto
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
        // Asignar ID del servicio actual
        $this->Service = $this->GetServiceID();
        // Asignar el ID del proyecto (si hay)
        $this->Project = $this->GetProjectID();
        // Obtener una referencia al Inyector de dependencias
        $Ioc = Injector::GetInstance();
        // Obtener el nombre de la cadena de conexión a utilizar
        $strConn = ConfigurationManager::GetKey( "connectionString" );
        // Obtener datos de conexión a bbdd
        $oConnData = ConfigurationManager::GetConnectionStr( $strConn );
        // Inyectar referencia
        $this->Dao = $Ioc->Resolve( "IDataAccessObject" );
        // Configurar DAO
        $this->Dao->Configure($oConnData);
    }

    /**
     * Establece los errores básicos en el proceso de autenticación
     * @param string Nombre de usuario
     * @param string Contraseña de usuario
     */
    private function SetError($username = "", $password = ""){
        if(empty($username)){
            $_SESSION["eUsername"] = "El campo nombre de usuario es obligatorio.";
        }

        if(empty($password)){
            $_SESSION["ePassword"] = "El campo password es obligatorio.";
        }
    }

    /**
     * Procesa la constraseña enviada calculando su función hash si se ha
     * especificado en la solicitud http mediante el parámetro "hash".
     * Si el parámetro no es especificado, se entiende que no hay que
     * tratar la contraseña. En caso contrario, se aplicará la función hash
     * definida en el parámetro o md5 por defecto.
     * @param string $pass Contraseña enviada en la solicitud http
     * @return string Contraseña tratada
     */
    private function ProcessPassword($pass = ""){

        if(empty($pass)){
            return $pass;
        }

        $hashFunction = filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING);

        if($hashFunction!== FALSE && $hashFunction!== NULL){
            $hashFunction = strtoupper(trim($hashFunction));
            $algo = "md5";
            if($hashFunction == "SHA1"){
                $algo = "sha1";
            }
            else if($hashFunction == "SHA256"){
                $algo = "sha256";
            }
            else if($hashFunction == "SHA512"){
                $algo = "sha512";
            }
            return hash($algo, $pass);
        }
        return $pass;
    }

    /**
     * Valida los datos de usuario contra el repositorio actual
     * y establece en la sesión los datos de usuario
     * [ $_SESSION["user"], $_SESSION["userid"] ]
     * @param string Nombre de usuario
     * @param string Contraseña de usuario
     * @return boolean
     */
    protected function ValidateUser($username = "", $spassword = ""){
        // Establecer los errores de login
        $this->SetError($username, $spassword);

        $password = $this->ProcessPassword($spassword);

        // Definir el filtro de búsqueda
        $filter = ($this->Project > 0)
            ? [ "Username" => $username, "Password" => $password,
                "IdService" => $this->Service,
                "IdProject" => $this->Project ]
            : [ "Username" => $username, "Password" => $password,
                "IdService" => $this->Service ];
        // Obtener el usuario de base de datos
        $users = $this->Dao->GetByFilter( "AuthEntity" , $filter);
        // Comprobar que hay resultados válidos: nos quedamos con el primero
        if(count($users) > 0 ){
            // almacenar en sesión datos de usuario
            $_SESSION[ "user" ] = $username;
            $_SESSION[ "userid" ] = $users[0]->IdUser;
            return TRUE;
        }
        // Mensaje de error para el login
        $_SESSION[ "eLogin" ]
                = "Las credenciales de usuario no han sido validadas.";
        return FALSE;
    }

    /**
     * Obtiene un array con el/los roles asociados al usuario autenticado
     * en el contexto. En caso de no estar autenticado el usuario, debe
     * retornar un array vacío.
     */
    public function GetUserRoles(){
        // Colección de roles de usuairo
        $roles = [];
        // Comporbar que el usuario está autenticado
        if(!$this->IsAuthenticate()) {
            return $roles;
        }
        // Obtener usuario del contexto
        $username = $this->GetUserName();
        // Definir el filtro
        $filter = ($this->Project > 0)
                ?["Username" => $username,
                    "IdService" => $this->Service,
                    "IdProject" => $this->Project ]
                :[ "Username" => $username,
                    "IdService" => $this->Service ];
        // Obtener el usuario de base de datos
        $result = $this->Dao->GetByFilter( "AuthEntity" , $filter);
        // Agregar los roles obtenidos al array
        foreach($result as $entity){
            $roles[] = trim($entity->Role);
        }
        // Retornar array de roles
        return $roles;
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
        // Comprobación si se utiliza un ticket de autenticación
        $ticket = filter_input(INPUT_GET, "ticket");
        // Proceso de validación del ticket
        if($ticket != FALSE && $ticket != NULL){
            return $this->AuthenticateTicket($ticket);
        }
        // ejecutar el proceso de autenticación básico
        return parent::Authenticate($username, $password);
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
        $arr = $this->ValidateTicket($ticket);
        if(is_array($arr)){
            // Validar origen del ticket
            if($arr["source"] != $_SERVER["REMOTE_ADDR"]){
                return FALSE;
            }
            // Validar fecha ticket
            $date = new \DateTime($arr["date"]);
            $now = new \DateTime("NOW");
            if(intval($date->format("d")) < intval($now->format("d"))){
                return FALSE;
            }
            // Establecer los datos de sesión
            $_SESSION["user"] = $arr["user"];
            $_SESSION["userid"] = $arr["userid"];
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Proceso para la validación del formato/estructura del ticket
     * @param string $ticket ticket de autenticación
     * @return mixed Retorna FALSE si falla el proceso o referencia al array con
     * la información del ticket
     */
    private function ValidateTicket($ticket = ""){
        // Validar tipología del parámetro
        if(!is_string($ticket)){
            return FALSE;
        }
        $json = base64_decode($ticket);
        // verificar la decodificación
        if($json == FALSE){
            return FALSE;
        }
        $arr = json_decode($json);
        // verificar la decodificación json
        if($arr == NULL || !is_object($arr)){
            return FALSE;
        }

        settype($arr, "array");

        if(empty($arr["user"])||empty($arr["userid"])
                ||empty($arr["source"])||empty($arr["date"])){
            return FALSE;
        }
        return $arr;
    }

    /**
     * Obtiene un ticket de autenticación a partir de la información del
     * usuario autenticado. En caso de no estar el usuario autenticado,
     * se retornará una cadena vacía.
     */
    public function GetTicket(){
        $ticket = "";
        if($this->IsAuthenticate()){
            $date = new \DateTime("NOW");
            $obj = [
                "user" => $_SESSION[ "user" ],
                "userid" => $_SESSION["userid"],
                "source" => $_SERVER["REMOTE_ADDR"],
                "date" => $date->format("Y-m-d H:i:s")
            ];
            $sObj = json_encode($obj);
            $ticket = base64_encode($sObj);
        }
        return $ticket;
    }
}
