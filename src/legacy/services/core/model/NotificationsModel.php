<?php

///<summary>
/// Model para la configuración de notificaciones
///</summary>
class NotificationsModel extends \CoreModel{

    ///<summary>
    /// Título del formulario
    ///</summary>
    public $Title = "Notificaciones";

    ///<summary>
    /// Parámetro para activar el menú
    ///</summary>
    public $MenuActivo = "Notifications";

    ///<summary>
    /// Referencia a la entidad Role
    ///</summary>
    public $Entity = null;

    ///<summary>
    /// Colección de Role disponibles
    ///</summary>
    public $Entities = array();

    ///<summary>
    /// Colección de Servicios disponibles y asociados
    ///</summary>
    public $Services = array();

    ///<summary>
    /// Colección de proyectos disponibles
    ///</summary>
    public $Projects = array();

    ///<summary>
    /// Colección de tipologías de mensajes
    ///</summary>
    public $Types = array();

    ///<summary>
    /// Propiedades gestión de errores
    ///</summary>
    public $eSubject = "";
    public $eSubjectClass = "";
    public $eProject = "";
    public $eProjectClass = "";
    public $eService = "";
    public $eServiceClass = "";
    public $eText = "";
    public $eTextClass = "";
    public $eFrom = "";
    public $eFromClass = "";
    public $eTo = "";
    public $eToClass = "";
    public $eTemplate = "";
    public $eTemplateClass = "";
    public $eResult= "";
    public $eResultClass= "has-success";

    ///<summary>
    /// Carga Tipologías de mensajes
    ///</summary>
    private function LoadTypes(){
        $this->Types = $this->Dao->Get( "NotificationType" );
    }

    ///<summary>
    /// Carga la información de los proyectos
    ///</summary>
    private function LoadProjects(){
        $this->Projects = $this->Dao->GetByFilter( "Project", ["Active" => 1]);
    }

    ///<summary>
    /// Carga la información de los servicios
    ///</summary>
    private function LoadServices(){
        $this->Services = $this->Dao->GetByFilter( "Service" , ["Active" => 1]);
    }

    ///<summary>
    /// Obtiene la información de la entidad buscada por Id
    ///</summary>
    private function Read($id = 0){
        $this->Entity = $this->Dao->Read( $id, "NotificationConfig");
    }

    ///<summary>
    /// Proceso de validación de la entidad
    ///</summary>
    private function Validate(){
        // Resultado por defecto
        $result = true;
        // Referenciar la entidad
        $o = $this->Entity;

        // Validar el asunto
        if(!isset($o->Subject) || $o->Subject == "" ){
            $this->eSubject = "El campo palabra clave es obligatorio.";
            $this->eSubjectClass = "has-error";
            $result = false;
        }
        elseif(strlen($o->Subject) > 100){
            $this->eSubject = "La longitud de la palabra clave no "
                    . "puede ser superior a 100 catacteres.";
            $this->eSubjectClass = "has-error";
            $result = false;
        }

        // Validar el proyecto
        if(!isset($o->Project) || !is_numeric($o->Project)){
                $this->eProject = "Debe seleccionar un proyecto.";
                $this->eProjectClass = "has-error";
                $result = false;
        }
        elseif(intval($o->Project) <= 0){
                $this->eProject = "El proyecto seleccionado no es válido.";
                $this->eProjectClass = "has-error";
                $result = false;
        }

        // Validar el servicio
        if(!isset($o->Service) || !is_numeric($o->Service)){
                $this->eService = "Debe seleccionar un servicio.";
                $this->eServiceClass = "has-error";
                $result = false;
        }
        elseif(intval($o->Service) <= 0){
                $this->eService = "El servicio seleccionado no es válido.";
                $this->eServiceClass = "has-error";
                $result = false;
        }

        // Validar el texto del asunto
        if(!isset($o->Text) || $o->Text == "" ){
            $this->eText = "El campo asunto es obligatorio.";
            $this->eTextClass = "has-error";
            $result = false;
        }
        elseif(strlen($o->Text) > 200){
            $this->eText = "La longitud del asunto no puede ser "
                    . "superior a 200 catacteres.";
            $this->eTextClass = "has-error";
            $result = false;
        }

        // Validar el texto del asunto
        if(!isset($o->From) || $o->From == "" ){
            $this->eFrom = "El campo remitente es obligatorio.";
            $this->eFromClass = "has-error";
            $result = false;
        }
        elseif(strlen($o->From) > 100){
            $this->eFrom = "La longitud del remitente no puede ser "
                    . "superior a 100 catacteres.";
            $this->eFromClass = "has-error";
            $result = false;
        }

        // Validar el texto del asunto
        if(!isset($o->To) || $o->To == "" ){
            $this->eTo = "El campo destinatario es obligatorio.";
            $this->eToClass = "has-error";
            $result = false;
        }
        elseif(strlen($o->To) > 200){
            $this->eTo = "La longitud del destinatario no puede ser "
                    . "superior a 100 catacteres.";
            $this->eToClass = "has-error";
            $result = false;
        }

        // Validar el texto del asunto
        if(!isset($o->Template) || $o->Template == "" ){
            $this->eTemplate = "No se ha definido el contenido de la plantilla";
            $this->eTemplateClass = "has-error";
            $result = false;
        }

        if(!$result){
            $this->eResult = "No se ha validado el formulario correctamente.";
            $this->eResultClass = "has-error";
        }

        // Retornar el resultado de la operación
        return $result;
    }

    ///<summary>
    /// Constructor
    ///</summary>
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

    ///<summary>
    /// Cargar configuración de notificaciones
    ///</summary>
    public function LoadConfigs(){
        // Obtener todos los registros de configuraciones
        $this->Entities =
                $this->Dao->GetByFilter( "NotificationConfigDTO" ,
                    ["State" => 1]);

        foreach($this->Entities as $item){
            $text = $item->Text;
            $item->SubjectT = $item->Subject;
            $item->ProjectNameT = $item->ProjectName;
            $item->ServiceNameT = $item->ServiceName;
            $item->TextT = $text;
            $item->FromT = $item->From;
            $item->ToT = $item->To;
            $item->Subject = (strlen($item->Subject) < 15) ?
                    $item->Subject : substr($item->Subject ,0,12).".." ;
            /*
            $item->ProjectName = (strlen($item->ProjectName) < 15) ?
                    $item->ProjectName : substr($item->ProjectName ,0,12)."..";
            */
            $item->ServiceName = (strlen($item->ServiceName) < 15) ?
                    $item->ServiceName : substr($item->ServiceName ,0,12)."..";
            $item->Text = (strlen($text) < 15) ?
                    $item->Text : substr($text ,0,12)."..";
            $item->From = (strlen($item->From) < 15) ?
                    $item->From : substr($item->From ,0,12)."..";
            $item->To = (strlen($item->To) < 15) ?
                    $item->To : substr($item->To ,0,12)."..";
        }
    }

    ///<summary>
    /// Cargar Toda la información de una configuración por su id
    ///</summary>
    public function LoadFormData($id = 0, $state = false){
        // Obtiene toda la información de proyectos registrados y activos
        $this->LoadProjects();
        // Obtiene toda la información de los servicios registrados y activos
        $this->LoadServices();
        // Cargar las tipologías
        $this->LoadTypes();
        // Cargar datos de la entidad si procede
        if($id > 0){
            $this->Read($id);
        }
        // Comprobamos si hay estado anterior
        if($state){
            $this->eResult = "La operación se realizó correctamente.";
        }
    }

    ///<summary>
    /// Realiza el borrado lógico de la
    ///</summary>
    public function Delete($id = 0){
        // Resultado por defecto de la operación
        $result = false;
        // Buscamos la entidad por id
        $entities = $this->Dao->GetByFilter("NotificationConfig",
            ["Id" => $id ]);
        // Comprobamos si existe
        if(count($entities) == 1){
            // Referenciar entidad
            $entity = $entities[0];
            // Establecemos el estado lógico
            $entity->State = 0;
            $entity->_From = $entity->From;
            $entity->_To = $entity->To;
            // Actualizamos la entidad en bbdd
            $this->Dao->Update($entity);
            // Actualizar el resultado de la operación
            $result = true;
        }
        // Retornar el resultado
        return $result;
    }

    ///<summary>
    /// Proceso de guardado de la configuración de notificación
    ///</summary>
    public function Save($entity = null){
        // Resultado por defecto de la operación
        $result = false;
        // Asignar la entidad
        $this->Entity = $entity;
        // Validamos los datos de la entidad
        if($this->Validate()){
            $entity->_From = $entity->From;
            $entity->_To = $entity->To;
            // almacenamos la información
            if($entity->Id == 0){
                $this->Entity->Id = $this->Dao->Create($entity);
            }
            else{
                $this->Dao->Update($entity);
            }
            // Confirmar el resultado
            $result = true;
            // Mensaje de operacion
            $this->eResult = "Los cambios han sido guardados correctamente.";
        }
        // Retornar el resultado final
        return $result;
    }

}
