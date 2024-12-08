<?php




    class SaasController extends Controller{


        protected $Security = null;


        public $Project = 0;


        public $ProjectName = "";


        public $ProjectPath = "";


        public $Service = 0;


        public function __construct(){
                         parent::__construct();
                         $this->Security = $this->Injector->Resolve( "ISecurity" );
                         $this->SetContext();
        }


        protected function SetContext(){
                         $this->Project = (isset($_SESSION["projectId"]))
                    ? $_SESSION["projectId"] : 0;
                         $this->ProjectName = (isset($_SESSION["projectName"]))
                    ? $_SESSION["projectName"] : "";
                         $this->ProjectPath = (isset($_SESSION["projectPath"]))
                    ? $_SESSION["projectPath"] : "";
                         $this->Service = (isset($_SESSION["serviceId"]))
                    ? $_SESSION["serviceId"] : 0;
        }


        protected function LogErrorTrace($method = "", $e = null){

            $error = (isset($e) && $e != null) ? $e->getMessage() : "";

            $msg = "Method: ".$method." - Info: ".$error;

            $this->LogError($msg);
        }

    }






    class SaasModel extends Model{


        protected $Security = null;


        public $Project = 0;


        public $ProjectName = "";


        public $ProjectPath = "";


        public $Service = 0;


        public $Username = "";


        public $eUsername = "";


        public $eUsernameClass = "";


        public $ePassword = "";


        public $ePasswordClass = "";


        public $eLogin = "";


        public $eLoginClass = "has-success";


        public function __construct(){
                         parent::__construct();
                         $this->Security = $this->Injector->Resolve( "ISecurity" );
                         $this->Menu = $this->Security->GetControllersByRol($this->Security->GetUserRoles());
                         $this->Username = $this->Security->GetUserName();
                         $this->SetDataContext();

            $this->SetLoginError();
        }


        private function SetDataContext(){
                         $this->Project = (isset($_SESSION["projectId"]))
                    ? $_SESSION["projectId"] : 0;
                         $this->ProjectName = (isset($_SESSION["projectName"]))
                    ? $_SESSION["projectName"] : "";
                         $this->ProjectPath = (isset($_SESSION["projectPath"]))
                    ? $_SESSION["projectPath"] : "";
                         $this->Service = (isset($_SESSION["serviceId"]))
                    ? $_SESSION["serviceId"] : 0;
        }


        private function SetLoginError(){

            if(isset($_SESSION["eUsername"])){
                $this->eUsername = $_SESSION["eUsername"];
                $this->eUsernameClass = "has-error";
                unset($_SESSION["eUsername"]);
            }

            if(isset($_SESSION["ePassword"])){
                $this->ePassword = $_SESSION["ePassword"];
                $this->ePasswordClass = "has-error";
                unset($_SESSION["ePassword"]);
            }

            if(isset($_SESSION["eLogin"])){
                $this->eLogin = $_SESSION["eLogin"];
                $this->eLoginClass = "has-error";
                unset($_SESSION["eLogin"]);
            }
        }
    }







    class SaasSecurity extends Security implements ISecurity{


        protected $Dao = null;


        protected $Service = 0;


        protected $Project = 0;


        protected function GetServiceID(){
            return (isset($_SESSION["serviceId"]))
                ? $_SESSION["serviceId"] : 0;
        }


        protected function GetProjectID(){
            return (isset($_SESSION["projectId"]))
                ? $_SESSION["projectId"] : 0;
        }


        protected function ConfigDAO(){
                         $strConn = ConfigurationManager::GetKey( "connectionString" );
                         $oConnData = ConfigurationManager::GetConnectionStr( $strConn );
                         $Ioc = Injector::GetInstance();
                         $this->Dao = $Ioc->Resolve( "IDataAccessObject" );
                         $this->Dao->Configure($oConnData);
        }


        public function __construct(){
                         parent::__construct();
                         $this->Service = $this->GetServiceID();
                         $this->Project = $this->GetProjectID();
                         $this->ConfigDAO();
        }


        private function SetError($username = "", $password = ""){
            if(!isset($username) || $username == ""){
                $_SESSION["eUsername"] =
                        "El campo nombre de usuario es obligatorio.";
            }

            if(!isset($password) || $password == ""){
                $_SESSION["ePassword"] =
                        "El campo password es obligatorio.";
            }
        }

        private function SetCookie($user = NULL, $password = ""){
            if($user != NULL && isset($_REQUEST["remember"])){
                // la cookie dura un año
                $time = time()+60*60*24*365;
                // Información de la cookie
                $u = [
                        "Username" => $user->Username,
                        "Password" => $password
                    ];
                // serializar
                $json = json_decode($u);
                // codificar
                $info = base64_encode($json);
                // Establecer como cookie el usuario serializado
                setcookie("rb-user-access", $info, $time);
                // Confirmar la creación de cookie cookie
                return TRUE;
            }
            return FALSE;
        }

        private function ProcessPassword($pass = ""){
            if($pass == ""){
                return $pass;
            }

            $hashFunction = filter_input(INPUT_POST, "hash",
                    FILTER_SANITIZE_STRING);

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


        protected function ValidateUser($username = "", $spassword = ""){
                         $this->SetError($username, $spassword);

            $password = $this->ProcessPassword($spassword);

                         $filter = ($this->Project > 0)
                ? array( "Username" => $username, "Password" => $password,
                    "IdService" => $this->Service,
                    "IdProject" => $this->Project )
                : array( "Username" => $username, "Password" => $password,
                    "IdService" => $this->Service );
                         $users = $this->Dao->GetByFilter( "AuthEntity" , $filter);
                         if(count($users) > 0 ){
                                 $_SESSION[ "user" ] = $username;
                $_SESSION[ "userid" ] = $users[0]->IdUser;

                $this->SetCookie($users[0], $spassword);

                return TRUE;
            }
                         $_SESSION[ "eLogin" ]
                    = "Las credenciales de usuario no han sido validadas.";
            return FALSE;
        }


        public function GetUserRoles(){
                         $roles = array();
                         if(!$this->IsAuthenticate()) {
                return $roles;
            }
                         $username = $this->GetUserName();
                         $filter = ($this->Project > 0)
                    ?array( "Username" => $username,
                        "IdService" => $this->Service,
                        "IdProject" => $this->Project )
                    :array( "Username" => $username,
                        "IdService" => $this->Service );
                         $result = $this->Dao->GetByFilter( "AuthEntity" , $filter);
                         foreach($result as $entity){
                $roles[] = trim($entity->Role);
            }
                         return $roles;
        }

    }




    class UserDTOUtils{


        public $Email = "";


        public $Password = "";


        public $Date = "";

    }


    class UserUtilities{


        private static function GetDao(){
                         $injector = Injector::GetInstance();
                         $dao = $injector->Resolve( "IDataAccessObject" );
                         $connectionString =
                    ConfigurationManager::GetKey( "connectionString" );
                         $oConnString =
                    ConfigurationManager::GetConnectionStr($connectionString);
                         $dao->Configure($oConnString);
                         return $dao;
        }


        private static function Create($factory = null, $user = null){
            if($factory != null && $user != null){
                                 $dao = UserUtilities::GetDao();
                                 $pass = $factory->GetPassword();
                                 $hash = $factory->GetSHA512( $pass );
                                 $user->Password = $hash;
                                 $dao->Create( $user );
            }
            return $pass;
        }


        private static function Update($factory = null, $user = null){
            if($factory != null && $user != null){
                                 $dao = UserUtilities::GetDao();
                                 $pass = $factory->GetPassword();
                                 $hash = $factory->GetSHA512( $pass );
                                 $user->Password = $hash;
                                 $dao->Update( $user );
            }
            return $pass;
        }


        private static function GetUserDto($user = null, $pass = ""){
                         $date = new DateTime( "NOW" );
                         $userDto = new UserDTOUtils();
            $userDto->Email = $user->Username;
            $userDto->Password = $pass;
            $userDto->Date = $date->format( "d-m-Y" );
            return $userDto;
        }


        private static function CreateNotification($data = null,
                $user = null, $userDto = null){
                         $date = new DateTime( "NOW" );
                         $dao = UserUtilities::GetDao();
                         $dto = new Notification();
            $dto->Project = $data[ "project" ];
            $dto->Service = $data[ "service" ];
            $dto->To = $user->Username;
            $dto->Subject =  "create-user";
            $dto->Content = json_encode($userDto);
            $dto->Date = $date->format( "Y-m-d" );
                         $dao->Create( $dto );
        }


        private static function ResetNotification($data = null,
                $user = null, $userDto = null){
                         $date = new DateTime( "NOW" );
                         $dao = UserUtilities::GetDao();
                         $dto = new Notification();
            $dto->Project = $data[ "project" ];
            $dto->Service = $data[ "service" ];
            $dto->To = $user->Username;
            $dto->Subject =  "create-user";
            $dto->Content = json_encode($userDto);
            $dto->Date = $date->format( "Y-m-d" );
                         $dao->Create( $dto );
        }


        private static function GetUserByEmail($email = ""){
                         $dao = UserUtilities::GetDao();
                         return $dao->GetByFilter( "User" , array( "Username" => $email ));
        }


        public static function CreateUser($data = null){
                         $result = false;
                         $factory = PasswordFactory::GetInstance();
                         if(isset($data)
                && $data != null
                    && is_array($data)
                        && isset($data["user"])
                            && is_object($data["user"])){
                                 $user = $data["user"];
                                 $pass = UserUtilities::Create($factory, $user);
                                 $userDto = UserUtilities::GetUserDto($user, $pass);
                                 UserUtilities::CreateNotification($data, $userDto);
                                 $result = true;
            }
            return $result;
        }


        public static function ResetPassword($data = null){
                         $result = false;
                         $factory = PasswordFactory::GetInstance();
                         $emails = UserUtilities::GetUserByEmail($data["email"]);
                         if(isset($emails) && $emails != null && count($emails) > 0){
                                 $user = $emails[0];
                                 $pass = UserUtilities::Update($factory, $user);
                                 $userDto = UserUtilities::GetUserDto($user, $pass);
                                 UserUtilities::ResetNotification($data, $user, $userDto);
                                 $result = true;
            }

            return $result;
        }


        public static function ChangePassword($data = null){
                         $result = false;
                         $dao = UserUtilities::GetDao();
                                      $filter = array( "Username" => $data[ "email" ],
                "Password" => $data[ "pass" ] );
                         $emails = $dao->GetByFilter( "User" , $filter);
                         if(isset($emails) && $emails != null && count($emails) > 0){
                                 $user = $emails[0];
                                 $hash = $data[ "newpass" ];
                                 $user->Password = $hash;
                                 $dao->Update( $user );
                                 $result = true;
            }
            return $result;
        }

    }







    class SaasHttpModule extends \HttpModule implements IHttpModule{


        protected function GetServiceName(){
                         $path = getcwd();
                         $pos = strrpos ( $path , "/" );
                         if( $pos === false ){
                throw new UrlException( "GetServiceName - ".$path );
            }
                         $path = substr( $path, $pos);
                         $name = str_replace( "/" , "" , $path);

            return $name;
        }


        protected function SetServiceData( $name = "" ){
                         $services = $this->Dao->GetByFilter( "Service",
                    array ( "Name" => $name ));
                         if(count($services) == 0){
                throw new UrlException( "BeginRequest - ".$name );
            }
                         $_SESSION["serviceId"] = $services[0]->Id;
            $_SESSION["serviceName"] = $services[0]->Name;
        }


        public function __construct(){
                         parent::__construct();
                         $this->Dao = $this->Injector->Resolve( "IDataAccessObject" );
                         $connectionString =
                    ConfigurationManager::GetKey( "connectionString" );
                         $oConnString =
                    ConfigurationManager::GetConnectionStr($connectionString);
                         $this->Dao->Configure($oConnString);
        }


        public function BeginRequest(){
                         $name = $this->GetServiceName();
                         $this->SetServiceData( $name );
                         $this->Security = $this->Injector->Resolve( "ISecurity" );
        }
    }

    ?>
