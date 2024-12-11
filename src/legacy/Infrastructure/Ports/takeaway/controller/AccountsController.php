<?php



// Cargar dependencias
require_once( "model/AccountsModel.php" );

/**
 *  Controlador para gestionar el login
 */
class AccountsController extends \Controller{

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Formulario de recuperación de contraseña
     * @return String Vista rederizada
     */
    public function Index(){
        try{
            if(isset($_SESSION[ "recoveryModel" ])){
                // Recuperar referencia de la sesión
                $model = json_decode($_SESSION[ "recoveryModel" ]);
                // eliminar referencia
                unset($_SESSION[ "recoveryModel" ]);
            }
            else{
                // Instanciar el modelo
                $model = new \AccountsModel();
            }
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Generar traza de error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para visualizar el formulario de contraseñas
     * @return string Vista del modelo renderizada
     */
    public function ChangePass(){
        try{

            if(isset($_SESSION[ "changeModel" ])){
                // Recuperar referencia de la sesión
                $model = json_decode($_SESSION[ "changeModel" ]);
                // eliminar referencia
                unset($_SESSION[ "changeModel" ]);
            }
            else{
                // Instanciar modelo
                $model = new AccountsModel();
            }
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Generar traza de error
            return $this->ProcessError("ChangePass", $e);
        }
    }

    /**
     * Acción para modificar contraseña
     * @return string Redirección o vista renderizada
     */
    public function Change(){
        try{
            // Recuperar parámetros de la llamada
            $dto = $this->GetEntity( "ChangeDTO" );
            // Instanciar modelo
            $model = new AccountsModel();
            // Lanzar el proceso de recuperación
            if($model->ChangePass($dto)){
                // Setear el contexto
                $_SESSION[ "changeModel" ] = json_encode($model);
                // redirigir el flujo de ejecución
                return $this->RedirectTo( "ChangePass", "Accounts" );
            }
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Generar traza de error
            return $this->ProcessError( "Change", $e);
        }
    }

    /**
     * Acción para generar la recuperación de la contraseña
     * @return string Redirección al index de gestión de cuentas
     */
    public function Recovery(){
        try{
            // Recuperar parámetros de la llamada
            $dto = $this->GetEntity( "ChangeDTO" );
            // Instanciar modelo
            $model = new AccountsModel();
            // Lanzar el proceso de recuperación
            $model->Recovery($dto->Email);
            // Setear el contexto
            $_SESSION[ "recoveryModel" ] = json_encode($model);
            // Redirigir el flujo
            return $this->RedirectTo( "Index", "Accounts" );
        }
        catch(Exception $e){
            // Generar traza de error
            return $this->ProcessError( "Recovery", $e);
        }
    }

    /**
     * Acción para iniciar la sessión actual
     * @return string redirección al inicio
     */
    public function Login(){
        try{
            // Instanciar modelo
            $model = new AccountsModel();
            // Redirigir el flujo
            return $this->RedirectTo("Index", "Home");
        }
        catch(Exception $e){
            // Generar traza de error
            return $this->ProcessError( "Login", $e);
        }
    }

    /**
     * Acción para cerrar la sessión actual
     * @return string redirección al inicio
     */
    public function Logout(){
        try{
            // eliminar la sesión actual
            session_destroy();
            // Redirigir el flujo
            return $this->RedirectTo( "Index", "Home");
        }
        catch(Exception $e){
            // Generar traza de error
            return $this->ProcessError( "Logout", $e);
        }
    }

    /**
     * Procesado de la excepción capturada
     * @param string $action Nombre de la acción donde se produce el error
     * @param \Exception $e Referencia a la excepción capturada
     * @return string Vista renderizada
     */
    private function ProcessError($action = "", $e = NULL){
        // Crear traza de error
        $this->Log->LogErrorTrace($action, $e);
        // Instanciar Modelo
        $model = new \SaasModel();
        // Renderizado de la vista de error
        return $this->Partial( "error", $model);
    }
}
