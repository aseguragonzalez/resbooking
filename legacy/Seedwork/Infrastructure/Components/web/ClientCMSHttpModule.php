<?php



/*
    Dependencias :
    - Clase base HttpModule y todas sus dependencias
    - Interfaz IHttpModule
    - Componentes definidos : [ IDataAccessObject ]
    - Claves de config.xml : [ connectionString, urlReferer ]
    - Entidad de base de datos : Service, Project
*/

/**
 * Implementación de la interfaz IHttpModule
 */
class ClientCMSHttpModule extends \HttpModule implements \IHttpModule{

    /**
     * Obtiene el path del proyecto actual
     * Formato del path : ../project/service/
     */
    protected function GetProjectPath(){
        // Obtener ruta actual del fichero para determinar el proyecto
        $spath = getcwd();
        // Buscamos la posición del último directorio
        $ppos = strrpos ( $spath , "/" );
        // Si no encontramos el caracter error
        if($ppos === false ){
            throw new UrlException( "GetProjectPath - ".$spath );
        }
        // Extraemos el último directorio => el servicio
        $serviceName = substr( $spath, $ppos);
        // Nos quedamos sólo con la ruta de proyecto
        $path = str_replace($serviceName, "", $spath);

        $pos = strrpos ( $path , "/" );
        // extraemos la ruta exclusiva del proyecto
        $path = substr( $path, $pos + 1);
        // Retornar el path de ejecución
        return $path;
    }

    /**
     * Obtiene el nombre del servicio actual a partir de la ruta de ejecución
     * Formato del path : ../project/service/
     */
    protected function GetServiceName(){
        // Obtener ruta actual
        $spath = getcwd();
        // Buscamos la posición del último directorio
        $pos = strrpos ( $spath , "/" );
        // Si no encontramos el caracter error
        if( $pos === false ){
            throw new UrlException( "GetServiceName - ".$spath );
        }
        // Extraemos el último directorio
        $path = substr( $path, $pos);

        $name = str_replace( "/" , "" , $path);

        return $name;
    }


    /**
     * Configurar los datos del proyecto actual a partir del path
     * @param string Ruta del proyecto
     * @throws UrlException
     */
    protected function SetProjectData($path = ""){
        // Buscar datos de proyecto
        $projects = $this->Dao->GetByFilter( "Project",
                array( "Path" => $path ));
        // Comprobar si hay resultados
        if(count($projects) == 0) {
            throw new UrlException( "SetProjectData - ".$path );
        }
        // Almacenar en sesión los datos del primer proyecto
        $_SESSION["projectId"] = $projects[0]->Id;
        $_SESSION["projectName"] = $projects[0]->Name;
        $_SESSION["projectPath"] = $projects[0]->Path;
    }

    /**
     * Configurar los datos del servicio actual a partir del nombre
     * @param string Nombre del servicio activo
     * @throws UrlException
     */
    protected function SetServiceData( $name = "" ){
        // Buscar datos del servicio
        $services = $this->Dao->GetByFilter( "Service",
                array ( "Path" => $name ));
        // Comprobar si hay resultados
        if(count($services) == 0) {
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
        $this->Dao = $this->Injector->Resolve("IDataAccessObject");
        // Obtener la clave de cadena de conexión
        $connectionString =
                ConfigurationManager::GetKey("connectionString");
        // Obtener los parámetros de conexión a bbdd
        $oConnString =
                ConfigurationManager::GetConnectionStr($connectionString);
        // Configurar Objeto de acceso a datos
        $this->Dao->Configure( $oConnString );
    }

    /**
     * Se encarga de realizar las tareas comunes a cualquier petición de
     * cliente como generar una traza, comprobar si existe sesión...
     */
    public function BeginRequest(){

        $path = $this->GetProjectPath();
        // Setear datos del proyecto
        $this->SetProjectData($path);
        // Obtener el nombre del servicio actual
        $name = $this->GetServiceName();
        // Setear los datos del servicio activo
        $this->SetServiceData( $name );
    }

    /**
     * Se encarga de realizar el procesado de la petición. Para ello debe
     * hacer uso de las diferentes clases con las que se constituye el
     * proyecto como por ejemplo el manejador de peticiones IHttpHandler.
     */
    public function ProcessRequest(){
        // Obtener los datos de la petición
        $urlData = $this->HttpHandler->ProcessUrl($_SERVER['REQUEST_URI']);
        // Ejecutar controlador y acción
        if(!isset($urlData["First"])
                || $urlData["First"] == null
                || $urlData["First"] == ""){
            $this->Render .= $this->HttpHandler->Run(
                        $urlData["Controller"],
                        $urlData["Action"],
                        $urlData["Params"]
                    );
        }
        else{
            $this->Render.= $this->HttpHandler->Run(
                        $urlData["Controller"]."/".$urlData["Action"],
                        $urlData["First"],
                        $urlData["Params"]
                    );
        }
    }

    /**
     * Se encarga de realizar las tareas comunes previas a la finalización
     * del procesado de la petición como puede ser la generación de trazas.
     */
    public function EndRequest(){
        // Obtener la ruta temporal de los recursos
        $respath = ConfigurationManager::GetKey( "resourcePath" );
        // Reemplazar nombre de proyecto
        $path = str_replace("/{Project}",$_SESSION["projectPath"],$respath);
        // Eliminar rutas básicas
        $htmlToRender = str_replace( $path, "", $this->Render);
        // print html
        print $htmlToRender;
    }

}
