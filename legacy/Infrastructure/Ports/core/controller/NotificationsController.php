<?php

require_once("model/NotificationsModel.php");

///<summary>
/// Controlador para la configuración de notificaciones
///</summary>
class NotificationsController extends SaasController{

    ///<summary>
    /// Gestión de estados
    ///</summary>
    private $State = false;

    ///<summary>
    /// Constructor por defecto
    ///</summary>
    public function __construct(){
        parent::__construct();
        // Comprobamos si hay un estado anterior de operación
        $this->State = isset($_SESSION["notificationConfig"]) ;
        // Eliminar estado anterior
        if($this->State){
            unset($_SESSION["notificationConfig"]);
        }
    }

    ///<summary>
    /// Carga la página inicial
    ///</summary>
    public function Index(){
        try{
            // Instanciar model
            $model = new NotificationsModel();
            // Cargar las configuraciones existentes
            $model->LoadConfigs();
            // Renderizar la vista con la info del modelo
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesar el error capturado
            $this->Log->LogErrorTrace("Index", $e);
            // Relanzar la excepción
            throw $e;
        }
    }

    ///<summary>
    /// Carga la página inicial
    ///</summary>
    public function Create(){
        try{
            // Instanciar modelo
            $model = new NotificationsModel();
            // Cargar toda la información para el formulario
            $model->LoadFormData();
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesar el error capturado
            $this->Log->LogErrorTrace("Create", $e);
            // Relanzar la excepción
            throw $e;
        }
    }

    ///<summary>
    /// Carga la página inicial
    ///</summary>
    public function Edit($id = 0){
        try{
            // Instanciar modelo
            $model = new NotificationsModel();
            // Cargar toda la información para el formulario
            $model->LoadFormData($id, $this->State);
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesar el error capturado
            $this->Log->LogErrorTrace("Edit", $e);
            // Relanzar la excepción
            throw $e;
        }
    }

    ///<summary>
    /// Carga la página inicial
    ///</summary>
    public function Save(){
        try{
            // Obtener los datos del formulario
            $entity = $this->GetEntity( "NotificationConfig" );
            // Instanciar modelo
            $model = new NotificationsModel();
            // Proceso de guardado
            if($model->Save($entity)){
                // Establecemos como correcta la operación
                $_SESSION[ "notificationConfig" ] = true;
                // Redirigimos el tráfico
                return $this->RedirectTo("Edit", "Notifications",
                        [ "id" => $model->Entity->Id ]);
            }
            else{
                // Cargar formulario
                $model->LoadFormData($entity->Id);
                // Renderizar la vista
                return $this->Partial("Edit", $model);
            }
        }
        catch(Exception $e){
            // Procesar el error capturado
            $this->Log->LogErrorTrace("Save", $e);
            // Relanzar la excepción
            throw $e;
        }
    }

    ///<summary>
    /// Carga la página inicial
    ///</summary>
    public function Delete($id = 0){
        try{
            // Instanciar modelo
            $model = new NotificationsModel();
            // Eliminar la notificación
            $model->Delete($id);
            // Redirigir el flujo de la ejecución
            return $this->RedirectTo("Index","Notifications");
        }
        catch(Exception $e){
            // Procesar el error capturado
            $this->Log->LogErrorTrace("Delete", $e);
            // Relanzar la excepción
            throw $e;
        }
    }

}
