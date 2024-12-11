<?php

///<summary>
/// Model para la gestión de Service
///</summary>
class ServiceModel extends \CoreModel{

    ///<summary>
    /// Parámetro para activar el menú
    ///</summary>
    public $MenuActivo = "Service";

    ///<summary>
    /// Referencia a la entidad Service
    ///</summary>
    public $Entity = null;

    ///<summary>
    /// Colección de Service disponibles
    ///</summary>
    public $Entities = array();

    ///<summary>
    /// Colección de Proyectos disponibles
    ///</summary>
    public $Projects = array();

    ///<summary>
    /// Colección de Roles disponibles
    ///</summary>
    public $Roles = array();

    ///<summary>
    /// Propiedades para la validación del servicio
    ///</summary>
    public $eName = "";
    public $eNameClass = "";
    public $ePath = "";
    public $ePathClass = "";
    public $ePlatform = "";
    public $ePlatformClass = "";
    public $eDesc = "";
    public $eDescClass = "";
    public $eResult = "";
    public $eResultClass = "has-success";

    ///<summary>
    /// Método que obtiene el Service filtrado por su identidad
    ///</summary>
    private function Read($id = 0){
            // Filtro de búsqueda
            $filter = array( "Id" => $id, "Active" => 1);
            // Buscar por id el servicio activo
            $entities = $this->Dao->GetByFilter( "Service", $filter );
            // Validación de la entidad buscada
            if(count($entities) == 1){
                    $this->Entity = $entities[0];
                    return $entities[0];
            }
            // Si hay errores..
            throw new Exception ( "Item not found - id : ".$id );
    }

    ///<summary>
    /// Proceso de validación del servicio
    ///</summary>
    private function Validate(){
            // Resultado por defecto
            $result = true;
            // Referenciar entidad
            $o = $this->Entity;

            // Validación del nombre
            if(!isset($o->Name) || $o->Name == ""){
                    $this->eName = "El campo Nombre es obligatorio.";
                    $this->eNameClass = "has-error";
                    $result = false;
            }
            elseif(strlen($o->Name) > 50){
                    $this->eName = "La longitud del nombre no puede ser superior a 50 caracteres.";
                    $this->eNameClass = "has-error";
                    $result = false;
            }
            else{
                    // Filtro de búsqueda
                    $filter = array( "Name" => $o->Name , "Active" => 1);
                    // Buscamos entidades con el mismo nombre
                    $entities = $this->Dao->GetByFilter( "Service", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->eName = "El nombre no es válido. Ya existe un servicio con el mismo nombre";
                            $this->eNameClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->eName = "El nombre no es válido. Ya existe un servicio con el mismo nombre";
                            $this->eNameClass = "has-error";
                            $result = false;
                    }
            }

            // Validación del Path
            if(strlen($o->Path) > 50){
                    $this->ePath = "La longitud del Path no puede ser superior a 50 caracteres.";
                    $this->ePathClass = "has-error";
                    $result = false;
            }
/*
            else{
                    // Filtro de búsqueda
                    $filter = array( "Path" => $o->Path , "Active" => 1);
                    // Buscamos entidades con el mismo nombre
                    $entities = $this->Dao->GetByFilter( "Service", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->ePath = "El Path no es válido. Ya existe un servicio con el mismo Path";
                            $this->ePathClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->eName = "El Path no es válido. Ya existe un servicio con el mismo Path";
                            $this->eNameClass = "has-error";
                            $result = false;
                    }
            }
*/

            // Validación de la plataforma
            if(strlen($o->Platform) > 200){
                    $this->ePlatform = "La longitud de la url no puede ser superior a 200 caracteres.";
                    $this->ePlatformClass = "has-error";
                    $result = false;
            }
            elseif($o->Platform != ""){
                    // Filtro de búsqueda
                    $filter = array( "Platform" => $o->Platform );
                    // Buscamos entidades con el mismo nombre
                    $entities = $this->Dao->GetByFilter( "Service", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->ePlatform = "El Plataforma no es válida. Ya existe un servicio con la misma Plataforma";
                            $this->ePlatformClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->ePlatform = "El Plataforma no es válida. Ya existe un servicio con la misma Plataforma";
                            $this->ePlatformClass = "has-error";
                            $result = false;
                    }
            }

            // Validación de la Descripción
            if(!isset($o->Description) || $o->Description == ""){
                    $this->eDesc = "El campo Descripción es obligatorio.";
                    $this->eDescClass = "has-error";
                    $result = false;
            }
            elseif(strlen($o->Description) > 500){
                    $this->eDesc = "La longitud de la Descripción no puede ser superior a 500 caracteres.";
                    $this->eDescClass = "has-error";
                    $result = false;
            }

            // Actualizar el mensaje final si corresponde
            if(!$result){
                    $this->eResult = "No se ha validado el formulario correctamente.";
                    $this->eResultClass = "has-error";
            }

            // retornar el resultado
            return $result;
    }

    ///<summary>
    /// Constructor por defecto
    ///</summary>
    public function __construct(){
            parent::__construct();
    }

    ///<summary>
    /// Método que obtiene la lista de Service
    ///</summary>
    public function LoadServices(){
            $this->Entities = $this->Dao->GetByfilter( "Service" , array( "Active" => 1));
            foreach($this->Entities as $item){
                    $item->Title = $item->Description;
                    if(strlen($item->Description) > 30)
                            $item->Description = substr($item->Description,0,27)."..";
            }
    }

    ///<summary>
    /// Establece los parámetros del formulario para cuando hay un error de tipo ajax
    ///</summary>
    public function SetAjaxError($msg = ""){
            $this->Entity = new Service();
            $this->eResult = "Se ha producido un error interno. Detalles - ".$msg;
            $this->eResultClass = "has-error";
    }

    ///<summary>
    /// Método que carga las dependencias del formulario de Service
    ///</summary>
    public function LoadFormData($id = 0){
            // Cargar la información de la entidad
            $this->Read($id);
            // Obtener la lista de roles disponibles
            $this->Roles = $this->Dao->GetByFilter( "Role", array( "Active" => 1));
            // Obtener todas las posibles relaciones entre Servicios y el role
            $result = $this->Dao->GetByFilter( "ServiceRole" , array( "IdService" => $this->Entity->Id ));
            // Serializamos las asociaciones de servicios
            $this->Relations = json_encode($result);
    }

    ///<summary>
    /// Método que guarda la información relativa a la entidad
    ///</summary>
    public function Save($entity = null){
            // Resultado por defecto
            $result = false;
            // Referenciar la entidad
            $this->Entity = $entity;
            // Validación del formulario
            if($this->Validate()){
                    if($entity->Id == 0)
                            $this->Entity->Id = $this->Dao->Create($entity);
                    else
                            $this->Dao->Update($entity);
                    // Confirmar el resultado
                    $result = true;
                    // Mensaje de operacion
                    $this->eResult = "Los cambios han sido guardados correctamente.";
            }
            return $result;
    }

    ///<summary>
    /// Método que elimina la entidad identificada por su id
    ///</summary>
    public function Delete($id = 0){
            // Obtener la entidad de base de datos
            $entity = $this->Read($id);
            // Establecer el estado lógico
            $entity->Active = 0;
            // Actualizar la entidad
            $this->Dao->Update($entity);

            // Obtener todas las posibles relaciones entre Servicio, role y usuario
            $result = $this->Dao->GetByFilter( "UserRoleService" , array( "IdService" => $id ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "UserRoleService" );

            // Obtener todas las posibles relaciones entre Servicio y role
            $result = $this->Dao->GetByFilter( "ServiceRole" , array( "IdService" => $id ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ServiceRole" );

            // Obtener todas las posibles relaciones entre Servicio y projecto
            $result = $this->Dao->GetByFilter( "ProjectServices" , array( "IdService" => $id ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectServices" );
    }

    ///<summary>
    /// Método que crea una asociación entre un servicio y un proyecto
    ///</summary>
    public function AddProject($entity){
            // Persistir
            $this->Dao->Create($entity);
            // Obtener los datos del servicio
            $service = $this->Dao->Read($entity->IdService, "Service" );
            // Obtener los datos del proyecto
            $project = $this->Dao->Read($entity->IdProject, "Project" );
            // validar paths
            if($service->Path != "" && $project->Path != ""){
                    // Obtener path cliente
                    $clientPath = ConfigurationManager::GetKey( "clientPath" );
                    $clientPath = str_replace( "{Client}", $service->Path, $clientPath);
                    $clientPath = str_replace( "//", "/", $clientPath);
                    // Obtener path proyecto
                    $projectPath = ConfigurationManager::GetKey( "projectPath" );
                    $projectPath = str_replace( "{Client}", $service->Path, $projectPath);
                    $projectPath = str_replace( "{Project}", $project->Path, $projectPath);
                    $projectPath = str_replace( "//", "/", $projectPath);

                    // Comprobar si existe el path destino
                    if(file_exists($projectPath)==false){
                            FileManager::CopyDirectory( $clientPath, $projectPath );
                            $files = FileManager::GetFiles($clientPath); var_dump($files);
                    }
            }

            // retornar entidad
            return $entity;
    }

    ///<summary>
    /// Método que elimina la/s asociación/es entre un servicio y un proyecto
    ///</summary>
    public function RemoveProject($entity){
            // Obtener todas las posibles relaciones entre Servicio y projecto
            $result = $this->Dao->GetByFilter( "ProjectServices" , array( "IdProject" => $entity->IdProject, "IdService" => $entity->IdService ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectServices" );
    }

    ///<summary>
    /// Método que crea una asociación entre un servicio y un role
    ///</summary>
    public function AddRole($entity){
            // Persistir
            $this->Dao->Create($entity);
            // retornar entidad
            return $entity;
    }

    ///<summary>
    /// Método que elimina la/s asociación/es entre un servicio y un role
    ///</summary>
    public function RemoveRole($entity){
            // Obtener todas las posibles relaciones entre Servicio, rol y usuario
            $result = $this->Dao->GetByFilter( "UserRoleService" , array( "IdRole" => $entity->IdRole, "IdService" => $entity->IdService ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "UserRoleService" );

            // Obtener todas las posibles relaciones entre Servicio y rol
            $result = $this->Dao->GetByFilter( "ServiceRole" , array( "IdRole" => $entity->IdRole, "IdService" => $entity->IdService ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ServiceRole" );
    }


}
