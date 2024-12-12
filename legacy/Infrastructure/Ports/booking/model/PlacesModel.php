<?php

declare(strict_types=1);

/**
 * Model para la gestión de espacios/salas
 *
 * @author alfonso
 */
class PlacesModel extends \ResbookingModel{

    /**
     * Flag para indicar error en la última operación
     * @var int
     */
    public $Error = 0;

    /**
     * Indica la pestaña activa del menú principal
     * @var string
     */
    public $Activo = "Configuración";

    /**
     * Referencia a la entidad
     * @var \Place
     */
    public $Entity = null;

    /**
     * Array de Espacios registrados
     * @var array
     */
    public $Entities = [];

    /**
     * Mensaje de error para el campo Nombre
     * @var type
     */
    public $eName = "";

    /**
     * Clase CSS a utilizar en el mensaje de error del campo Nombre
     * @var type
     */
    public $eNameClass = "";

    /**
     * Mensaje de error para el campo Descripción
     * @var string
     */
    public $eDesc = "";

    /**
     * Clase CSS para el mensaje de error sobre la descripción
     * @var type
     */
    public $eDescClass = "";

    /**
     * Mensaje para el resultado de la operación actual
     * @var string
     */
    public $eResult = "";

    /**
     * Clase CSS a utilizar en el mensaje de resultado de operación
     * @var string
     */
    public $eResultClass = "has-success";

    /**
     * Mensaje para la vista principal sobre el último resultado
     * @var string
     */
    public $eGenResult = "";

    /**
     * Clase CSS utilizada en el mensaje de la vista principal
     * @var type
     */
    public $eGenResultClass = "";

    /**
     *
     * @var array
     */
    protected $Codes = [];

    /**
     * Códigos de error generados en la ejecución
     * @var array
     */
    protected $Codigos = [];

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
        // Título de la página
        $this->Title = "Configuración::Salas";
        // Iniciar la entidad
        $this->Entity = new \Place();
        // Iniciar códigos de error
        $this->SetCodes();
    }

    /**
     * Cargar la colección de espacios configurados
     */
    public function GetPlaces(){
        // filtro de búsqueda
        $filter = [ "Project" => $this->Project, "Active" => true ];
        // Obtener la lista de "Espacios" configurados
        $this->Entities = $this->Dao->GetByFilter( "Place", $filter );

        foreach($this->Entities as $place){
            $place->sName = $this->SetText($place->Name);
            $place->sDescription = $this->SetText($place->Description);
        }
    }

    /**
     * Acorta el texto en función de la longitud del mismo
     * @param string $comment Texto a recortar
     * @return string Texto
     */
    private function SetText($text  = "", $maxlength = 25){
        if(isset($text) && strlen($text) > $maxlength){
            return substr($text, 0, $maxlength-3 )."...";
        }
        return $text;
    }


    /**
     * Proceso de almacenado de entidad
     * @param \Place $place Referencia a la entidad Place a crear
     * @return boolean Resultado de la operación
     */
    public function Save($place = null){
        $this->Entity = $place;
        if($place != null && $this->Validate($place)){
            $place->Project = $this->Project;
            if($place->Id == 0) {
                $place->Id = $this->Dao->Create($place);
            }
            else{
                $this->Dao->Update($place);
            }
            $this->eGenResult = "La sala ha sido guardada con éxito.";
            $this->eGenResultClass = "alert-success";
            return true;
        }
        $this->TranslateResultCodes();
        $this->Error = 1;
        return false;
    }

    /**
     * Eliminar el espacio registrado (Borrado lógico)
     * @param int $id Identidad del espacio a eliminar
     * @return boolean
     */
    public function Delete($id = 0){
        if(is_numeric($id)){
            $o = $this->Dao->Read($id, "Place");
            if($o->Active != false){
                $o->Active = false;
                $this->Dao->Update( $o );
                $this->eGenResult = "La sala ha sido eliminada con éxito.";
                $this->eGenResultClass = "alert-success";
                return true;
            }
        }
        $this->eGenResult = "La sala no ha podido ser eliminada.";
        $this->eGenResultClass = "alert-danger";
        return false;
    }

    /**
     * Proceso de validación del formulario
     * @param \Place $entity Referencia a la entidad a validar
     * @return boolean Resultado de la validación
     */
    public function Validate($entity = null){
        if($entity != null){
            $this->ValidateIdentity($entity->Id);
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateDescription($entity->Description);
        }
        else{
            $this->Codigos[] = -1;
        }
        return count($this->Codigos) == 0;
    }

    /**
     * Proceso de validación de la identidad de la sala.
     * @param int $id Identidad de la sala
     */
    private function ValidateIdentity($id = 0){
        if(!is_numeric($id)){
            $this->Codigos[] = -2;
        }
        elseif($id < 0){
            $this->Codigos[] = -3;
        }
    }

    /**
     * Proceso de validación del nombre de la sala.
     * @param int $id Identidad de la sala
     * @param string $name Nombre de la sala
     */
    private function ValidateName($id = 0, $name = ""){
        if(empty($name)){
            $this->Codigos[] = -4;
        }
        elseif(!is_string($name)){
            $this->Codigos[] = -5;
        }
        elseif(strlen($name) > 100){
            $this->Codigos[] = -6;
        }
        elseif($id == 0){
            $filtro =  [ "Project" => $this->Project,
                "Active" => true, "Name" => $name];
            $salas = $this->Dao->GetByFilter("Place", $filtro);
            if(count($salas)>0){
                $this->Codigos[] = -10;
            }
        }
        elseif($id > 0){
            $filtro =  [ "Project" => $this->Project,
                "Active" => true, "Name" => $name];
            $salas = $this->Dao->GetByFilter("Place", $filtro);
            $cuenta = count($salas);
            if($cuenta > 1 || ($cuenta == 1 && $salas[0]->Id != $id)){
                $this->Codigos[] = -10;
            }
        }
    }

    /**
     * Proceso de validación de la descripción.
     * @param string $description Descripción de la sala
     */
    private function ValidateDescription($description = ""){
        if(empty($description)){
            $this->Codigos[] = -7;
        }
        elseif(!is_string($description)){
            $this->Codigos[] = -8;
        }
        elseif( strlen($description) > 500 ){
            $this->Codigos[] = -9;
        }
    }

    /**
     * Establece el array de "traducción" de códigos de error
     * @return void
     */
    private function SetCodes(){
       $this->Codes = [
           0 => [ "name" => "eResult", "msg" => "La sala se ha guardado correctamente" ],
           -1 => [ "name" => "eResult", "msg" => "No se ha recuperado la sala" ],
           -2 => [ "name" => "eResult", "msg" => "El tipo de dato del Id no es correcto." ],
           -3 => [ "name" => "eResult", "msg" => "El Id no puede ser menor que 1" ],
           -4 => [ "name" => "eName", "msg" => "Debe especificar un nombre." ],
           -5 => [ "name" => "eName", "msg" => "El tipo de dato no es correcto." ],
           -6 => [ "name" => "eName", "msg" => "La longitud del nombre supera el "
               . "máximo de caracteres (100)." ],
           -7 => [ "name" => "eDesc", "msg" => "Debe especificar una descripción." ],
           -8 => [ "name" => "eDesc", "msg" => "La longitud de la descripción "
               . "debe ser menor que 500 caractéres" ] ,
           -9 => [ "name" => "eDesc", "msg" => "El tipo de dato no es correcto." ],
           -10 => [ "name" => "eName", "msg" => "El nombre de sala ya existe." ]
       ];
    }

    /**
     * Establece los mensajes de error a partir de los codigos obtenidos
     * en la operacion anterior.
     * @return void
     */
    private function TranslateResultCodes(){
        foreach ($this->Codigos as $code){
            if(!isset($this->Codes[$code])){
                continue;
            }
            $codeInfo = $this->Codes[$code];
            $class = ($code == 0) ? "has-success" : "has-error";
            $this->{$codeInfo["name"]} = $codeInfo["msg"];
            $this->{$codeInfo["name"]."Class"} = $class;
        }
    }

}
