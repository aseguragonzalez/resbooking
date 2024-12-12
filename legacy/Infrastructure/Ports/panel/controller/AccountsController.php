<?php



// Cargar la referencia al model de cuentas de usuario
require_once "model/AccountsModel.php";
require_once "model/dto/AccountDTO.php";

/**
 * Controlador para la gestión de cuentas de usuarios
 *
 * @author alfonso
 */
class AccountsController extends \PanelController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción para cargar el formulario de recuperación de contraseña
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar el modelo
            $model = new \AccountsModel();
            // Establecer cabecera
            $model->Title = "Perfil de usuario";
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción de actualización de la contraseña de acceso al sistema
     * del usuario actual
     * @return string Vista renderizada
     */
    public function SaveChanges(){
        try{
            // Obtener los datos de la solicitud
            $dto = $this->GetEntity( "AccountDTO" );
            // Instanciar el modelo
            $model = new \AccountsModel();
            // Establecer cabecera
            $model->Title = "Perfil de usuario";
            // Proceso de actualización de los cambios
            $model->SaveNewPassword($dto);
            // Renderizar la vista
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("SaveChanges", $e);
        }
    }

    /**
     * Acción para cargar el formulario de regeneración de password
     * @return string Vista renderizada
     */
    public function Recovery(){
        try{
            // Instanciar modelo
            $model = new \AccountsModel();
            // Establecer cabecera
            $model->Title = "Recuperación de contraseña";
            // renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Recovery", $e);
        }
    }

    /**
     * Acción para ejecutar el proceso de recuperación de la contraseña
     * @return string Vista renderizada
     */
    public function SetRecovery(){
        try{
            // Obtener la información del formulario
            $dto = $this->GetEntity("AccountDTO");
            // Instanciar modelo
            $model = new \AccountsModel();
            // Establecer cabecera
            $model->Title = "Recuperación de contraseña";
            // Proceso de recuperación
            $model->RecoveryPassword($dto);
            // renderizar la vista
            return $this->Partial("Recovery", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("SetRecovery", $e);
        }
    }

    /**
     * Acción para iniciar la sessión actual
     * @return string Vista renderizada
     */
    public function Login(){
        try{
            // Instanciar modelo
            // $model = new \AccountsModel();
            // Redirigir el flujo
            return $this->RedirectTo("Index", "Home");
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Login", $e);
        }
    }

    /**
     * Acción para cerrar la sessión actual
     * @return string Vista renderizada
     */
    public function Logout(){
        try{
            // eliminar la sesión actual
            session_destroy();
            // Redirigir el flujo
            return $this->RedirectTo( "Index", "Home");
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Logout", $e);
        }
    }
}
