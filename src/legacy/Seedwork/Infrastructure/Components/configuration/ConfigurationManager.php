<?php

declare(strict_types=1);

/**
 * Clase para el acceso al fichero de configuración
 *
 * @author alfonso
 */
class ConfigurationManager{

    /**
     * Referencia al gestor de configuraciones actual
     * @var \ConfigurationManager $_configuration Referencia al gestor
     * de configuraciones
     */
    private static $_configuration = null;

    /**
     * Referencia al nombre del fichero de configuraciones actual
     * @var string $_filename Nombre del fichero de configuración
     */
    private static $_filename = null;

    /**
     * Colección de las referencias definidas
     * @var array $References Colección de referencias a dependencias del
     * proyecto
     */
    protected $References = array();

    /**
     * Colección de las cadenas de conexión definidas
     * @var array $ConnectionStrings Colección de cadenas de conexión
     * configuradas
     */
    protected $ConnectionStrings = array();

    /**
     * Colección de pares clave-valor para la aplicación
     * @var array $Settings Colección de claves configuradas
     */
    protected $Settings = array();

    /**
     * Carga toda la información de las referencias y dependencias
     * @var object $xml Referencia al nodo xml a cargar
     */
    private function LoadRef($xml){
        if(isset($xml) && $xml != null && is_object($xml)){
            // Obtener la lista de referencias
            $nodes = $xml->references->children();
            // Array de referencias
            $references = array();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $references[(string)$attributes->name]
                        = (string)$attributes->path;
            }
            $this->References = $references;
        }
    }

    /**
     * Carga toda la información sobre cadenas de conexión
     * @var object $xml Referencia al nodo xml a cargar
     */
    private function LoadConnectionStrings($xml){
        if(isset($xml) && $xml != null && is_object($xml)){
            // Obtener la lista de configuraciones
            $nodes = $xml->connectionStrings->children();
            // Array de connectionString
            $connectionStrings = array();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
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

    /**
     * Carga toda la información sobre parámetros de configuración
     * @var object $xml Referencia al nodo xml a cargar
     */
    private function LoadSettings($xml = null){
        if(isset($xml) && $xml != null && is_object($xml)){
            // Obtener la lista de configuraciones
            $nodes = $xml->settings->children();
            // Array de configuraciones
            $settings = array();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $settings[(string)$attributes->key]
                        = (string)$attributes->value;
            }
            $this->Settings = $settings;
        }
    }

    /**
     * Carga toda la información del fichero de configuración en
     * un array en memoria
     * @var string $xmlstr Ruta al fichero de configuración
     */
    private function Load($xmlstr = "config.xml"){

        $sXml = ($xmlstr == "")
                ? "config.xml": $xmlstr;

        if(file_exists ($sXml)){
            // Cargamos el contenido de la configuración desde el xml
            $configurator = simplexml_load_file($sXml);
            // Cargar los datos de configuración
            $this->LoadRef($configurator);
            $this->LoadConnectionStrings($configurator);
            $this->LoadSettings($configurator);
            return;
        }

        throw new \ConfigurationManagerException( "config file not found" );
    }

    /**
     * Constructor por defecto
     * @var string $configFile Ruta al fichero de configuración
     */
    public function __construct($configFile = ""){
        $this->Load($configFile);
    }

    /**
     * Obtiene un diccionario con los parámetros de conexión a
     * base de datos identificados con el nombre oConnName.
     * @var string $oConnName Nombre de la conexión a base de datos
     */
    public static function GetConnectionStr($oConnName){
        // Obtener una referencia
        $obj = ConfigurationManager::
                GetInstance(ConfigurationManager::$_filename);
        // retornamos la cadena seleccionada
        return $obj->ConnectionStrings[$oConnName];
    }

    /**
     * Obtiene el valor almacenado en la clave de configuración
     * identificada como keyName.
     * @var string $keyName Nombre de la clave a buscar
     * @var string $fileName Ruta del fichero de configuración
     */
    public static function GetKey($keyName, $fileName = ""){
        // Obtener una referencia
        $obj = ConfigurationManager::
                GetInstance($fileName);
        // retornamos la cadena seleccionada
        return $obj->Settings[$keyName];
    }

    /**
     * Obtiene un array con los datos de acceso a cada una de las
     * referencias que deben cargarse para la ejecución.
     * @var string $fileName Ruta del fichero de configuración
     */
    public static function GetReferences($fileName = ""){
        // Obtener una referencia
        $obj = ConfigurationManager::
                GetInstance($fileName);
        // Obtener la lista de referencias
        return $obj->References;
    }

    /**
     * Cargar todas las referencias configuradas
     * @var string $fileName Ruta del fichero de configuración
     */
    public static function LoadReferences($fileName = ""){
        // Obtener una referencia
        $obj = ConfigurationManager
                ::GetInstance($fileName);
        $references = $obj->References;
        //foreach($references as $key => $value){
        foreach($references as $value){
            // include_once($value);
            require_once($value);
        }
    }

    /**
     * Obtiene la instancia actual de ConfigurationManager
     * @param string $configFile Ruta del fichero de configuración
     * @return \ConfigurationManager
     */
    public static function GetInstance($configFile = ""){
        // Comprobar si hay una referencia actual al
        // gestor de configuraciones
        if(ConfigurationManager::$_configuration == null){
            ConfigurationManager::$_configuration
                    = new \ConfigurationManager( $configFile );
        }
        // Asignar el nombre del fichero si no está definido
        if(ConfigurationManager::$_filename == null
                && $configFile != "" && $configFile != null){
                ConfigurationManager::$_filename = $configFile;
        }
        // Retornar referencia al fichero actual
        return ConfigurationManager::$_configuration;
    }

}
