<?php



define("_OP_CREATE_", 0);
define("_OP_READ_", 1);
define("_OP_UPDATE_", 2);
define("_OP_DELETE_", 3);

/**
 * Clase base para los modelos
 *
 * @author manager
 */
abstract class TakeawayModel extends \SaasModel{

    /**
     * Pestaña del menú activo
     * @var string
     */
    public $Activo = "";

    /**
     * Referencia a la página de tabla origen
     * @var int
     */
    public $Page = "";

    /**
     * Referencia al agregado
     * @var \BaseAggregate
     */
    public $Aggregate = null;

    /**
     * Referencia al gestor del agregado
     * @var \BaseManagement
     */
    protected $Management = null;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Codes = [];

    /**
     * Mensaje del resultado de la operación
     * @var String
     */
    public $eResult = "";

    /**
     * Clase CSS aplicada al mensaje de resultado
     * @var String
     */
    public $eResultClass = "";

    /**
     * Cadena aleatoria para forzar no cache en dependencias
     * @var string
     */
    public $Random = "";

    /**
     * Constructor de la clase
     * @param string $title Título para el formulario
     * @param string $activo Pestaña del menú activa
     * @param string $management Nombre del management a utilizar
     */
    public function __construct($title= "", $activo = "" ,$management = ""){
        parent::__construct();
        $this->Title = $title;
        $this->Activo = $activo;
        if($management != ""){
            $this->Management =
                    $management::GetInstance($this->Project, $this->Service);
            $this->aggregate = $this->Management->GetAggregate();
        }
        $this->SetResultCodes();
        $date = new \DateTime("NOW");
        $this->Random = $date->format("YmdHis");
    }

    /**
     * Establece los mensajes de error a partir de los codigos obtenidos
     * en la operacion anterior.
     * @param int $oper Identificador de la operación realizada
     * @param array $codes Coleccion de codigos de error obtenidos
     */
    protected function TranslateResultCodes($oper = 0, $codes = null){
       if($codes != null && is_array($codes)){
           foreach ($codes as $code){
               if(!isset($this->Codes[$oper][$code])){
                   continue;
               }
               $codeInfo = $this->Codes[$oper][$code];
               $class = ($code == 0) ? "has-success" : "has-error";
               $this->{$codeInfo["name"]} = $codeInfo["msg"];
               $this->{$codeInfo["name"]."Class"} = $class;
           }
       }
    }

    /**
     * Obtiene la colección de mensajes de error generados
     * @return array Mensajes de error generados
     */
    protected function GetResultMessage($oper = 0, $codes = null){
        $messages = [];
        if($codes != null && is_array($codes)){
           foreach ($codes as $code){
               if(!isset($this->Codes[$oper][$code])){
                   continue;
               }
               $codeInfo = $this->Codes[$oper][$code];
               $messages[$code] =  $codeInfo["msg"];
           }
       }
       elseif(is_numeric($codes)){
            if(isset($this->Codes[$oper][$codes])){
                $codeInfo = $this->Codes[$oper][$codes];
                $messages[$codes] =  $codeInfo["msg"];
            }
       }
       return $messages;
    }

    /**
     * Comprueba la longitud del texto pasado como argumento, recortando
     * si ésta es mayor que le parámetro de longitud máxima
     * @param string $text Texto original
     * @param int $maxLength Longitud máxima del texto
     * @return string
     */
    protected function GetCutText($text = "", $maxLength = 15){
        if(!empty($text) && strlen($text)> $maxLength){
            return substr($text, 0, $maxLength-3)."...";
        }
        return $text;
    }

    /**
     * Configuración de los códigos de operación
     */
    abstract protected function SetResultCodes();

    /**
     * Configuración de las propiedades del modelo
     */
    abstract protected function SetModel();

}
