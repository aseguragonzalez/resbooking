<?php

///<summary>
/// Model para la gestión de Project
///</summary>
class ProjectModel extends CoreModel{

    ///<summary>
    /// Parámetro para activar el menú
    ///</summary>
    public $MenuActivo = "Project";

    ///<summary>
    /// Referencia a la entidad Project
    ///</summary>
    public $Entity = null;

    ///<summary>
    /// Colección de Project disponibles
    ///</summary>
    public $Entities = array();

    ///<summary>
    /// Colección de Servicios disponibles
    ///</summary>
    public $Services = array();

    ///<summary>
    /// Colección de Servicios asociados
    ///</summary>
    public $jsonServices = "[]";

    ///<summary>
    /// Colección de Usuarios disponibles
    ///</summary>
    public $Users = array();

    ///<summary>
    /// Serialización de usuarios asociados
    ///</summary>
    public $jsonUsers = "[]";

    ///<summary>
    /// Colección de propiedades para la validación de formularios
    ///</summary>
    public $eName = "";
    public $eNameClass = "";
    public $eDesc = "";
    public $eDescClass = "";
    public $ePath = "";
    public $ePathClass = "";
    public $eDate = "";
    public $eDateClass = "";
    public $eResult = "";
    public $eResultClass = "has-success";

    ///<summary>
    /// Crea el directorio del proyecto indicado
    ///</summary>
    private function CreateProjectPath($project = null){
            // Validación de la referencia al proyecto
            if($project == null) return false;
            // Obtener path
            $path = ConfigurationManager::GetKey( "projectPath" );
            $path = str_replace( "{Client}", "", $path);
            $path = str_replace( "{Project}", $project->Path, $path);
            $path = str_replace( "//", "/", $path);
            if(file_exists ( $path ) == false){
                    @mkdir( $path, 0777 );
                    chmod( $path, 0777);
            }
            // Resultado de la operación
            return true;
    }

    ///<summary>
    /// Replicar el directorio cliente del servicio al path de proyecto
    ///</summary>
    private function SetServicePath($project = null, $service = null){
            // Validación de los parámetros
            if($project == null || $service == null) return false;

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
                            $files = FileManager::GetFiles($clientPath);
                    }
            }
            // Proceso finalizado correctamente
            return true;
    }

    ///<summary>
    /// Proceso de validación del formulario
    ///</summary>
    private function Validate(){
            // Valor por defecto de la validación
            $result = true;
            // Referenciar la entidad
            $o = $this->Entity;

            // Validación del nombre
            if(!isset($o->Name)  || $o->Name == ""){
                    $this->eName = "El campo nombre es obligatorio.";
                    $this->eNameClass = "has-error";
                    $result = false;
            }
            elseif(strlen($o->Name) > 50){
                    $this->eName = "La longitud del nombre no puede superar los 50 caracteres.";
                    $this->eNameClass = "has-error";
                    $result = false;
            }
            // Validación si el nombre ya está registrado
            else{
                    // Filtro de búsqueda
                    $filter = array( "Name" => $o->Name );
                    // Buscamos entidades con el mismo nombre
                    $entities = $this->Dao->GetByFilter( "Project", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->eName = "El nombre no es válido. Ya existe un proyecto con el mismo nombre";
                            $this->eNameClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->eName = "El nombre no es válido. Ya existe un proyecto con el mismo nombre";
                            $this->eNameClass = "has-error";
                            $result = false;
                    }
            }

            // Validación de la descripción
            if(!isset($o->Description)  || $o->Description == ""){
                    $this->eDesc = "El campo descripción es obligatorio.";
                    $this->eDescClass = "has-error";
                    $result = false;
            }
            elseif(strlen($o->Description) > 500){
                    $this->eDesc = "La longitud de la descripción no puede ser superior a 500 caracteres.";
                    $this->eDescClass = "has-error";
                    $result = false;
            }

            // Validación del path
            if(!isset($o->Path)  || $o->Path == ""){
                    $this->ePath = "El campo ruta es obligatorio.";
                    $this->ePathClass = "has-error";
                    $result = false;
            }
            elseif(strlen($o->Path) > 500){
                    $this->eName = "La longitud de la ruta no puede superar los 500 caracteres.";
                    $this->eNameClass = "has-error";
                    $result = false;
            }
            // Validación si el nombre ya está registrado
            else{
                    // Filtro de búsqueda
                    $filter = array( "Path" => $o->Path );
                    // Buscamos entidades con el mismo nombre
                    $entities = $this->Dao->GetByFilter( "Project", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->ePath = "La ruta no es válido. Ya existe un proyecto con el mismo directorio.";
                            $this->ePathClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->ePath = "La ruta no es válido. Ya existe un proyecto con el mismo directorio.";
                            $this->ePathClass = "has-error";
                            $result = false;
                    }
            }

            if($o->Id == 0){
                    // Validación de la descripción
                    if(!isset($o->Date)  || $o->Date == ""){
                            $this->eDate = "El campo fecha es obligatorio.";
                            $this->eDateClass = "has-error";
                            $result = false;
                    }
                    elseif(strtotime($o->Date) === false){
                            $this->eDate = "La fecha introducida no es válida.";
                            $this->eDateClass = "has-error";
                            $result = false;
                    }
            }

            // Actualizar el mensaje final si corresponde
            if(!$result){
                    $this->eResult = "No se ha validado el formulario correctamente.";
                    $this->eResultClass = "has-error";
            }

            // Retornamos el resultado de la validación
            return $result;
    }

    ///<summary>
    /// Obtener el proyecto activo filtrado por su id
    ///</summary>
    private function Read($id = 0, $format = true){
            // filtro de búsqueda
            $filter = array( "Id" => $id, "Active" => 1 );
            // Ejecutar búsqueda
            $projects = $this->Dao->GetByFilter( "Project", $filter );
            // Validación del resultado de la operación
            if(count($projects == 1)){
                    // Asignar la referencia del proyecto encontrado
                    $this->Entity = $projects[0];
                    if($format){
                            // Asignar la fecha con el formato especidicado
                            $date = new DateTime($this->Entity->Date);
                            $this->Entity->Date = $date->format( "d-m-Y" );
                    }
                    // Retornar referencia
                    return $this->Entity;
            }
            // No se ha encontrado el proyecto
            throw new Exception ( "Item not found - id : ".$id );
    }

    ///<summary>
    /// Constructor por defecto
    ///</summary>
    public function __construct(){
            parent::__construct();
    }

    ///<summary>
    /// Establece los parámetros del formulario para cuando hay un error de tipo ajax
    ///</summary>
    public function SetAjaxError($msg = ""){
            $this->Entity = new Project();
            $this->eResult = "Se ha producido un error interno. Detalles - ".$msg;
            $this->eResultClass = "has-error";
    }

    ///<summary>
    /// Método que obtiene la lista de Proyectos
    ///</summary>
    public function LoadProjects(){
            // Cargar los proyectos activos en bbdd
            $this->Entities = $this->Dao->GetByFilter( "Project", array( "Active" => 1 ));
            // Adaptar la fecha para cada uno de los proyectos
            foreach($this->Entities as $item){
                    // Instanciar fecha
                    $date = new DateTime($item->Date);
                    // formatear la fecha
                    $item->Date = $date->format( "d-m-Y" );
                    // Asignar al title el nombre de proyecto
                    $item->TitleName = $item->Name;
                    // Acortar si es necesario
                    /*
                    $item->Name = (strlen($item->Name)<15)
                            ? $item->Name
                            : substr($item->Name, 0, 12)."...";
                     * */
                    // Asignar titulo de ayuda
                    $item->Title = $item->Description;
                    // Reducir el texto
                    $item->Description = (strlen($item->Description)<15)
                            ? $item->Description
                            : substr($item->Description,0,12)."...";
            }
    }

    ///<summary>
    /// Método que guarda la información del proyecto
    ///</summary>
    public function Save($entity = null){
            // Asignar resultado
            $result = false;

            if($entity->Id > 0){
                    // Cargar datos
                    $this->Read($entity->Id, false);
                    $this->Entity->Name = $entity->Name;
                    $this->Entity->Description = $entity->Description;
            }
            else{
                    // asignar entidad
                    $this->Entity = $entity;
            }

            // Validación del formulario
            if($this->Validate()){
                    if($entity->Id == 0){
                            // Registrar el proyecto en base de datos
                            $this->Entity->Id = $this->Dao->Create($entity);
                            // Crear el path de proyecto
                            $this->CreateProjectPath($this->Entity);
                    }
                    else{
                            // Actualizamos la información en bbdd
                            $this->Dao->Update($this->Entity);
                    }
                    $this->Read($this->Entity->Id);
                    // Asignar mensaje de resultado
                    $this->eResult = "La operación se ha realizado correctamente.";
                    // Asignar resultado de la operación
                    $result = true;
            }
            return $result;
    }

    ///<summary>
    /// Eliminación del proyecto y sus relaciones por su id
    ///</summary>
    public function Delete($id = 0){
            // Buscar el proyecto activo por su id
            $projects = $this->Dao->GetByFilter( "Project", array( "Id" => $id, "Active" => 1 ));
            // Comprobar si existe
            if(count($projects) != 1) return;
            // Obtener referencia al proyecto
            $project = $projects[0];
            // modificar su estado lógico
            $project->Active = 0;
            // Actualizar en bbdd el estado del proyecto
            $this->Dao->Update($project);

            // Filtro para las búsquedas
            $filter = array( "IdProject" => $id );
            // Obtener todas las posibles relaciones entre Servicio y projecto
            $result = $this->Dao->GetByFilter( "ProjectServices" , $filter);
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectServices" );

            // Obtener todas las posibles relaciones entre usuarios y proyecto
            $result = $this->Dao->GetByFilter( "ProjectUsers" , $filter);
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectUsers" );

            // Obtener todas las posibles relaciones entre usuarios y proyecto
            $result = $this->Dao->GetByFilter( "UserRoleServiceProject" , $filter);
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "UserRoleServiceProject" );

    }

    ///<summary>
    /// Obtiene los servicios y las asociaciones con el proyecto
    ///</summary>
    public function LoadServices($projectId = 0){
            // Cargar la información de proyecto
            $project = $this->Read($projectId);
            // Cargar los servicios activos
            $this->Services = $this->Dao->GetByFilter( "Service", array( "Active" => 1));
            // Cargar los servicios asociados al proyecto
            $services = $this->Dao->GetByFilter( "ProjectServices", array( "IdProject" => $projectId ));
            // Serializar las relaciones
            if($services != null && count($services) > 0 )
                    $this->jsonServices = json_encode($services);
    }

    ///<summary>
    /// Método que crea una asociación entre un servicio y un proyecto
    ///</summary>
    public function AddService($entity){
            // Persistir
            $this->Dao->Create($entity);
            // Obtener los datos del servicio
            $service = $this->Dao->Read($entity->IdService, "Service" );
            // Obtener los datos del proyecto
            $project = $this->Dao->Read($entity->IdProject, "Project" );
            // validar paths
            $this->SetServicePath($project, $service);
            // retornar entidad
            return $entity;
    }

    ///<summary>
    /// Método que elimina la/s asociación/es entre un servicio y un proyecto
    ///</summary>
    public function RemoveService($entity){
            // filtro de búsqueda
            $filter = array( "IdProject" => $entity->IdProject, "IdService" => $entity->IdService );
            // Obtener todas las posibles relaciones entre Servicio y projecto
            $result = $this->Dao->GetByFilter( "ProjectServices" , $filter);
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectServices" );
    }

    ///<summary>
    /// Obtiene los usuarios del sistema y las asociaciones con el proyecto
    ///</summary>
    public function LoadUsers($projectId = 0){
            // Cargar la información de proyecto
            $project = $this->Read($projectId);
            // Cargar los servicios activos
            $this->Users = $this->Dao->GetByFilter( "User", array( "Active" => 1));
            // Cargar los servicios asociados al proyecto
            $users = $this->Dao->GetByFilter( "ProjectUsers", array( "IdProject" => $projectId ));
            // Serializar las relaciones
            if($users != null && count($users) > 0 )
                    $this->jsonUsers = json_encode($users);
    }

    ///<summary>
    /// Método que crea una asociación entre un usuario y un proyecto
    ///</summary>
    public function AddUser($entity){
            // Persistir
            $this->Dao->Create($entity);
            // retornar entidad
            return $entity;
    }

    ///<summary>
    /// Método que elimina la/s asociación/es entre un servicio y un proyecto
    ///</summary>
    public function RemoveUser($entity){
            // filtro de búsqueda
            $filter = array( "IdProject" => $entity->IdProject, "IdUser" => $entity->IdUser );
            // Obtener todas las posibles relaciones entre usuarios y proyecto
            $result = $this->Dao->GetByFilter( "ProjectUsers" , $filter);
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectUsers" );
    }

}
