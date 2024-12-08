<?php


    interface IDataAccessObject{


        public function Configure($connection = null);


        public function Create($entity);


        public function Read($identity, $entityName);


        public function Update($entity);


        public function Delete($identity, $entityName);


        public function Get($entityName);


        public function GetByFilter($entityName, $filter);


        public function ExeQuery($query);


        public function IsValid($entity);

    }





    interface ILogManager{


        public function LogInfo($message);


        public function LogInfoTrace($message, $e);


        public function LogDebug($message);


        public function LogDebugTrace($message, $e);


        public function LogWarn($message);


        public function LogWarnTrace($message, $e);


        public function LogError($message);


        public function LogErrorTrace($message, $e);


        public function LogFatal($message);


        public function LogFatalTrace($message, $e);


        public static function GetInstance();
    }




    interface IValidatorClient{


        public function Validate($entity = null);


        public function Configure($fileName = "");


        public static function GetInstance();

    }





    interface IHttpModule{


        public function BeginRequest();


        public function ProcessRequest();


        public function EndRequest();


        public static function Start();


        public static function ApplicationError($errno = 0, $errstr = null,
                $errfile = null, $errline = null, $errcontext = null);


        public static function ApplicationFatal($errno = 0, $errstr = null,
                $errfile = null, $errline = null, $errcontext = null);

    }





    interface IHttpHandler{


        public function ValidateController($controller);


        public function Validate($controller, $action);


        public function SetDefault($controller, $action);


        public function ProcessUrl($urlRequest);


        public function ProcessParameters($parameters);


        public function SetLanguage($language);


        public function GetLanguage();


        public function Run($controller, $action, $params = null);


        public function RegisterRoutes($routes);
    }




    interface ISecurity{


        public function AuthenticateTicket($ticket);


        public function Authenticate($username, $password);


        public function RequiredAuthentication($controller, $action);


        public function AuthorizeController($controller);


        public function Authorize($controller, $action);


        public function GetUserName();


        public function GetUserRoles();


        public function GetUserData();


        public function GetTicket();


        public function GetViewName($controller, $action);


        public function GetControllersByRol($roles);

    }





    interface INotificator{


        public function Send($data, $content);


        public function GetTemplate($templateName);

    }





    class ConfigurationManager{


        private static $_configuration = null;


        private static $_filename = null;


        protected $References = array();


        protected $ConnectionStrings = array();


        protected $Settings = array();


        private function LoadRef($xml){
            if(isset($xml) && $xml != null && is_object($xml)){
                                 $nodes = $xml->references->children();
                                 $references = array();
                                 foreach($nodes as $node){
                                         $attributes = $node->attributes();
                                         $references[(string)$attributes->name]
                            = (string)$attributes->path;
                }
                $this->References = $references;
            }
        }


        private function LoadConnectionStrings($xml){
            if(isset($xml) && $xml != null && is_object($xml)){
                                 $nodes = $xml->connectionStrings->children();
                                 $connectionStrings = array();
                                 foreach($nodes as $node){
                                         $attributes = $node->attributes();
                                         $connectionStrings[(string)$attributes->name] =
                    array(
                        "server" => (string)$attributes->server,
                        "user" => (string)$attributes->user,
                        "password" => (string)$attributes->password,
                        "scheme" =>  (string)$attributes->scheme,
                        "filename" => (string)$attributes->filename
                    );
                }
                $this->ConnectionStrings = $connectionStrings;
            }
        }


        private function LoadSettings($xml = null){
            if(isset($xml) && $xml != null && is_object($xml)){
                                 $nodes = $xml->settings->children();
                                 $settings = array();
                                 foreach($nodes as $node){
                                         $attributes = $node->attributes();
                                         $settings[(string)$attributes->key]
                            = (string)$attributes->value;
                }
                $this->Settings = $settings;
            }
        }


        private function Load($xmlstr = "config.xml"){

            $sXml = ($xmlstr == "")
                    ? "config.xml": $xmlstr;

            if(file_exists ($sXml)){
                                 $configurator = simplexml_load_file($xmlstr);
                                 $this->LoadRef($configurator);
                $this->LoadConnectionStrings($configurator);
                $this->LoadSettings($configurator);
                return;
            }

            throw new ConfigurationManagerException( "config file not found" );
        }


        public function __construct($configFile = ""){
            $this->Load($configFile);
        }


        public static function GetConnectionStr($oConnName){
                         $obj = ConfigurationManager::
                    GetInstance(ConfigurationManager::$_filename);
                         return $obj->ConnectionStrings[$oConnName];
        }


        public static function GetKey($keyName, $fileName = ""){
                         $obj = ConfigurationManager::
                    GetInstance($fileName);
                         return $obj->Settings[$keyName];
        }


        public static function GetReferences($fileName = ""){
                         $obj = ConfigurationManager::
                    GetInstance($fileName);
                         return $obj->References;
        }


        public static function LoadReferences($fileName = ""){
                         $obj = ConfigurationManager
                    ::GetInstance($fileName);
            $references = $obj->References;
                         foreach($references as $value){
                                 tc_require_once($value);
            }
        }


        public static function GetInstance($configFile = ""){
                                      if(ConfigurationManager::$_configuration == null){
                ConfigurationManager::$_configuration
                        = new ConfigurationManager( $configFile );
            }
                         if(ConfigurationManager::$_filename == null
                    && $configFile != "" && $configFile != null){
                    ConfigurationManager::$_filename = $configFile;
            }
                         return ConfigurationManager::$_configuration;
        }


    }


    class ConfigurationManagerException extends BaseException{


        public function __construct($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class PasswordFactory{


        protected static $Factory = null;


        protected $Alphabet = "";


        protected $MinLength = 8;


        protected $MaxLength = 20;


        protected $Length = 12;


        private function __construct($sfile = ""){
                         $file = ($sfile == "") ? "config.xml": $sfile;

            if(file_exists ($file)){
                                 $xml = simplexml_load_file($file);
                $attr1 = $xml->passwordfactory->alphabet->attributes();
                                 $this->Alphabet = (string)$attr1["value"];
                $attr2 = $xml->passwordfactory->minlength->attributes();
                                 $this->MinLength = (string)$attr2["value"];
                $attr3 = $xml->passwordfactory->maxlength->attributes();
                                 $this->MaxLength = (string)$attr3["value"];
                $attr4 = $xml->passwordfactory->default->attributes();
                                 $this->Length = (string)$attr4["value"];
                                 return;
            }

            throw new PasswordFactoryException( "config file not found" );
        }


        public function GetPassword($length = 12){
                         if(!is_numeric($length)){
                $length = $this->Length;
            }
            else if($length < $this->MinLength){
                $length = $this->MinLength;
            }
            else if($length > $this->MaxLength){
                $length = $this->MaxLength;
            }
                         $cadena = $this->Alphabet;
                         $longitudCadena = strlen($cadena);
                         $pass = "";
                                      $longitudPass = $length;
                         for($i=1 ; $i <= $longitudPass ; $i++){
                                                  $pos=rand(0,$longitudCadena-1);
                                 $pass .= substr($cadena,$pos,1);
            }
            return $pass;
        }


        public function GetMD5($text = ""){
            return hash( "md5", $text );
        }


        public function GetSHA1($text = ""){
            return hash( "sha1", $text );
        }


        public function GetSHA256($text = ""){
            return hash( "sha256", $text );
        }


        public function GetSHA512($text = ""){
            return hash( "sha512", $text );
        }


        public static function GetInstance($file = ""){
            if(PasswordFactory::$Factory == null){
                PasswordFactory::$Factory = new PasswordFactory($file);
            }
            return PasswordFactory::$Factory;
        }
    }




    class PasswordFactoryException extends BaseException{


        public function __construct($message, $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }






    class AjaxException extends BaseException {


        public function __construct($message = "",
                $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class ArgumentException extends BaseException {


        public function __construct($message = "",
                $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }



    class BaseException extends Exception {


        public function __construct($message = "",
                $code = 0, Exception $previous = null) {

            if(($code != 0)&&($previous != null)){
                parent::__construct($message, $code, $previous);
            }

            if(($code == 0)&&($previous != null)){
                parent::__construct($message, $code, $previous);
            }

            if(($code != 0)&&($previous == null)){
                parent::__construct($message, $code);
            }

            if(($code == 0)&&($previous == null)){
                parent::__construct($message);
            }
        }


        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }


    class NotImplementedException extends BaseException {


        public function __construct($message="", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class SqlConnectionException extends BaseException {


        public function __construct($message="", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class SqlException extends BaseException {


        public function __construct($message="", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class UnAuthenticateException extends BaseException {


        public function __construct($message="", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class UnAuthorizeException extends BaseException {


        public function __construct($message="", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class UrlException extends BaseException {


        public function __construct($message="", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class FileManager{


        public static function GetFiles($path = ""){
                         $array = array();
                         if($path == "") {
                return $array;
            }
                         if(($fileManager = opendir($path))== true) {
                while (false !== ($file = readdir($fileManager))){
                                         $item =
                        array( "Path" =>  $path."/".$file ,"Name" => $file );
                                         array_push($array, $item);
                }
                                         closedir($fileManager);
            }
                         return $array;
        }


        public static function GetFilterFiles($path = "", $extension = ""){
                         $array = array();
                         if($path == ""){
                return $array;
            }

                         if($extension == ""){
                return FileManager::GetFiles($path);
            }
                             if(($fileManager = opendir($path))==true){
                while(false !== ($file = readdir($fileManager))) {
                                         $posicion = strrpos($file, "." );
                                         if(!is_numeric($posicion)){
                        continue;
                    }
                                         $ext = substr( $file, $posicion + 1);
                                         if($ext != "" && stristr($extension, $ext)){
                                                 $item = array( "Path" =>
                            $path."/".$file ,"Name" => $file );
                                                 array_push($array, $item);
                    }
                }
                                 closedir($fileManager);
            }
                         return $array;
        }


        public static function CopyDirectory( $source, $destination ) {
            if ( is_dir( $source ) ) {
                                 @mkdir( $destination, 0777 );
                                 chmod($destination, 0777);
                                 $directory = dir( $source );
                                 while ( FALSE !== ( $readdirectory = $directory->read())){
                                                              if ( $readdirectory == '.' || $readdirectory == '..' ){
                        continue;
                    }
                                         $PathDir = $source . '/' . $readdirectory;
                                         if(is_dir( $PathDir )){
                                                 FileManager::CopyDirectory( $PathDir,
                                $destination . '/' . $readdirectory );
                        continue;
                    }
                                         copy( $PathDir, $destination . '/' . $readdirectory );
                }
                                 $directory->close();
            }else {
                                 copy( $source, $destination );
            }
        }
    }




    class FileManagerException extends BaseException{


        public function __construct($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class Uploader{


        public $Path;


        public $AllowedExts;


        public $Name;


        public function __construct(){
            $this->Path = "";
            $this->AllowedExts = array("php", "PHP");
            $this->Name = "file";
        }


        public function Upload(){
                         $file =  $_FILES[$this->Name];
                         $extension = end(explode(".", $file["name"]));
                         if (in_array($extension, $this->AllowedExts)){
                if ($file["error"] > 0){
                    throw new UploaderException("Return Code: "
                            . $file["error"]);
                }

                if (file_exists($this->Path. $file["name"])){
                    throw new UploaderException($file["name"]
                            ." already exists.");
                }

                move_uploaded_file($file["tmp_name"],
                        $this->Path.$file["name"]);

                return;
            }

            throw new UploaderException("Invalid file");
        }


        public static function UploadFile($sFile="",
                $path="", $extension="", $overRide = false){
                         $file =  $_FILES[$sFile];
                         $fileName = $file[ "name" ];
                         $sExtension = ($extension=="")
                    ? explode(".", $fileName)
                    : $extension;
                         $ext = end($sExtension);
                         if (in_array($ext, $extension)){
                if ($file["error"] > 0){
                    throw new UploaderException("Return Code: "
                            . $file["error"]);
                }

                $exist = file_exists($path. $file["name"]);

                if($exist && !$overRide){
                    throw new UploaderException($file["name"]." "
                            . "already exists.");
                }
                elseif($exist && $overRide){
                    unlink($path. $file["name"]);
                }

                move_uploaded_file($file["tmp_name"], $path.$file["name"]);

                return;
            }

            throw new UploaderException("Invalid file");
        }
    }




    class UploaderException extends BaseException{


        public function __construct($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }





    class Injector{


        private static $_injector = null;


        private $_repository = null;


        private function __construct($fileName = ""){
                                      $sFileName = ($fileName == "" || $fileName == null)
                    ? "config.xml" : $fileName;
                         $configurator = simplexml_load_file($sFileName);
                         $this->_repository = array();
                         $this->Load($configurator);
        }


        private function Load($xml = null){
            if(isset($xml) && is_object($xml)){
                                 $nodes = $xml->interfaces->children();
                                 foreach($nodes as $node){
                                         $attributes = $node->attributes();
                                         $this->_repository[(string)$attributes->name] =
                            array(
                                "mapTo" => (string)$attributes->mapTo,
                                "src" => (string)$attributes->src
                            );
                }
            }
        }


        public function Resolve($interfaceName = "", $include = FALSE){
                         if(!array_key_exists($interfaceName, $this->_repository)){
                return null;
            }
                         $data = $this->_repository[$interfaceName];
                         if($data["mapTo"] == "" || $data["src"] == ""){
                return null;
            }
                         if($include == TRUE){
                require_once($data["src"]);
            }
                         $object = new $data["mapTo"]();
                         if (!$object instanceof $interfaceName){
                throw new InjectorException($interfaceName);
            }
                         return $object;
        }


        public static function GetInstance($fileName = ""){
                                      if(Injector::$_injector == null){
                Injector::$_injector = new Injector( $fileName );
            }
                         return Injector::$_injector;
        }
    }






    class InjectorException extends BaseException {


        public function InjectorException($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

    }




    class LogManager implements ILogManager{


        private static $_logmanager = null;


        protected $FileName = null;


        public function __construct($fileName = ""){
            if(LogManager::$_logmanager == null){
                                 $this->FileName = $fileName;
                                 LogManager::$_logmanager = $this;
            }
        }


        private function WriteLog($type, $data){
            $date = new DateTime( "NOW" );
                         $trace = array( "date" => $date->format("Ynj - h:i:s A"),
                "level" => $type, "details" => $data );
                         $trace = json_encode($trace).",\n";
                         $fileName = ($this->FileName == "" || $this->FileName == null)
                    ? "logs/data-".date("Ynj").".log"
                    : $this->FileName.date("Ynj").".log";
                         $fp = fopen($fileName, 'a');
                         fwrite($fp, $trace);
                         fclose($fp);
        }


        private function WriteErrorLog($type, $data, $e){
            $date = new DateTime( "NOW" );
                         $trace = array(
                "date" => $date->format("Ynj - h:i:s A"),
                "level" => $type, "details" => $data,
                "exception" => $e->getMessage());
                         $trace = json_encode($trace).",\n";
                         $fileName = ($this->FileName == "" || $this->FileName == null)
                    ? "logs/data-".date("Ynj").".log"
                    : $this->FileName.date("Ynj").".log";
                         $fp = fopen($fileName, 'a');
                         fwrite($fp, $trace);
                         fclose($fp);
        }


        public function LogInfo($message){
            $this->WriteLog( "Info" , $message);
        }


        public function LogInfoTrace($message, $e){
            $this->WriteErrorLog( "Info" , $message, $e);
        }


        public function LogDebug($message){
            $this->WriteLog( "Debug" , $message);
        }


        public function LogDebugTrace($message, $e){
            $this->WriteErrorLog( "Debug" , $message, $e);
        }


        public function LogWarn($message){
            $this->WriteLog( "Warn" , $message);
        }


        public function LogWarnTrace($message, $e){
            $this->WriteErrorLog( "Warn" , $message, $e);
        }


        public function LogError($message){
            $this->WriteLog( "Error" , $message);
        }


        public function LogErrorTrace($message, $e){
            $this->WriteErrorLog( "Error" , $message, $e);
        }


        public function LogFatal($message){
            $this->WriteLog( "Fatal" , $message);
        }


        public function LogFatalTrace($message, $e){
            $this->WriteErrorLog( "Fatal" , $message, $e);
        }


        public static function GetInstance($fileName = ""){
                         if(LogManager::$_logmanager == null){
                LogManager::$_logmanager = new LogManager( $fileName );
            }
                         return LogManager::$_logmanager;
        }

    }







    class Controller{


        protected $_PrivateMethods = array( "PartialView", "GetActionName" );


        protected $Pattern = "<!--NAME-->";


        protected $ClassName = "Controller";


        protected $Log = null;


        protected $Injector = null;


        public function __construct(){
                         $this->Injector = Injector::GetInstance();
                         $this->Log = $this->Injector->Resolve( "ILogManager" );
                         $this->ClassName = get_class($this);
        }


        private function FindPattern($sName = "", $content = ""){
            $result = "";
                         $name = str_replace("NAME", $sName, $this->Pattern);
                         $start = strpos( $content , $name );
                         if($start === FALSE){
                return $result;
            }
                         $end = strpos( $content , $name , ($start + 1));
                         if($end === FALSE){
                return $result;
            }
                         return substr( $content , $start , ($end - $start));
        }


        private function GetActionName(){
                         $trace = debug_backtrace();
                         foreach($trace as $method){
                                 $function = $method["function"];
                                 $class = $method["class"] == $this->ClassName;
                                                  $action = !in_array($function, $this->_PrivateMethods);
                                 if($class && $action) {
                    return $function;
                }
            }
            return "";
        }


        private function ClearPatternSubrArray($propertyName="", $item=""){
            $pattern = $this->FindPattern($propertyName, $item);
            $sItem = str_replace($pattern, "", $item);
            return str_replace("<!--$propertyName-->", "", $sItem);
        }


        private function ReplaceSubArray($view="", $name="", $array = null){
            if(is_object($array)){
                settype($array, "array");
            }
            $temp = ""; $sView = "";
                         $pattern = $this->FindPattern($name, $view);

            foreach($array as $items){
                $temp = $pattern;

                if(!is_array($items)){
                    settype($items, "array");
                }

                foreach($items as $key => $value){
                    $val = str_replace("{item.$name.$key}",$value, $temp);
                    $temp = ($val != $pattern ) ? $val : "";
                }
                $sView .= $temp;
            }

                         $tag = str_replace("NAME", $name, $this->Pattern);

            return str_replace($tag, "", $sView);
        }


        private function ReplaceItem($item="", $propertyName="",
                $propertyValue = NULL){
            if(is_array($propertyValue)|| is_object($propertyValue)){
                $item .= $this->ReplaceSubArray($item,
                        $propertyName, $propertyValue );
                $item = $this->ClearPatternSubrArray(
                        $propertyName, $item);
            }
            else{
                $item = str_replace("{item.$propertyName}",
                        $propertyValue, $item);
            }
            return $item;
        }


        protected function ReplaceArray($sView="", $name="", $array=NULL){
                         $match = $this->FindPattern($name, $sView);
                         $toReplace = "";
                         foreach($array as $object){
                settype($object, "array");
                $item = $match;
                foreach($object as $propertyName => $propertyValue){
                    $item = $this->ReplaceItem($item,
                            $propertyName, $propertyValue);
                }
                $toReplace .= $item;
            }
                         $view = str_replace($match, $toReplace, $sView);
                         return str_replace("<!--$name-->", "", $view);
        }


        protected function ReplaceObject($view="",
                $name="", $object=null){
                         if(!is_array($object)){
                settype($object, "array");
            }
                         foreach($object as $key => $value){
                                                  if(is_array($value) || is_object($value)){
                    continue;
                }
                                                  $view = str_replace("{".$name.".".$key."}", $value, $view);
            }
                         return $view;
        }


        protected function GetViewContent($filepath = ""){
                         $fileContent = file_get_contents($filepath);
                         $start = strpos($fileContent, "<!--Layout={");
            $last = strpos( $fileContent, "}-->");
            if($start !== false && $last !== false){
                $start = $start + 12;
                $length = $last - $start;
                $layout = substr($fileContent, $start, $length);
                if($layout != ""){
                    $layout = "view/shared/".$layout;
                    $fileContent = str_replace("{BODY}", $fileContent,
                            file_get_contents($layout));
                }
            }
            return $fileContent;
        }


        protected function ProcessView($view="", $model=null){
                         if(is_object($model)){
                settype($model, "array");
            }
                         foreach($model as $propertyName => $propertyValue){
                if(is_array($propertyValue)){
                    $view = $this->ReplaceArray($view,
                            $propertyName, $propertyValue);
                }
                else if(is_object($propertyValue)){
                    $view = $this->ReplaceObject($view,
                            $propertyName, $propertyValue);
                }
                else{
                    $view = str_replace("{".$propertyName."}",
                            $propertyValue, $view);
                }
            }
                         return $view;
        }


        protected function Render($filepath = ""){
            if($filepath == "" || !file_exists ($filepath)){
                throw new ResourceNotFound("file name :".$filepath);
            }
            return $this->GetViewContent($filepath);
        }


        protected function RenderView($filepath="", $model=null){
            if($filepath == "" || !file_exists ($filepath)){
                throw new ResourceNotFound("file name :".$filepath);
            }

            $view = $this->GetViewContent($filepath);

            return $this->ProcessView($view, $model);
        }


        public function PartialView($model = null){
                         $actionName = $this->GetActionName().".html";
                         $className = str_replace("Controller", "", get_class($this));
                         $filePath = "view/".$className."/".$actionName;
                         return ($model == null)
                ? $this->Render($filePath)
                : $this->RenderView($filePath, $model);
        }


        public function Partial($viewName = "", $model = null){
                         $actionName = $viewName.".html";
                         $className = str_replace("Controller", "", get_class($this));
                         $filePath = "view/".$className."/".$actionName;
                         return ($model == null)
                ? $this->Render($filePath)
                : $this->RenderView($filePath, $model);
        }


        private function ReadEntityFromRequest($entity = null,
                $array = null){
            if($entity != null && $array != null){
                                 foreach( $array as $key => $value){
                    if(isset($_REQUEST[$key])){

                        $item = $_REQUEST[$key];
                                                 $value = strip_tags($item);
                                                 $entity->{ $key } = $value;
                    }
                }
            }
            return $entity;
        }


        public function GetEntity($entityName = ""){
                         if($entityName == "" ){
                return null;
            }
                         $temp = new $entityName();
                         $entity = new $entityName();
                                      settype( $temp , "array" );
                         return $this->ReadEntityFromRequest($entity, $temp);
        }


        public function RedirectTo($action = "",
                $controller = "", $args = null){
                         $path = ConfigurationManager::GetKey( "path" );
                         $url = $path."/".$controller."/".$action;
            $params = "";
            if(is_array($args)){
                foreach($args as $key => $value){
                    $params .= "&".$key."=".$value;
                }
                if(count($args) > 0){
                    $params = substr($params, 1);
                }
            }
                         $url = (strlen($params) > 0) ? $url."?".$params : $url;
                         return "<script type='text/javascript'>"
                . "window.location=\"".$url."\"</script>";
        }


        public function ReturnJSON($obj = NULL){
            $returnValue = "[]";
            header('Content-Type: application/json');
            if($obj != NULL){
                $returnValue = json_encode($obj);
            }
            return $returnValue;
        }
    }




    class Model{


        protected $Injector = null;


        protected $Dao = null;


        public $Path = "";


        public $Resources = "";


        public $Title = "";


        public $Menu = array();


        public $ErrorList = array();


        public function __construct(){
                         $this->Path = ConfigurationManager
                    ::GetKey( "path" );
                         $this->Resources = ConfigurationManager
                    ::GetKey( "resources" );
                         $connectionString = ConfigurationManager
                    ::GetKey( "connectionString" );
                         $oConnString = ConfigurationManager
                    ::GetConnectionStr($connectionString);
                         $this->Injector = Injector::GetInstance();
                         $this->Dao = $this->Injector->Resolve( "IDataAccessObject" );
                         $this->Dao->Configure($oConnString);
        }
    }




    class MailNotificator implements INotificator{


        public function Send($data, $content){

            if(!is_array($data)){
                throw new MailNotificatorException( "data - is not array" );
            }

            if (!array_key_exists( "To" , $data)){
                throw new MailNotificatorException( "To - is not defined" );
            }

            if (!array_key_exists( "From" , $data)){
                throw new MailNotificatorException( "From - is not defined" );
            }

            if (!array_key_exists( "Subject" , $data)){
                throw new MailNotificatorException( "Subject - is not defined" );
            }

            if ( $content == "" ){
                throw new MailNotificatorException( "Content - is empty" );
            }

            $contentType = "Content-type: text/html; charset=iso-8859-1\r\n";
                         $headers = str_replace( "{FROM}",
                    $data["From"], "From: {FROM}\r\n " );
                         mail($data["To"],
                    $data["Subject"], $content, $contentType.$headers);
        }


        public function GetTemplate($templateName){
                         $path = ConfigurationManager::GetKey( $templateName );
                         $result = file_exists( $path ) ? file_get_contents( $path ) : "";
                         return $result;
        }
    }




    class MailNotificatorException extends BaseException{
                 public function __construct($message = "",
                $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class Notificator {


        public $ContentType =
                "Content-type: text/html; charset=iso-8859-1\r\n ";


        public $Header = "From: {FROM}\r\n ";


        public function Send( $data = null, $templateName = ""){
            if( $data == null || $templateName == "" ){
                return;
            }
                         $sContent = $this->GetTemplate( $templateName );
                         $this->Validate( $data, $sContent );
                         $object = (isset($data)) ? $data["Info"] : array();
                         $content = $this->GetContent( $object, $content );
                         $this->SendMail($data, $content);
        }


        private function Validate( $data, $content){

            if(!is_array($data)){
                throw new NotificatorException( "data - is not array" );
            }

            if (!array_key_exists( "To" , $data)){
                throw new NotificatorException( "To - is not defined" );
            }

            if ( $data[ "To" ] == ""){
                throw new NotificatorException( "To - is empty" );
            }

            if (!array_key_exists( "From" , $data)){
                throw new NotificatorException( "From - is not defined" );
            }

            if ($data[ "From" ] == ""){
                throw new NotificatorException( "From - is empty" );
            }

            if (!array_key_exists( "Subject" , $data)){
                throw new NotificatorException( "Subject - is not defined" );
            }

            if ( $data[ "Subject" ] == "" ){
                throw new NotificatorException( "Subject - is empty" );
            }

            if ( $content == "" ){
                throw new NotificatorException( "Content - is empty" );
            }
        }


        private function GetTemplate($templateName){
                         $path = ConfigurationManager::GetKey( $templateName );
                         $result = file_exists( $path ) ? file_get_contents( $path ) : "";
                         return $result;
        }


        private function GetContent($object = null, $content = ""){
                         if( $object == null || $content == "" ){
                return $content;
            }
                         if(!is_array($object)){
                settype( $object, "array" );
            }
                         foreach($object as $key => $value){
                $content = str_replace( "{".$key."}", $value, $content);
            }
            return $content;
        }


        private function SendMail($data, $sContent){
                         $this->Validate($data, $sContent);
                         $content = $this->GetContent( $data[ "Info" ], $content);
                         $headers = str_replace( "{FROM}", $data["From"],  $this->Header );
                         mail($data["To"], $data["Subject"],
                    $content, $this->ContentType.$headers);
        }
    }





    class NotificatorException extends BaseException{

        public function __construct($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

    }



                             class NotificatorDB {


        public $ContentType =
                "Content-type: text/html; charset=iso-8859-1\r\n ";


        public $Header = "From: {FROM}\r\n ";


        public function Send( $data = null, $templateName = ""){
                         if( $data == null || $templateName == "" ){
                return;
            }
                         $sContent = $this->GetTemplate( $templateName );
                         $object = (isset($data)) ? $data["Info"] : array();
                         $content = $this->GetContent( $object, $sContent );
                         $data["template"] = $templateName;
                         $this->SendMail($data, $content);
        }


        private function Validate( $data, $content){
            if(!is_array($data)){
                throw new NotificatorDBException( "data - is not array" );
            }

            if (!array_key_exists( "To" , $data)){
                throw new NotificatorDBException( "To - is not defined" );
            }

            if ( $data[ "To" ] == ""){
                throw new NotificatorDBException( "To - is empty" );
            }

            if (!array_key_exists( "From" , $data)){
                throw new NotificatorDBException( "From - is not defined" );
            }

            if ($data[ "From" ] == ""){
                throw new NotificatorDBException( "From - is empty" );
            }

            if (!array_key_exists( "Subject" , $data)){
                throw new NotificatorDBException( "Subject - is not defined" );
            }

            if ( $data[ "Subject" ] == "" ){
                throw new NotificatorDBException( "Subject - is empty" );
            }

            if ( $content == "" ){
                throw new NotificatorDBException( "Content - is empty" );
            }
        }


        private function GetTemplate($templateName){
                         $path = ConfigurationManager::GetKey( $templateName );
                         $result = file_exists( $path ) ? file_get_contents( $path ) : "";
                         return $result;
        }


        private function GetContent($object = null, $content = ""){
                         if( $object == null || $content == "" ){
                return $content;
            }
                         if(!is_array($object)){
                settype( $object, "array" );
            }
                         foreach($object as $key => $value){
                $content = str_replace( "{".$key."}", $value, $content);
            }

            return $content;
        }


        private function SendMail($data, $sContent){
                         $content = $this->GetContent( $data[ "Info" ], $sContent);
                         $this->Validate($data, $content);
                         $headers = str_replace( "{FROM}", $data["From"],  $this->Header );
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
                         $dao = Injector::GetInstance();
                         $dao->Create( $dto );
        }
    }




    class NotificatorDBException extends BaseException{

        public function __construct($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }




    class Security implements ISecurity{


        protected $Controllers = null;


        private function ReadChildren($action = NULL){
            if($action != NULL){
                $attr = $action->attributes();
                return array(
                    "name" => (string)$attr->name,
                    "roles" => (string)$attr->roles,
                    "param" => (string)$attr->param,
                    "controller" => (string)$attr->controller,
                    "title" =>  (string)$attr->title,
                    "text" =>  (string)$attr->text,
                    "visible" =>  (isset($attr->visible)
                                && ((string)$attr->visible == "true"))
                        );
            }
            return FALSE;
        }


        private function GetChildrens($actions = NULL){
            $acciones = array();
            if($actions != NULL && is_object($actions)){
                foreach($actions as $action){
                    $item = $this->ReadChildren($action);
                    if($item != FALSE && $item["visible"] === TRUE){
                        $acciones[$item["name"]] = $item;
                    }
                }
            }
            return $acciones;
        }


        private function GetActions($actions = NULL){
            $acciones = array();
            if($actions != NULL && is_object($actions)){
                foreach($actions as $action){
                    $attr = $action->attributes();
                    $acciones[(string)$attr->name] = (string)$attr->roles;
                }
            }
            return $acciones;
        }


        private function ProcessControllerEntry($node = NULL){
            $actions = $node->actions->children();
            $acciones = $this->GetActions($actions);
            $childrens = $this->GetChildrens($actions);
            $attributes = $node->attributes();
            $name = (string)$attributes->name;
            $this->Controllers[$name] =
                    array( "name" => $name,
                        "actions" => $acciones,
                        "childrens" => $childrens,
                        "action" => (string)$attributes->action,
                        "roles" => (string)$attributes->roles,
                        "title" => (string)$attributes->title,
                        "text" => (string)$attributes->text,
                        "visible" => ( isset($attributes->visible)
                                && ( (string)$attributes->visible == "true" ) )
                        || (!isset($attributes->visible)));
        }


        protected function LoadControllers($xml){
                         $nodes = $xml->controllers->children();
                         $this->Controllers = array();
                         foreach($nodes as $node){
                $this->ProcessControllerEntry($node);
            }
        }


        public function AuthenticateTicket($ticket){
            throw new NotImplementedException( "AuthenticateTicket" );
        }


        protected function ValidateUser($username, $password){
            throw new NotImplementedException( "ValidateUser" );
        }


        protected function SetAuthenticateFail(){
            $count = (isset($_SESSION["auth_count"]))
                    ? intval($_SESSION["auth_count"]) : 0;
            $count++;
            $_SESSION["auth_count"] = $count;
        }


        protected function IsAuthenticate(){
                         return (isset($_SESSION["user"]) && ($_SESSION["user"] != ""));
        }


        protected function IsBlocked(){
                         if(isset($_SESSION["auth_count"])){
                                 $count = intval($_SESSION["auth_count"]);
                                 return $count >= 5;
            }
            return false;
        }


        public function __construct(){
                         $xmlstr = ConfigurationManager::GetKey( "configFile" );
                         $configurator = simplexml_load_file($xmlstr);
                         $this->LoadControllers($configurator);
        }


        public function Authenticate($username, $password){
                         if($this->IsBlocked()){
                return false;
            }
                         if($this->IsAuthenticate()){
                return true;
            }
                         $auth = $this->ValidateUser($username, $password);
                         if(!$auth){
                $this->SetAuthenticateFail();
            }
                         return $auth;
        }


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


        private function GetArrayRoles($strRoles = ""){
            $roles = array();
            if($strRoles != ""){
                                 $roles = explode(",", $strRoles);
                                 foreach($roles as $key => $role){
                    $roles[$key] = trim($role);
                }
            }
            return $roles;
        }


        private function ValidateUserRole($roles = null){
            if(isset($roles)
                    && is_array($roles)
                    && count($roles)){
                return true;
            }

                         $userRoles = $this->GetUserRoles();
                         if(isset($userRoles)){
                foreach($userRoles as $rol){
                    if(in_array($rol, $roles)){
                        return true;
                    }
                }
                $authorize = false;
            }
            else{
                $authorize = false;
            }
            return $authorize;
        }


        public function Authorize($controller, $action){
                         if(array_key_exists($controller, $this->Controllers)){

                $item = $this->Controllers[$controller];

                if(!array_key_exists($action, $item["actions"])){
                    throw new ResourceNotFound("Action not found: ".$action);
                }

                $sRoles = $item["actions"][$action];

                $roles = $this->GetArrayRoles($sRoles);

                return $this->ValidateUserRole($roles);
            }
            return TRUE;
        }


        private function ProcessRoles($roles){
                         if(!is_array($roles)){
                $roles = explode(",", $roles);
            }
                         foreach($roles as $key => $role){
                $roles[$key] = trim($role);
            }
            return $roles;
        }


        private function FilterActionsByRole($actions = NULL, $roles = NULL){

            $childrens = array();

            if($roles == NULL || $actions == NULL){
                return $childrens;
            }
            foreach($actions as $item){
                if($item["roles"] == ""
                        && $item["visible"] == TRUE){
                    $childrens[$item["name"]] = $item;
                                         continue;
                }
                $sRoles = explode(",", $item["roles"]);
                foreach($sRoles as $role){
                    if(in_array(trim($role), $roles)
                            && $item["visible"] == TRUE){
                        $childrens[$item["name"]] = $item;
                                             }
                }
            }
            return $childrens;
        }


        private function FilterControllerByRole($item = NULL, $roles = NULL){
            if($item != NULL){
                $childrens = $this->FilterActionsByRole(
                        $item["childrens"], $roles);
                settype($childrens, "array");
                $item["childrens"] = json_encode($childrens);

                if($item["roles"] == "" && $item["visible"] == TRUE){
                    return $item;
                }

                $sRoles = explode(",", $item["roles"]);
                foreach($sRoles as $role){
                    if(in_array(trim($role), $roles)
                            && $item["visible"] == TRUE){
                        return $item;
                    }
                }
            }
            return FALSE;
        }


        public function GetControllersByRol( $sRoles ){
                         $controllers = array();
                         $roles = $this->ProcessRoles($sRoles);
                         foreach($this->Controllers as $value){
                if(($controller = $this->FilterControllerByRole(
                        $value, $roles)) == TRUE){
                    $controllers[] = $controller;
                }
            }
            return $controllers;
        }


        public function AuthorizeController($controller){
            throw new NotImplementedException( "AuthorizeController" );
        }


        public function GetUserName(){
            return (isset($_SESSION["user"])) ? $_SESSION["user"] : "";
        }


        public function GetUserRoles(){
            throw new NotImplementedException( "GetUserRoles" );
        }


        public function GetUserData(){
            throw new NotImplementedException( "GetUserData" );
        }


        public function GetTicket(){
            throw new NotImplementedException( "GetTicket" );
        }


        public function GetViewName($controller, $action){
            throw new NotImplementedException( "GetViewName" );
        }
    }


    class StmtBaseDAO implements IDataAccessObject{


        private $_log = null;


        private $_isDebug = FALSE;


        protected $StmtClient = null;


        protected $ValidatorClient = null;


        private function SetDebug(){
                         $this->_isDebug = (DEBUG == 1);
        }


        private function SetReferences(){
                         $injector = Injector::GetInstance();
                         $this->_log = $injector->Resolve( "ILogManager" );
        }


        protected function LogQuery( $method = "",
                $entity = "" ,$query = "" ){
                         if(!$this->_isDebug) {
                return;
            }
                         if($this->_log == null) {
                return;
            }
                         $message = $method . " - ".$entity." - ".$query;
                         $this->_log->LogInfo( $message );
        }


        public function __construct(){
                         $this->SetDebug();
                         $this->SetReferences();
        }


        public function Configure($connection = null){
                         if ($connection == null){
                return;
            }
                         $this->StmtClient =
                    StmtClient::GetInstance(null, $connection["filename"]);
                         $this->ValidatorClient =
                    ValidatorClient::GetInstance($connection["filename"]);
        }


        public function Create($entity){
                         $entityName = get_class($entity);
                         $select = $this->StmtClient->GetCreateQuery($entityName);
                         $this->LogQuery( "Create", $entityName, $select);
                         $this->StmtClient->SetParameters($entity);
                         $this->StmtClient->Open();
                         $id = $this->StmtClient->Execute($select);
                         $this->StmtClient->Close();
                         return $id;
        }


        public function Read($identity, $entityName){
                         $select = $this->StmtClient->GetReadQuery( $entityName );
                         $this->LogQuery( "Read", $entityName, $select);
                         $entity = $this->StmtClient->GetEntity( $entityName, $identity );
                         $this->StmtClient->SetParameters( $entity );
                         $this->StmtClient->Open();
                         $result =	$this->StmtClient->ExecuteQuery( $select, $entityName );
                         $this->StmtClient->Close();
                         return (count($result) >0)? $result[0] : null;
        }


        public function Update($entity){
                         $entityName = get_class($entity);
                         $select = $this->StmtClient->GetUpdateQuery($entityName);
                         $this->LogQuery( "Update", $entityName, $select);
                         $this->StmtClient->SetParameters($entity);
                         $this->StmtClient->Open();
                         $data = $this->StmtClient->Execute($select);
                         $this->StmtClient->Close();
                         return $data;
        }


        public function Delete($identity, $entityName){
                         $select = $this->StmtClient->GetDeleteQuery($entityName);
                         $this->LogQuery( "Delete", $entityName, $select);
                         $entity = $this->StmtClient->GetEntity( $entityName, $identity );
                         $this->StmtClient->SetParameters($entity);
                         $this->StmtClient->Open();
                         $data = $this->StmtClient->Execute($select);
                         $this->StmtClient->Close();
                         return $data;
        }


        public function Get($entityName){
                         $select = $this->StmtClient->GetListQuery($entityName);
                         $this->LogQuery( "Get", $entityName, $select);
                         $this->StmtClient->Open();
                         $result = $this->StmtClient->ExecuteQuery($select, $entityName);
                         $this->StmtClient->Close();
                         return $result;
        }


        public function GetByFilter($entityName, $filter){
                         $select = $this->StmtClient->GetFilterQuery($entityName, $filter);
                         $this->LogQuery( "GetByFilter", $entityName, $select);
                         $entity = $this->StmtClient->SetEntity( $entityName, $filter);
                         $this->StmtClient->SetParameters($entity);
                         $this->StmtClient->Open();

                         $result =	$this->StmtClient->ExecuteQuery($select, $entityName);
                         $this->StmtClient->Close();
                         return $result;
        }


        public function ExeQuery($query){
                         $this->StmtClient->Open();
                         $this->StmtClient->ExecuteQuery($query);
                         $this->StmtClient->Close();
        }


        public function IsValid($entity){
                         if( $this->ValidatorClient == null) {
                return array();
            }
                         return $this->ValidatorClient->Validate($entity);
        }
    }





    class StmtClient {


        private static $_singleton = null;


        private $_filename;


        private $_oConnData = null;


        private $_dbName;


        private $_dbObjects = array();


        private $_oConn = null;


        private $_strTypes = "";


        private $_parameters = array();


        private $_query = "";


        private $_pkname = "";


        private $_where = "";


        private $_tablename = "";


        private function GetPropertyType($type = ""){
            if($type == "string" || $type == "date"){
                return "s";
            }
            else if ($type == "int" || $type == "bool"){
                return "i";
            }
            else if ($type == "double" || $type == "float"){
                return "d";
            }
            else{
                return "s";
            }
        }


        private function GetPropertyValue($type = "", $val= null){
            $value = $val;

            if($type == "bool"){
                $value = ($val) ? 1: 0;
            }

            return $value;
        }


        private function IsMapped($entityName = "" ){
                         if($entityName == ""){
                throw new StmtClientException("EntityName : is Empty");
            }
                                      if(!array_key_exists( $entityName, $this->_dbObjects )){
                throw new StmtClientException("EntityName : ".$entityName);
            }
                         return $this->_dbObjects[$entityName];
        }


        private function InitQueryParameters(){
                         $this->_parameters = array();
                         $this->_strTypes = "";
                         $this->_query = "";
            $this->_pkname = "";
            $this->_where = "";
            $this->_tablename = "";
        }


        private static function ReadAttributes($attrs = null){
            $atributos = array();
                         foreach($attrs as $attr){
                                 $attributes = $attr->attributes();
                                 $atributos[(string)$attributes->property] = array(
                    "Name" => (string) $attributes->name,
                    "Property" => (string) $attributes->property,
                    "DataType" => (string) $attributes->dataType,
                    "ColumnType" => (string) $attributes->columnType,
                    "Required" => (string) $attributes->required,
                    "MaxLength" => (isset($attributes->maxLength))
                        ? (string) $attributes->maxLength : "-",
                    "Min" => (isset($attributes->min))
                    ? (string) $attributes->min : "-",
                    "Max" => (isset($attributes->max))
                    ? (string) $attributes->max : "-"
                );
            }
            return $atributos;
        }


        private static function GetAttributes($object = null){
                         $atributos = array();
                         if($object == null){
                return $atributos;
            }
                         $attrs = $object->children();
                         return StmtClient::ReadAttributes($attrs);


        }


        private function Load($objects = null){
                         if( $objects == null ){
                return;
            }
                                      foreach($objects as $object){
                                 $attrs = StmtClient::GetAttributes($object);

                $attributes = $object->attributes();
                                 $this->_dbObjects[(string)$attributes->entity] = array(
                    "Type" =>(string)$attributes->type,
                    "Name" => (string)$attributes->name,
                    "Entity" => (string)$attributes->entity,
                    "Properties" =>$attrs
                );
            }
        }


        private function GetDataConnection($databaseNode = null){
                         if($databaseNode == null) {
                return array();
            }
                         $attrs = $databaseNode->attributes();
                         return array(
                    "server" => (string)$attrs->server,
                    "user" => (string)$attrs->user,
                    "password" => (string)$attrs->password,
                    "scheme" => (string)$attrs->scheme
             );
        }


        private function LoadDataBase(){
                         if(!file_exists( $this->_filename )){
                throw new Exception( "FileNotFound :".$this->_filename);
            }
                         $configurator = simplexml_load_file($this->_filename);
                         $this->_oConnData =
                    $this->GetDataConnection($configurator->database);
                         $objects = $configurator->objects->children();
                         $this->Load($objects);
        }


        private function __construct($fileName = ""){
                         $this->_filename = ($fileName != "")
                    ? $fileName : "database.xml";
                         $this->LoadDataBase();
        }


        public function __destruct(){
            $this->Close();
        }


        public function SetEntity($entityName, $arrayData){
                         $entity = new $entityName();
                         $reflector = new ReflectionClass($entityName);
                         $properties = $reflector->getProperties();
                         foreach($properties as $property){
                                 if(!array_key_exists($property->getName(), $arrayData)){
                    continue;
                }
                                 $entity->{ $property->getName() } =
                        $arrayData[$property->getName()];
            }
                         return $entity;
        }


        public function GetEntity($entityName, $identity){
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];
                         $entity = new $entityName();
                                      foreach($columns as $value){
                if($value["ColumnType"] == "pk"
                        || $value["ColumnType"] == "pk-auto"){
                    $entity-> { $value["Property"] } = $identity;
                }
            }
                         return $entity;
        }


        private function SetPropertyType($data = ""){
            $pos = strpos($data["Property"], "-filter");
                         if(!($pos === false)){
                $this->_strTypes .= $this->GetPropertyType( $data["DataType"] );
            }
        }


        public function SetParameters($entity = null){
                         if($entity != null){
                if(!is_array($entity)){
                    settype( $entity, "array" );
                }
                foreach($this->_parameters as $key => $value){

                    if(array_key_exists($value["Property"], $entity)) {
                                                 $this->_parameters[$key]["Value"] =
                                $this->GetPropertyValue($value["DataType"],
                                        $entity[$value["Property"]]);
                        $this->_strTypes .=
                                $this->GetPropertyType($value["DataType"]);
                        continue;
                    }

                    $this->SetPropertyType($value);

                }
            }
        }


        public function Execute($query){
            $stmt = $this->_oConn->prepare($query);
            $parameters = array();
            $parameters[] = $this->_strTypes;
            foreach($this->_parameters as $key => $value){
                $parameters[] = &$this->_parameters[$key]["Value"];
            }

            call_user_func_array(array($stmt, 'bind_param'), $parameters);

            if($stmt){
                if($stmt->execute()) {
                    $id = $stmt->insert_id;
                    $stmt->close();
                    return $id;
                }
                throw new StmtClientExecuteException(
                    "Execute execute fail : ".$stmt->error);
            }
            throw new StmtClientExecuteException(
                "Execute prepare fail : ".$this->_oConn->error);
        }


        public function ExecuteQuery($query, $entityName){
            $result = array();
            $temp = new $entityName();
            settype( $temp, "array" );

            if (true == $stmt = $this->_oConn->prepare($query)) {
                $count = count($this->_parameters);
                if( $count > 0){
                    $parameters[] = $this->_strTypes;
                    foreach($this->_parameters as $key => $value){
                        $parameters[] = &$this->_parameters[$key]["Value"];
                    }

                    call_user_func_array(array($stmt, 'bind_param'),
                            $parameters);
                }

                if($stmt->execute()){
                    $parameters = array();
                    foreach($temp as $key => $value){
                        if(is_array($value)) {
                            continue;
                        }
                        $parameters[$key] = &$temp[$key];
                    }

                    call_user_func_array(array($stmt, 'bind_result'),
                            $parameters);

                    while ($stmt->fetch()) {
                        $item = $this->SetEntity($entityName, $temp);
                        array_push($result, $item);
                    }
                    $stmt->close();
                }
                else{
                    throw new StmtClientExecuteException(
                            "ExecuteQuery execute fail : ".$stmt->error);
                }
            }
            else{
                throw new StmtClientExecuteException(
                        "ExecuteQuery prepare fail : ".$this->_oConn->error);
            }

            return $result;
        }


        public function GetReadQuery($entityName = ""){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];
                         foreach($columns as $value){
                $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
                if($value["ColumnType"] == "pk"
                        || $value["ColumnType"] == "pk-auto"){
                    $this->_pkname = $value["Name"];
                    array_push($this->_parameters, array (
                        "Name" => $value["Name"],
                        "Property" => $value["Property"],
                        "DataType" => $value["DataType"],
                        "Value" => ""
                    ));
                }
            }

            $this->_query = substr($this->_query, 1);

            return "SELECT ".$this->_query." FROM "
                    .$this->_tablename." WHERE ".$this->_pkname." = ? ;";
        }


        private function GetParamsAndNamesInsert($columns = null){

            $result = array( "Names" => "", "Params" => "" );

            if(isset($columns) && is_array($columns)){
                $sParams = "";
                $sNames = "";
                                 foreach($columns as $value){
                    if($value["ColumnType"] != "pk-auto"){
                        $sNames .= ", ".$value["Name"];
                        $sParams .= ", ?";
                        array_push($this->_parameters, array (
                                    "Name" => $value["Name"],
                                    "Property" => $value["Property"],
                                    "DataType" => $value["DataType"],
                                    "Value" => ""
                            ));
                    }
                }
                $result["Names"] = substr($sNames, 1);
                $result["Params"] = substr($sParams, 1);
            }
            return $result;
        }


        public function GetCreateQuery($entityName = ""){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];
                         $res = $this->GetParamsAndNamesInsert($columns);
                         return "INSERT INTO ".$this->_tablename
                ." (".$res["Names"].") VALUES (".$res["Params"].");";
        }


        public function GetUpdateQuery($entityName = ""){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];

            $pkName = "";
            $pkProperty = "";
            $pkDataType = "";

            foreach($columns as $key => $value){
                                                  if($value["ColumnType"] == "pk-auto"
                        || $value["ColumnType"] == "pk"){
                    $pkName = $value["Name"];
                    $pkProperty = $value["Property"];
                    $pkDataType = $value["DataType"];
                    continue;
                }

                $this->_query .= ", ".$value["Name"]." = ?";

                array_push($this->_parameters, array (
                                    "Name" => $value["Name"],
                                    "Property" => $value["Property"],
                                    "DataType" => $value["DataType"],
                                    "Value" => ""
                                ));
            }

            array_push($this->_parameters, array (
                                "Name" => $pkName,
                                "Property" => $pkProperty,
                                "DataType" => $pkDataType,
                                "Value" => ""
                            ));

            $this->_query = substr($this->_query, 1);

            return "UPDATE ".$this->_tablename." SET "
                    .$this->_query." WHERE ".$pkName." = ?;";
        }


        public function GetDeleteQuery($entityName = ""){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];

            foreach($columns as $key => $value){
                if($value["ColumnType"] == "pk"
                        || $value["ColumnType"] == "pk-auto"){
                    $this->pkname = $value["Name"];
                    array_push($this->_parameters, array (
                        "Name" => $value["Name"],
                        "Property" => $value["Property"],
                        "DataType" => $value["DataType"],
                        "Value" => ""
                    ));
                }
            }
            return "DELETE FROM ".$this->_tablename
                    ." WHERE ".$this->pkname." = ?;";
        }


        public function GetOrderByQuery($entityName = "", $order = null){
                         $object = $this->IsMapped($entityName);
                         $keys = array_keys ($order);
                         $sSqlQuery = $this->GetListQuery($entityName);
                         $sqlQuery = str_replace( ";", "", $sSqlQuery );
                         $columnas = $object["Properties"];
                         $columna = $columnas[$keys[0]]["Name"];
                         $tipo = $order[$keys[0]];
                         $sqlQuery .= " ORDER BY ".$columna." ".$tipo;
                         return $sqlQuery;
        }


        public function GetOrderByFilterQuery($entityName = "",
                $filter = null, $order = null){
                         $object = $this->IsMapped($entityName);
                         $keys = array_keys ($order);
                         $sSqlQuery = $this->GetFilterQuery($entityName, $filter);
                         $sqlQuery = str_replace( ";", "", $sSqlQuery );
                         $columnas = $object["Properties"];
                         $columna = $columnas[$keys[0]]["Name"];
                         $tipo = $order[$keys[0]];
                         $sqlQuery .= " ORDER BY ".$columna." ".$tipo;
                         return $sqlQuery;
        }


        public function GetFilterQuery($entityName = "", $filter = null){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];

            foreach($columns as $key => $value){
                $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
            }

            if(is_array($filter)){

                $nFilter = array();
                                 foreach($columns as $key => $value){
                    if(array_key_exists( $value["Property"], $filter)){
                        $value["Value"] = $filter[$value["Property"]];
                        $nFilter[$value["Name"]] = $value;
                    }
                }

                foreach($nFilter as $key => $value){

                    if($value["Value"] === NULL){
                        $this->_where .= " AND ".$key." is null";
                        continue;
                    }

                    $this->_where .= ($value["DataType"]=="string"
                            || $value["DataType"]=="date")
                            ? " AND ".$key." LIKE ?" : " AND ".$key." = ?";

                    array_push($this->_parameters, array (
                            "Name" => $key,
                            "Property" => $value["Property"]."-filter",
                            "DataType" => $value["DataType"],
                            "Value" => $value["Value"]
                        ));
                }

                if(strlen($this->_where) > 0){
                    $this->_where = " WHERE ".substr($this->_where, 4);
                }
            }

            $this->_query = substr($this->_query, 1);

            return "SELECT ".$this->_query." FROM "
                    .$this->_tablename." ".$this->_where.";";
        }


        public function GetStringFilterQuery($entityName = "", $filter = ""){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns =$object["Properties"];
                         foreach($columns as $value){
                $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
            }

            if(strlen($filter) > 0){
                $this->_where = " WHERE ".$filter;
            }

            $this->_query = substr($this->_query, 1);

            return "SELECT ".$this->_query." FROM "
                    .$this->_tablename." ".$this->_where.";";
        }


        public function GetListQuery($entityName = ""){
                         $this->InitQueryParameters();
                         $object = $this->IsMapped($entityName);
                         $this->_tablename = $object["Name"];
                         $columns = $object["Properties"];
                         foreach($columns as $value){
                $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
            }

            $this->_query = substr($this->_query, 1);

            return "SELECT ".$this->_query." FROM ".$this->_tablename.";";
        }


        public function SetDataConnection($data = null){
                         if($data == null){
                return;
            }
                         $this->_oConnData = $data;
        }


        public function Open(){
                         if($this->_oConnData == null){
                throw new StmtClientException('No data connection');
            }
                         $data = $this->_oConnData;
                         $this->_oConn = new mysqli($data["server"], $data["user"],
                    $data["password"],$data["scheme"]);
                         if (is_null(mysqli_connect_error())){
                return;
            }
                                                   throw new StmtClientException('Fail connection.. :'
                    . mysqli_connect_error());
        }


        public function Close(){

        }


        public static function GetInstance($fileName = ""){
                         if(StmtClient::$_singleton == null){
                                 StmtClient::$_singleton = new StmtClient($fileName);
            }
                         return StmtClient::$_singleton;
        }
    }





    class StmtClientException extends BaseException{


        public function __construct($message = "" , $code = 0,
                Exception $previous = null) {
           parent::__construct($message, $code, $previous);
        }
    }




    class StmtClientExecuteException extends BaseException{


        public function __construct($message = "", $code = 0,
                Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }





    class ValidatorClient implements IValidatorClient{


        private static $_singleton = null;


        private $_isConfigure = false;


        private function __construct(){

        }


        public function Validate($entity = null){
            return array();
        }


        public function Configure($fileName = ""){
            $this->_isConfigure = true;
        }


        public static function GetInstance($fileName = ""){
                         if(ValidatorClient::$_singleton == null){
                ValidatorClient::$_singleton = new ValidatorClient();
            }

            if($fileName != ""){
                ValidatorClient::$_singleton->Configure($fileName);
            }

            return ValidatorClient::$_singleton;
        }
    }





    class HttpHandler implements IHttpHandler{


        private $Controllers = array();


        private $Controller="";


        private $Action="";


        private $First=null;


        private $Params = array();


        private $GetLan = FALSE;


        private $Language = "";


        protected function LoadControllers($xml=null){
                         $nodes = $xml->controllers->children();
                         $this->Controllers = array();
                         foreach($nodes as $node){
                                 $actions = $node->actions->children();
                                 $actiones = array();
                                 foreach($actions as $action){
                                         $attr = $action->attributes();
                                         $actiones[(string)$attr->name] = array(
                        "roles" => (string)$attr->roles,
                        "params" =>  (string)$attr->params);
                }
                                 $attributes = $node->attributes();
                                 $this->Controllers[(string)$attributes->name] = array(
                                "actions" => $actiones,
                                "action" => (string)$attributes->action,
                                "roles" => (string)$attributes->roles
                        );
            }
        }


        private function SetUrlParts($parts = null){
                         $count = count($parts);
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


        private function SetUrlLanguageParts($parts = null){
                         $count = count($parts);
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



        protected function GetUrlParts($parts = null){
            if($this->GetLan){
                                 $this->SetUrlLanguageParts($parts);
            }
            else{
                $this->SetUrlParts($parts);
            }
        }


        public function __construct(){
                         $xmlstr = ConfigurationManager::GetKey( "configFile" );
                         $configurator = simplexml_load_file($xmlstr);
                         $this->LoadControllers($configurator);
                         $this->GetLan = isset($configurator->language);
                         if($this->GetLan){
                $this->Language =
                        (string)$configurator->language->attribute["default"];
            }
        }


        public function ValidateController($controller){
            return (array_key_exists ($controller , $this->Controllers ));
        }


        public function Validate($controller, $action){
            return (array_key_exists ($controller , $this->Controllers ))
                        && (array_key_exists ($action ,
                                $this->Controllers[$controller]["actions"]));
        }


        public function SetDefault($controller, $action){
            if(array_key_exists ($controller , $this->Controllers)){
                return array("Controller" => $controller,
                    "Action" => $this->Controllers[$controller]["action"]);
            }
            else{
                                 $message = "HttpHandler - SetDefault - Controller : "
                        .$controller.", Action : ".$action;
                                 throw new UrlException( $message );
            }
        }


        public function ProcessUrl($urlRequest){
                         $urlParts = explode("?", $urlRequest);
                         $urlPartsCount = count($urlParts);
                         if($urlPartsCount == 1){
                                 $url = $urlParts[0];
                                 $parts = explode("/", $url);
                                 $this->GetUrlParts($parts);
            }
            else if($urlPartsCount > 1){
                                 $url = $urlParts[0];
                                                  $parts = explode("/", $url);
                                 $this->GetUrlParts($parts);
                                 $this->Params = $urlParts[1];
            }
            else{
                throw new UrlException("HttpHandler - ProcessUrl - "
                        . $urlRequest);
            }
                         $this->ProcessParameters($this->Params);
                         $result = array(
                "Language" => $this->Language,
                "Controller" => $this->Controller,
                "Action" => $this->Action,
                "First" => $this->First,
                "Params" => $this->Params
            );
                         return $result;
        }


        public function ProcessParameters($parameters){
                         $params = array();
                         if(isset($this->First)){
                $params["id"] = $this->First;
            }
                         if(is_string($parameters)) {
                                 $parameters = explode("&", $parameters);
                                                  foreach($parameters as $value){
                    $parts = explode("=", $value);
                    if(count($parts) == 2){
                        $params[$parts[0]] = $parts[1];
                    }
                }
            }
                         $this->Params = $params;
        }


        public function SetLanguage($language){
                         $this->Language = $language;
                         $_SESSION["language"] = $language;
        }


        public function GetLanguage(){
            return (isset($_SESSION["language"]))
                ? $_SESSION["language"] : "";
        }


        public function Run($sController, $action, $params = null){
            $name = $sController."Controller";
            tc_require_once("controller/$name.php");
            $controller = new $name();
            return call_user_func_array(array($controller, $action), $params);
        }


        public function RegisterRoutes($routes){
            throw new NotImplementedException( "HttpHandler" );
        }

    }






    class HttpModule implements IHttpModule{


        public $Injector;


        public $HttpHandler;


        public $LogManager;


        public $Security;


        public $Render;


        protected function Authentication(){
                         if($this->Security->GetUserName() == ""){
                if(!isset($_POST["password"])
                        || !isset($_POST["username"])){
                                         $message = get_class()
                            ." - Authentication - no parameters";
                                         throw new UnAuthenticateException( $message );
                }

                if(!$this->Security->Authenticate(
                            $_POST["username"],
                            $_POST["password"]
                        )){
                    $message = "Authentication - user: "
                            .$_POST["username"]." , pass: "
                            .$_POST["password"];
                                         throw new UnAuthenticateException( $message );
                }
            }
        }


        protected function ValidateUrlData( $urlData = null){
            if(is_array($urlData)){
                                 if(!$this->HttpHandler->Validate(
                            $urlData["Controller"],
                            $urlData["Action"]
                        )){

                    if($urlData["Action"] == ""){
                        $result = $this->HttpHandler->SetDefault(
                                    $urlData["Controller"],
                                    $urlData["Action"]
                                );
                        $urlData["Controller"] = $result["Controller"];
                        $urlData["Action"] = $result["Action"];
                    }
                    else{
                        $message = "ValidateUrlData - Validate "
                                    .$_SERVER['REQUEST_URI'];

                        throw new UrlException($message);
                    }
                }
            }
            return $urlData;
        }


        protected function ValidateSecurity( $urlData ){
                         $required = $this->Security->RequiredAuthentication(
                        $urlData["Controller"],
                        $urlData["Action"]
                    );
                         if($required){
                                 $this->Authentication();
                                 if(!$this->Security->Authorize(
                            $urlData["Controller"],
                            $urlData["Action"]
                        )){
                                         $message = "ValidateSecurity - Authorize -"
                            .$_SERVER['REQUEST_URI'];

                    throw new UnAuthorizeException( $message );
                }
            }
            return $urlData;
        }


        public function __construct(){
                         HttpModule::Start();
                         $this->Injector = Injector::GetInstance();
                         $this->HttpHandler = $this->Injector->Resolve( "IHttpHandler" );
                         $this->LogManager = $this->Injector->Resolve( "ILogManager" );
        }


        public function BeginRequest(){

        }


        public function ProcessRequest(){
                         $urlData =
                    $this->HttpHandler->ProcessUrl($_SERVER['REQUEST_URI']);
                         $urlData = $this->ValidateUrlData( $urlData );
                         $urlData = $this->ValidateSecurity( $urlData );
                         $this->Render .= $this->HttpHandler->Run(
                        $urlData["Controller"],
                        $urlData["Action"],
                        $urlData["Params"]
                    );
        }


        public function EndRequest(){
            print $this->Render;
        }


        public static function Start(){

        }


        public static function ApplicationError($errno = 0, $errstr = null,
                $errfile = null, $errline = null, $errcontext = null){

        }


        public static function ApplicationFatal($errno = 0, $errstr = null,
                $errfile = null, $errline = null, $errcontext = null){

        }

    }





    function set_debug($debug = null){
                 if(!isset($debug)){
            $val = 1;
        }
        else{
            $val = ($debug) ? 1 : 0;
        }
        define( "DEBUG", $val);
    }


    function set_handlers( $level = E_ALL,
            $errorHandler = "application_error_handler",
            $exceptionHandler = "application_exception_handler"  ){
                 error_reporting($level);
                 set_error_handler($errorHandler);
                 set_exception_handler($exceptionHandler);
    }


    function set_session(){
        session_start();
    }


    function set_time($zone = 'Europe/Madrid' ){
        date_default_timezone_set( $zone );
    }


    function tc_include($filename = "" ){
        include($filename);
    }


    function tc_include_once($filename = "" ){
        include_once($filename);
    }


    function tc_require($filename = "" ){
        require($filename);
    }


    function tc_require_once($filename = "" ){
        require_once($filename);
    }


    function load_references($references = null){
                 if($references == null || !is_array($references)) {
            return;
        }
                 foreach($references as $reference){
            tc_require_once($reference);
        }
    }


    function set_cache($cache = FALSE){
        if($cache == FALSE){
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        }
    }


    function print_error($errno, $errstr = null, $errfile = null, $errline = null, $errcontext = null){
        $errstr = ($errstr != null) ? $errstr : "-";
        $errfile = ($errfile != null) ? $errfile : "-";
        $errline = ($errline != null) ? $errline : "-";
        $errcontext = ($errcontext != null) ? $errcontext : "-";
        echo "<div>";
        echo "<p>Se ha producido un error.</p>";
        echo "<ul>";
        echo "<li>N. Error : ".$errno."</li>";
        echo "<li>Fichero : ".$errfile."</li>";
        echo "<li>Linea : ".$errline."</li>";
        echo "<li>Detalles : ".$errstr."</li>";
        echo "<li>StackTrace : ".$errcontext."</li>";
        echo "</ul>";
        echo "</div>";
    }


    function application_error_handler($errno = 0, $errstr = null,
            $errfile = null, $errline = null, $errcontext = null){

        print_error($errno, $errstr, $errfile, $errline, $errcontext);

        exit();
    }


    function application_exception_handler($errno=0,$errstr = null,
            $errfile = null, $errline = null, $errcontext = null){

        print_error($errno, $errstr, $errfile, $errline, $errcontext);

        exit();
    }


    function setUrl($startController = ""){
                 $url = (isset($_REQUEST["url"]))
                ? $url = $_REQUEST["url"] : "";
                 if($url != ""){
            $_SERVER['REQUEST_URI'] = $url;
        }
                 if($_SERVER['REQUEST_URI'] == "/"){
            $_SERVER['REQUEST_URI'] = "/".$startController;
        }
    }


    function catchError($message = "", $fileName = "" , $e = NULL ){
                 $injector = Injector::GetInstance();
                 $log = $injector->Resolve( "ILogManager" );
                 $log->LogErrorTrace( $message , $e);
                 $sPath = ConfigurationManager::GetKey( "path" );
                 $sView = str_replace("{Path}" , $sPath,
                file_get_contents( "view/shared/".$fileName ));
                 $path = ConfigurationManager::GetKey( "resources" );
                 $view = str_replace( "{Resources}" , $path, $sView);
                 return processLoginError($view);
    }


    function replaceLoginError($view = ""){
        if(isset($_SESSION["eLogin"])){
            $sView = str_replace( "{eLogin}", $_SESSION["eLogin"], $view );
            $finalView = str_replace( "{eLoginClass}", "has-error", $sView );
            unset($_SESSION["eLogin"]);
        }
        else{
            $sView = str_replace( "{eLogin}", "", $view );
            $finalView = str_replace( "{eLoginClass}", "", $sView );
        }
        return $finalView;
    }


    function replaceUsernameError($view = ""){
        if(isset($_SESSION["eUsername"])){
            $sView = str_replace("{eUsername}",$_SESSION["eUsername"],$view);
            $finalView = str_replace("{eUsernameClass}","has-error",$sView);
            unset($_SESSION["eUsername"]);
        }
        else{
            $sView = str_replace( "{eUsername}", "", $view);
            $finalView = str_replace( "{eUsernameClass}","",$sView);
        }
        return $finalView;
    }


    function replacePasswordError($view = ""){
        if(isset($_SESSION["ePassword"])){
            $sView = str_replace("{ePassword}",$_SESSION["ePassword"],$view);
            $finalView = str_replace("{ePasswordClass}","has-error",$sView);
            unset($_SESSION["ePassword"]);
        }
        else{
            $sView = str_replace("{ePassword}","", $view);
            $finalView = str_replace("{ePasswordClass}","",$sView);
        }
        return $finalView;
    }


    function processLoginError($view = ""){
                 if($view != "" ){
                         $lView = replaceLoginError($view);
                         $uView = replaceUsernameError($lView);
                         $pView = replacePasswordError($uView);
                         $username = (isset($_REQUEST["username"]))
                    ? $_REQUEST["username"] : "";
                         $view = str_replace( "{username}", $username, $pView );
        }
        return $view;
    }


    function sendError($message = "", $e = null){

    } ?>
