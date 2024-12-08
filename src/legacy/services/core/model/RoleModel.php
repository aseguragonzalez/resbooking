<?php

///<summary>
/// Model para la gestión de Role
///</summary>
class RoleModel extends \CoreModel{

    ///<summary>
    /// Título para los formularios
    ///</summary>
    public $Title = "Gestión de Roles";

    ///<summary>
    /// Parámetro para activar el menú
    ///</summary>
    public $MenuActivo = "Role";

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
    /// Serialización json de los servicios relacionados
    ///</summary>
    public $Relations = "[]";

    ///<summary>
    /// Propiedades para la validación del formulario
    ///</summary>
    public $eName = "";
    public $eNameClass = "";
    public $eDesc = "";
    public $eDescClass = "";
    public $eResult = "";
    public $eResultClass = "has-success";

    ///<summary>
    /// Método que obtiene el Role filtrado por su identidad
    ///</summary>
    private function Read($id = 0){
            // Buscar la entidad
            $entities = $this->Dao->GetByFilter( "Role", array( "Active" => 1, "Id" => $id ));
            // Comprobar si hay una entidad
            if(count($entities) == 1)
                    $this->Entity = $entities[0];
            else
                    throw new Exception ( "Item not found - id : ".$id );
            return $this->Entity;
    }

    ///<summary>
    /// Proceso de validación de la entidad
    ///</summary>
    private function Validate(){
            // Resultado por defecto
            $result = true;
            // Referenciar la entidad
            $o = $this->Entity;

            // Validación del nombre
            if(!isset($o->Name)  || $o->Name == ""){
                    $this->eName = "El campo nombre es obligatorio";
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
                    $entities = $this->Dao->GetByFilter( "Role", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->eName = "El nombre no es válido. Ya existe un Role con el mismo nombre";
                            $this->eNameClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->eName = "El nombre no es válido. Ya existe un Role con el mismo nombre";
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

            // Actualizar el mensaje final si corresponde
            if(!$result){
                    $this->eResult = "No se ha validado el formulario correctamente.";
                    $this->eResultClass = "has-error";
            }

            // Retornamos el resultado de la validación
            return $result;
    }

    ///<summary>
    /// Constructor por defecto
    ///</summary>
    public function __construct(){
            parent::__construct();
    }

    ///<summary>
    /// Método que obtiene la lista de Role
    ///</summary>
    public function LoadRoles(){
            // Cargar entidades filtradas
            $this->Entities = $this->Dao->GetByFilter( "Role", array( "Active" => 1 ));
            // Ajustar descripciones
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
            $this->Entity = new Role();
            $this->eResult = "Se ha producido un error interno. Detalles - ".$msg;
            $this->eResultClass = "has-error";
    }

    ///<summary>
    /// Método que carga las dependencias del formulario de Role
    ///</summary>
    public function LoadFormData($id = 0, $state = 0){
            // Cargar entidad
            if($id > 0) $this->Read($id);
            // Obtener la lista de servicios disponibles
            $this->Services = $this->Dao->GetByFilter( "Service" , array( "Active" => 1));
            // Obtener todas las posibles relaciones entre Servicios y el role
            $result = $this->Dao->GetByFilter( "ServiceRole" , array( "IdRole" => $this->Entity->Id ));
            // Serializar las relaciones
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
            // Obtener la entidad actual
            $entity = $this->Read($id);
            // establecer el estado lógico de la entidad a borrado
            $entity->Active = 0;
            // Actualizar en bbdd
            $this->Dao->Update($entity);

            // Obtener todas las posibles relaciones entre Servicio y rol
            $result = $this->Dao->GetByFilter( "ServiceRole" , array( "IdRole" => $id));
            // Eliminar cada relación
            foreach($result as $entity)
                    $this->Dao->Delete($entity->Id, "ServiceRole" );

            // Obtener todas las posibles relaciones entre Servicio, rol y usuario
            $result = $this->Dao->GetByFilter( "UserRoleService" , array( "IdRole" => $id));
            // Eliminar cada relación
            foreach($result as $entity)
                    $this->Dao->Delete($entity->Id, "UserRoleService" );
    }

    ///<summary>
    /// Método que crea una asociación entre un servicio y un role
    ///</summary>
    public function AddService($idRole = 0, $idService = 0){
            // Instanciar entidad de relación
            $entity = new ServiceRole();
            $entity->IdRole = $idRole;
            $entity->IdService = $idService;
            // Persistir
            $this->Dao->Create($entity);
            return $entity;
    }

    ///<summary>
    /// Método que elimina la/s asociación/es entre un servicio y un role
    ///</summary>
    public function RemoveService($idRole = 0, $idService = 0){
            // Definir el filtro de búsqueda
            $filter = array( "IdRole" => $idRole, "IdService" => $idService );
            // Obtener todas las posibles relaciones entre Servicio y rol
            $result = $this->Dao->GetByFilter( "ServiceRole" , $filter);
            // Eliminar cada relación
            foreach($result as $entity)
                    $this->Dao->Delete($entity->Id, "ServiceRole" );

            // Definir el filtro
            $filter = array( "IdRole" => $idRole, "IdService" => $idService );
            // Obtener todas las posibles relaciones entre Servicio, rol y usuario
            $result = $this->Dao->GetByFilter( "UserRoleService" , $filter);
            // Eliminar cada relación
            foreach($result as $entity)
                    $this->Dao->Delete($entity->Id, "UserRoleService" );

    }

}
