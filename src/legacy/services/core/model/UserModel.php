<?php

///<summary>
/// Model para la gestión de User
///</summary>
class UserModel extends \CoreModel{

    ///<summary>
    /// Parámetro para activar el menú
    ///</summary>
    public $MenuActivo = "User";

    ///<summary>
    /// Proyecto seleccionado
    ///</summary>
    public $IdProject = 0;

    ///<summary>
    /// Referencia a la entidad User
    ///</summary>
    public $Entity = null;

    ///<summary>
    /// Colección de User disponibles
    ///</summary>
    public $Entities = array();

    ///<summary>
    /// Colección de proyectos disponibles
    ///</summary>
    public $Projects = array();

    ///<summary>
    /// Colección de servicios disponibles
    ///</summary>
    public $Services = array();

    ///<summary>
    /// Colección de roles disponibles
    ///</summary>
    public $Roles = array();

    ///<summary>
    /// Colección de asociaciones service-rol
    ///</summary>
    public $ServiceRoles = array();

    ///<summary>
    /// Colección de asociaciones proyecto-servicio
    ///</summary>
    public $ProjectServices = array();

    ///<summary>
    /// Colección de asociaciones proyecto-servicio-role-usuario
    ///</summary>
    public $UserRoleServiceProject = array();

    ///<summary>
    /// Propiedades para la validación de formularios
    ///</summary>
    public $eUsername = "";
    public $eUsernameClass = "";
    public $eResult = "";
    public $eResultClass = "has-success";

    ///<summary>
    /// Método que obtiene el User filtrado por su identidad
    ///</summary>
    private function Read($id = 0){
            // Filtro de búsqueda
            $filter = array( "Id" => $id, "Active" => 1 );
            // Buscar la entidad
            $entities = $this->Dao->GetByFilter( "User", $filter );
            // Validar la búsqueda
            if(count($entities) == 1){
                    // Obtener la referencia
                    $this->Entity =	$entities[0];
                    // retornar una copia
                    return $this->Entity;
            }
            // Lanzar la excepción
            throw new Exception ( "Item not found - id : ".$id );
    }

    ///<summary>
    /// Proceso de validación
    ///</summary>
    private function Validate(){
            // Resultado por defecto
            $result = true;
            // Referenciar la entidad
            $o = $this->Entity;
            // Validación del nombre
            if(!isset($o->Username)  || $o->Username == ""){
                    $this->eUsername = "El campo nombre de usuario es obligatorio.";
                    $this->eUsernameClass = "has-error";
                    $result = false;
            }
            elseif(strlen($o->Username) > 50){
                    $this->eUsername = "La longitud del nombre de usuario no puede superar los 50 caracteres.";
                    $this->eUsernameClass = "has-error";
                    $result = false;
            }
            // Validación si el nombre ya está registrado
            else{
                    // Filtro de búsqueda
                    $filter = array( "Username" => $o->Username );
                    // Buscamos entidades con el mismo nombre
                    $entities = $this->Dao->GetByFilter( "User", $filter);
                    // Comprobamos no sea edición
                    if(count($entities) == 1 && $entities[0]->Id != $o->Id){
                            $this->eUsername = "El nombre de usuario no es válido. Ya existe un usuario con el mismo e-mail.";
                            $this->eUsernameClass = "has-error";
                            $result = false;
                    }
                    elseif(count($entities) > 1){
                            $this->eUsername = "El nombre de usuario no es válido. Ya existe un usuario con el mismo e-mail.";
                            $this->eUsernameClass = "has-error";
                            $result = false;
                    }
            }
            // Actualizar el mensaje final si corresponde
            if(!$result){
                    $this->eResult = "No se ha validado el formulario correctamente.";
                    $this->eResultClass = "has-error";
            }
            // retornar el resultado
            return $result;
    }

    private function GetByParam($array, $property, $value){
            foreach($array as $item){
                    if($item->{$property} == $value){return $item;}
            }
            // retornar null
            return null;
    }

    ///<summary>
    /// Cargar los proyectos asociados al usuario
    ///</summary>
    private function LoadUserProjects( $idUser = 0 ){
            // Cargar la lista de proyectos disponibles
            $projects = $this->Dao->GetByFilter( "Project", array( "Active" => 1));
            // Cargar la lista de proyectos asociados al usuario
            $userProjects = $this->Dao->GetByFilter( "ProjectUsers" , array( "IdUser" => $idUser ));
            // Cargar proyectos asociados
            foreach($userProjects as $userproject){
                    // Buscar proyecto
                    $pro = $this->GetByParam($projects, "Id", $userproject->IdProject);
                    // Guardar el proyecto asociado
                    if($pro != null) $this->Projects[] = $pro;
            }
    }

    ///<summary>
    /// Realiza la carga de todas las entidades de bbdd necesarias
    ///</summary>
    private function LoadEntities($idProject = 0, $idUser = 0){
            // Cargar la lista de servicios disponibles
            $this->Services = $this->Dao->GetByFilter( "Service", array( "Active" => 1));
            // Serializar servicios
            $this->jsonServices = json_encode($this->Services);
            // Cargar la lista de servicios disponibles
            $this->Roles = $this->Dao->GetByFilter( "Role", array( "Active" => 1));
            // Serialización json
            $this->jsonRoles = json_encode($this->Roles);

            // Cargar la lista de las asociaciones de servicios y roles disponibles
            $this->ServiceRoles = $this->Dao->Get( "ServiceRole" );
            // Buscar los registros de asociación de proyectos con servicios
            $this->ProjectServices = $this->Dao->GetByFilter( "ProjectServices"  , array( "IdProject" => $idProject ));
            // Filtro de busqueda
            $filter = array( "IdUser" => $idUser , "IdProject" => $idProject);
            // Obtener la lista de asociaciones user-role-service
            $this->UserRoleServiceProject = $this->Dao->GetByFilter( "UserRoleServiceProject" , $filter);

            $this->AgruparInformacion();
    }

    ///<summary>
    /// Agrupar la información obtenida de base de datos
    ///</summary>
    private function AgruparInformacion(){

            $servicios = array();

            foreach($this->Services as $item){
                    $roles = array();
                    foreach($this->ServiceRoles as $rel){
                            if($rel->IdService == $item->Id){
                                    if(!isset($roles[$rel->IdRole]))
                                            $roles[$rel->IdRole] = array( "IdRole" => $rel->IdRole, "Checked" => false );
                            }
                    }

                    $servicios[$item->Id] = array( "IdService" => $item->Id, "roles" => $roles);
            }

            foreach($this->UserRoleServiceProject as $o){
                    $roles = $servicios[$o->IdService]["roles"];
                    foreach($roles as $role){
                            if($role["IdRole"] == $o->IdRole){
                                    $role["Checked"] = true;
                                    $role["IdService"] = $o->IdService;
                                    $role["IdProject"] = $o->IdProject;
                                    $role["IdUser"] = $o->IdUser;
                                    $roles[$role["IdRole"]] = $role;
                            }
                    }
                    $servicios[$o->IdService]["roles"] = $roles;
            }
            $this->jsonRelations = json_encode($servicios);
    }

    ///<summary>
    /// Constructor por defecto
    ///</summary>
    public function __construct(){
            parent::__construct();
    }

    ///<summary>
    /// Método que obtiene la lista de User
    ///</summary>
    public function LoadUsers(){
            // filtro de búsqueda
            $filter = array("Active" => 1);
            // Obtener todas las entidades
            $this->Entities = $this->Dao->GetByFilter( "User" , $filter );
    }

    ///<summary>
    /// Establece los parámetros del formulario para cuando hay un error de tipo ajax
    ///</summary>
    public function SetAjaxError($msg = ""){
            $this->Entity = new User();
            $this->eResult = "Se ha producido un error interno. Detalles - ".$msg;
            $this->eResultClass = "has-error";
    }

    ///<summary>
    /// Método que carga las dependencias del formulario de usuario
    ///</summary>
    public function LoadRelationsForm($idUser = 0, $idProject = 0){
            // Establecer proyecto seleccionado
            $this->IdProject = $idProject;
            // Cargar los datos de usuario
            $this->Read($idUser);
            // Cargar proyectos
            $this->LoadUserProjects($idUser);
            // Cargar un proyecto por defecto si hay
            if($idProject == 0 && count($this->Projects) > 0)
                    $idProject = $this->Projects[0]->Id;
            // Carga todas las dependencias de base de datos
            $this->LoadEntities($idProject, $idUser);
    }

    ///<summary>
    /// Método que guarda la información relativa a la entidad
    ///</summary>
    public function Save($entity = null){
            // Asignar resultado
            $result = false;
            // asignar entidad
            $this->Entity = $entity;
            // Validación del formulario
            if($this->Validate()){
                    // Determinar si es nuevo o edición
                    if($entity->Id == 0){
                            // Argumentos de la llamada
                            $parameters = array(
                                    "user" => $entity,
                                    "project" => $this->Project,
                                    "service" => $this->Service
                            );
                            // Crear usuario
                            $result = UserUtilities::CreateUser($parameters);
                    }
                    else{
                            // Obtener datos de bbdd
                            $temEntity = $this->Dao->Read($entity->Id, "User" );
                            // Mantener el password de bbdd
                            $entity->Password = $temEntity->Password;
                            // Persistir
                            $this->Dao->Update($entity);
                    }
                    // Asignar mensaje de resultado
                    $this->eResult = "La operación se ha realizado correctamente.";
                    // Asignar resultado
                    $result = true;
            }
            return $result;
    }

    ///<summary>
    /// Método que elimina la entidad identificada por su id
    ///</summary>
    public function Delete($id = 0){
            // Obtener Id
            $entity = $this->Read( $id );
            // Establecer estado de baja
            $entity->Active = 0;
            // Actualizar en bbdd
            $this->Dao->Update($entity);
            // Obtener todas las posibles relaciones entre Servicio y rol
            $result = $this->Dao->GetByFilter( "ProjectUsers" , array( "IdUser" => $id ));
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "ProjectUsers" );

            $data = array( "IdUser" => $id );
            // Obtener todas las posibles relaciones entre Servicio y role
            $result = $this->Dao->GetByFilter( "UserRoleServiceProject" , $data);
            // Eliminar cada relación
            foreach($result as $ent)
                    $this->Dao->Delete($ent->Id, "UserRoleServiceProject" );
    }

    ///<summary>
    /// Método para reiniciar la password del usuario
    ///</summary>
    public function Reset($id = 0){
            // Resultado por defecto
            $result = false;
            // Obtener datos de bbdd
            $user = $this->Read( $id );
            // validar el usuario
            if($user != null){
                    // Argumentos para la llamada
                    $parametros = array( "email" => $user->Username, "project" => $this->Project, "service" => $this->Service);
                    // Resetear usuario
                    $result = UserUtilities::ResetPassword($parametros);
            }
            return $result;
    }

    ///<summary>
    /// Actualiza la relación del usuario-role-servicio-proyecto
    ///</summary>
    public function SaveRelation($entity = null){
            // validar parámetro
            if($entity == null) return false;
            // filtro de búsqueda
            $filter = array(
                    "IdUser" => $entity->IdUser,
                    "IdService" => $entity->IdService,
                    "IdRole" => $entity->IdRole,
                    "IdProject" => $entity->IdProject
            );
            // Buscar relaciones
            $entities = $this->Dao->GetByFilter( "UserRoleServiceProject" , $filter );
            // Actualizar la relación
            if(count($entities) == 0)
                    $entity->Id = $this->Dao->Create($entity);
            else{
                    foreach($entities as $item)
                            $this->Dao->Delete( $item->Id, "UserRoleServiceProject");
            }
            return true;
    }

}
