<?php



/*
    Dependencias :
    - Clase Injector para la inyección de componentes
    - Componentes definidos : [ ILogManager ]
    - Clase ConfigurationManager para el acceso al config.xml
    - Claves de config.xm : [ path ]
*/

/**
 * Clase base para los controladores
 *
 * @author alfonso
 */
class Controller{

    /**
     * Array de métodos privados para filtrar al obtener el ActionName
     * @var array
     */
    protected $_PrivateMethods = array( "PartialView", "GetActionName" );

    /**
     * Expresión patrón para la búsqueda de subcadenas
     * @var string $Pattern Patron para buscar reemplazos en la vista
     */
    protected $Pattern = "<!--NAME-->";

    /**
     * Nombre de la clase
     * @var string $ClassName Nombre del controlador
     */
    protected $ClassName = "Controller";

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager Gestor de trazas
     */
    protected $Log = null;

    /**
     * Referencia al gestor de inyecciones
     * @var Injector Referencia al gestor de dependencias
     */
    protected $Injector = null;

    /**
     * Constructor de la clase base
     */
    public function __construct(){
        // Obtener referencia al objeto inyector de dependencias
        $this->Injector = Injector::GetInstance();
        // Obtener referencia al gestor de trazas
        $this->Log = $this->Injector->Resolve( "ILogManager" );
        // Asignar nombre actual de la clase
        $this->ClassName = get_class($this);
    }

    /**
     * Buscar la subcadena patrón
     * @param string $sName Nombre de la propiedad que se desea buscar
     * @param string $content Contenido por el que se reemplaza
     * @return string
     */
    private function FindPattern($sName = "", $content = ""){
        $result = "";
        // Obtener la expresión a buscar
        $name = str_replace("NAME", $sName, $this->Pattern);
        // Buscamos la primera aparición de la subcadena $name en $content
        $start = strpos( $content , $name );
        // Comprobar Si se ha encontrado la posición inicial
        if($start === false){
            return $result;
        }
        // Buscamos si hay una segunda aparición
        $end = strpos( $content , $name , ($start + 1));
        // Comprobar Si se ha encontrado la posición final
        if($end === false){
            return $result;
        }
        // Extraer la subcadena del patrón
        return substr( $content , $start , ($end - $start));
    }

    /**
     * Obtiene el nombre de la acción que se está ejecutando
     */
    private function GetActionName(){
        // Obtener stacktrace
        $trace = debug_backtrace();
        // Buscar el nombre de la función actual
        foreach($trace as $method){
            // Obtener el nombre de la acción actual
            $function = $method["function"];
            // Obtener el nombre de la clase actual
            $class = $method["class"] == $this->ClassName;
            // Evaluar si la acción | método en la pila es una acción
            //  o un método privado
            $action = !in_array($function, $this->_PrivateMethods);
            // Validación de los datos
            if($class && $action) {
                return $function;
            }
        }
        return "";
    }

    /**
     * Elimina los tags de reemplazos de la plantilla
     * @param string $propertyName Nombre de la propiedad
     * @param string $item Patrón
     * @return string contenido procesado
     */
    private function ClearPatternSubrArray($propertyName="", $item=""){
        $pattern = $this->FindPattern($propertyName, $item);
        $sItem = str_replace($pattern, "", $item);
        return str_replace("<!--$propertyName-->", "", $sItem);
    }

    /**
     * Reemplazo de un array contenido en otro
     * @param string $view Patrón de reemplazo
     * @param string $name Nombre de la propiedad
     * @param object $array Referencia a los datos de reemplazo
     * @return string Texto reemplazado
     */
    private function ReplaceSubArray($view="", $name="", $array = null){
        if(is_object($array)){
            settype($array, "array");
        }
        $temp = ""; $sView = "";
        // Obtener subpatron
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

        // Obtener la expresión a buscar
        $tag = str_replace("NAME", $name, $this->Pattern);

        return str_replace($tag, "", $sView);
    }

    /**
     * Genera el remplazo sobre un patrón
     * @param string $item Patrón de reemplazo
     * @param string $propertyName Nombre de la propiedad
     * @param object $propertyValue Referencia a la info de reemplazo
     * @return string Contenido reemplazado
     */
    private function ReplaceItem($item="", $propertyName="",
            $propertyValue = null){
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

    /**
     * Genera el remplazo de elementos contenidos en un tipo array
     * @param string $sView Nombre de la vista
     * @param string $name Nombre de la propiedad
     * @param array $array de Items a reemplazar
     * @return string Vista renderizada
     */
    protected function ReplaceArray($sView="", $name="", $array=null){
        // Buscar el patrón
        $match = $this->FindPattern($name, $sView);
        // Contenido a reemplazar por el patrón
        $toReplace = "";
        // Generación de la vista a reemplazar
        foreach($array as $object){
            settype($object, "array");
            $item = $match;
            foreach($object as $propertyName => $propertyValue){
                $item = $this->ReplaceItem($item,
                        $propertyName, $propertyValue);
            }
            $toReplace .= $item;
        }
        // Reemplazar contenido en la vista
        $view = str_replace($match, $toReplace, $sView);
        // Reemplazar tags de expresión regular
        return str_replace("<!--$name-->", "", $view);
    }

    /**
     * Genera el remplazo de elementos contenidos en un objeto
     * @param string $view Nombre de la vista
     * @param string $name Nombre de la propiedad
     * @param object $object Referencia al objeto a reemplazar
     * @return string Vista renderizada
     */
    protected function ReplaceObject($view="",
            $name="", $object=null){
        // Convertir el objeto a array
        if(!is_array($object)){
            settype($object, "array");
        }
        // Recorrer las propiedades del objeto para ser reemplazadas
        foreach($object as $key => $value){
            // Si la propiedad es array u objeto, pasamos a la siguiente,
            // se ingnora
            if(is_array($value) || is_object($value)){
                continue;
            }
            // Reemplazar en el contenido a generar la etiqueta
            // correspondiente por el valor de la propiedad
            $view = str_replace("{".$name.".".$key."}", $value, $view);
        }
        // Retornar la vista generada
        return $view;
    }

    /**
     * Obtiene la vista parcial más el layout si necesita
     * @var string $filepath Ruta a la vista
     * @return string Vista renderizada
     */
    protected function GetViewContent($filepath = ""){
        // Obtiene el contenido del fichero de vista
        $fileContent = file_get_contents($filepath);
        // Obtener layout si es necesario
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

    /**
     * Genera la vista sin objeto modelo
     * @var string $view Nombre de la vista
     * @var \Model $model Referencia al modelo de datos
     * @return string Vista renderizada
     */
    protected function ProcessView($view="", $model=null){
        // Convertir el objeto modelo a arrya
        if(is_object($model)){
            settype($model, "array");
        }
        // Recorrer las propiedades del modelo para generar los reemplazos
        foreach($model as $propertyName => $propertyValue){
            if(is_array($propertyValue)){
                $view = $this->ReplaceArray($view,
                        $propertyName, $propertyValue);
            }
            elseif(is_object($propertyValue)){
                $view = $this->ReplaceObject($view,
                        $propertyName, $propertyValue);
            }
            else{
                $view = str_replace("{".$propertyName."}",
                        $propertyValue, $view);
            }
        }
        // Retornar la vista
        return $view;
    }

    /**
     * Procesa el contenido de la vista sin objeto modelo
     * @var string $filepath Ruta física a la vista
     * @return string Vista renderizada
     * @throws ResourceNotFount
     */
    protected function Render($filepath = ""){
        if($filepath == "" || !file_exists ($filepath)){
            throw new ResourceNotFoundException("file name :".$filepath);
        }
        return $this->GetViewContent($filepath);
    }

    /**
     * Procesa el contenido de la vista con objeto modelo
     * @var string $filepath Ruta física a la vista
     * @var \Model $model Referencia al modelo a renderizar
     * @return string Vista renderizada
     * @throws ResourceNotFount
     */
    protected function RenderView($filepath="", $model=null){
        if($filepath == "" || !file_exists ($filepath)){
            throw new ResourceNotFoundException("file name :".$filepath);
        }

        $view = $this->GetViewContent($filepath);

        return $this->ProcessView($view, $model);
    }

    /**
     * Procesar la vista con o sin modelo
     * @var \Model $model Referencia al modelo a renderizar
     * @return string Vista renderizada
     */
    public function PartialView($model = null){
        // Obtener el nombre de la acción actual
        $actionName = $this->GetActionName().".html";
        // Obtener el nombre del controlador actual
        $className = str_replace("Controller", "", get_class($this));
        // Construir el path para la vista
        $filePath = "view/".$className."/".$actionName;
        // Validar la referencia al modelo
        return ($model == null)
            ? $this->Render($filePath)
            : $this->RenderView($filePath, $model);
    }

    /**
     * Procesar la vista parametrizando el nombre de la vista
     * @var string $viewName Nombre de la vista
     * @var \Model $model Referencia al modelo a renderizar
     * @return string Vista renderizada
     */
    public function Partial($viewName = "", $model = null){
        // Construir el nombre de la vista
        $actionName = $viewName.".html";
        // Obtener el nombre del controlador
        $className = str_replace("Controller", "", get_class($this));
        // Construir el path de acceso a la vista
        $filePath = "view/".$className."/".$actionName;
        // Validar la referencia al modelo
        return ($model == null)
            ? $this->Render($filePath)
            : $this->RenderView($filePath, $model);
    }

    /**
     * Completa la referencia a la entidad con los parámetros de la
     * solicitud http
     * @param object $entity Referencia a la entidad
     * @param array $array Mapeado de campos
     * @return object Entidad completada
     */
    private function ReadEntityFromRequest($entity = null,
            $array = null){
        if($entity != null && $array != null){
            // los valores en los parámetros de la llamada
            foreach( $array as $key => $value){
                if(isset($_REQUEST[$key])){

                    $item = $_REQUEST[$key];
                    // Eliminamos posibles tags html y php.
                    $value = strip_tags($item);
                    // Asignar parámetro
                    $entity->{ $key } = $value;
                }
            }
        }
        return $entity;
    }

    /**
     * Obtener una entidad con los parámetros de la petición http
     * @param string $entityName Nombre de la entidad
     * @return object Entidad obtenida
     */
    public function GetEntity($entityName = ""){
        // Validar el nombre de la entidad
        if($entityName == "" ){
            return null;
        }
        // Instanciar objeto temporal para la lectura
        $temp = new $entityName();
        // Instanciar objeto a devolver
        $entity = new $entityName();
        // Convertir el temporar el array para
        // recorrer sus propiedades
        settype( $temp , "array" );
        // Completar los datos de entidad
        return $this->ReadEntityFromRequest($entity, $temp);
    }

    /**
     * Procesar la redirección de la llamada
     * @param string $action Acción solicitada
     * @param string $controller Controlador a ejecutar
     * @param array $args Argumentos de la llamada
     * @return string
     */
    public function RedirectTo($action = "",
            $controller = "", $args = null){
        // Obtener el path de ejecución
        $path = ConfigurationManager::GetKey( "path" );
        // Construir la url
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
        // Url
        $url = (strlen($params) > 0) ? $url."?".$params : $url;
        // Contenido a renderizar
        return "<script type='text/javascript'>"
            . "window.location=\"".$url."\"</script>";
    }

    /**
     * Configura la peticion http actual y serializa el objeto para
     * generar un response de tipo json
     * @param object $obj Referencia al objeto a serializar
     * @return string
     */
    public function ReturnJSON($obj = null){
        $returnValue = "[]";
        header('Content-Type: application/json');
        if($obj != null){
            $returnValue = json_encode($obj);
        }
        return $returnValue;
    }
}
