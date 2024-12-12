<?php

declare(strict_types=1);

/**
 *  Model para las operaciones sobre cuentas de usuario
 */
class AccountsModel extends \SaasModel{

    /**
     * Opción del menú activa
     * @var string
     */
    public $Activo = "Perfil";

    /**
     * E-mail del usuario a resetear
     * @var string
     */
    public $Email = "";

    /**
     * Password original (Hash)
     * @var string
     */
    public $Pass = "";

    /**
     * Nueva password a utilizar (Hash)
     * @var string
     */
    public $NewPass = "";

    /**
     * Repetición password a utilizar (Hash)
     * @var string
     */
    public $ReNewPass = "";

    /**
     * Texto resultado de la operación
     * @var string
     */
    public $Result = "";

    /**
     * Clase CSS para el resultado de la operación
     * @var type
     */
    public $ResultCss = "has-error";

    /**
     * Mensaje de error para la propiedad email de usuario
     * @var string
     */
    public $eEmail = "";

    /**
     * Clase CSS para el mensaje de error sobre el email de usuario
     * @var string
     */
    public $eEmailClass = "";

    /**
     * Mensaje de error para la password actual
     * @var type
     */
    public $ePass = "";

    /**
     * Clase CSS para el mensaje de error de la password actual
     * @var string
     */
    public $ePassClass = "";

    /**
     * Mensaje de error para la nueva password
     * @var type
     */
    public $eNewPass = "";

    /**
     * Clase CSS para el mensaje de error de nueva password
     * @var string
     */
    public $eNewPassClass = "";

    /**
     * Mensaje de error para la repetición de la password
     * @var string
     */
    public $eReNewPass = "";

    /**
     * Clase CSS para el mensaje de error en la repetición del password
     * @var string
     */
    public $eReNewPassClass = "";

    /**
     * @ignore
     * Obtener el ide del proyecto Resbooking
     * @return type
     */
    private function GetProjectId(){
        // configurar el filtro de búsqueda
        $filter = ["Name" => "Resbooking" , "Active" => 1];
        // Obtener el proyecto resbooking
        $projects = $this->Dao->GetByFilter( "Project", $filter );
        // retornar el Id
        return $projects[0]->Id;
    }

    /**
     * @ignore
     * Obtener el id de servicio
     * @return int Id del servicio Resbooking
     */
    private function GetServiceId(){
        // configurar el filtro de búsqueda
        $filter = ["Name" => "resbooking", "Active" => 1];
        // Obtener el servicio resbooking
        $services = $this->Dao->GetByFilter( "Service", $filter );
        // retornar el Id
        return $services[0]->Id;
    }

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Proceso de recuperación de contraseña
     * @param string $email Email del usuario actual
     * @return boolean
     */
    public function Recovery($email = ""){
        $this->Email = $email;
        $resultado = false;
        if($this->ValidarRecovery()){
            // Obtener el id de proyecto resbooking
            $project = $this->GetProjectId();
            // Obtener el id servicio resbooking
            $service = $this->GetServiceId();
            // Argumentos para la llamada
            $parametros = array( "email" => $this->Email,
                "project" => $project, "service" => $service);
            // Resetear usuario
            $resultado = UserUtilities::ResetPassword($parametros);

            if($resultado == true){
                $this->Result = "Sus credenciales han sido reseteadas "
                        . "y notificadas a su cuenta de correo.";
                $this->ResultCss = "has-success";
            }
            else{
                $this->Result="No ha sido posible resetear las "
                        . "credenciales de usuario. Contacte con el"
                        . " administrador.";
            }
        }
        return $resultado;
    }

    /**
     * Establece los parámetros del DTO en el modelo actual
     * @param \ChangeDTO $dto Referencia al dto de cambio de password
     */
    private function SetChangeDTO($dto = null){
        if($dto != null){
            $this->Email = $this->Username;
            $this->Pass = $dto->Pass;
            $this->NewPass = $dto->NewPass;
            $this->ReNewPass = $dto->ReNewPass;
        }
    }

    /**
     * Proceso de modificación de la contraseña
     * @param \ChangeDTO $dto Referencia al DTO para cambio de contraseña
     * @return boolean
     */
    public function ChangePass($dto = null){

        $resultado = false;

        $this->SetChangeDTO($dto);

        if($this->ValidarChangePass()){
            $parametros = array( "email" => $this->Email,
                "pass" => $this->Pass, "newpass" => $this->NewPass);
            $resultado = UserUtilities::ChangePassword($parametros);
            if($resultado == true){
                $this->Result = "Sus credenciales han sido "
                        . "modificadas con éxito.";
                $this->ResultCss = "has-success";
            }
            else{
                $this->Result="No ha sido posible modificar sus "
                        . "credenciales. Contacte con el administrador.";
            }
        }
        return $resultado;
    }

    /**
     * Proceso de validación para recuperar la contraseña
     * @return boolean
     */
    private function ValidarRecovery(){
        $error = false;
        if(!isset($this->Email) || $this->Email == ""){
            $this->eEmail = "Debe especificar una dirección de e-mail.";
            $this->eEmailClass = "has-error";
            $error = true;
        }
        return !$error;
    }

    /**
     * Proceso de validación de la propiedad Email
     * @param boolean $error Estado de validación actual
     * @return boolean Resultado de la validación
     */
    private function ValidateEmail($error = false){
        if(!isset($this->Email) || $this->Email == ""){
            $this->eEmail = "Debe especificar una dirección de e-mail.";
            $this->eEmailClass = "has-error";
            $error = true;
        }
        return $error;
    }

    /**
     * Proceso de validación de la password actual
     * @param boolean $error Estado de la validación actual
     * @return boolean Resultado de la validación
     */
    private function ValidatePass($error = false){
        if(!isset($this->Pass) || $this->Pass == ""){
            $this->ePass = "Debe especificar su password actual.";
            $this->ePassClass = "has-error";
            $error = true;
        }
        return $error;
    }

    /**
     * Proceso de validación de la nueva password
     * @param boolean $error Estado del proceso de validación actual
     * @return boolean Resultado de la validación
     */
    private function ValidateNewPass($error = false){

        if(!isset($this->NewPass) || $this->NewPass == ""){
            $this->eNewPass = "Debe especificar su nueva password.";
            $this->eNewPassClass = "has-error";
            $error = true;
        }

        if(!isset($this->ReNewPass) || $this->ReNewPass == ""){
            $this->eReNewPass = "Debe repetir la contraseña.";
            $this->eReNewPassClass = "has-error";
            $error = true;
        }

        if(isset($this->NewPass) && $this->NewPass != ""
                && isset($this->ReNewPass) && $this->ReNewPass != ""
                    && $this->NewPass != $this->ReNewPass){
            $this->eReNewPass = "La contraseña y su repetición no coinciden.";
            $this->eReNewPassClass = "has-error";
            $error = true;
        }

        return $error;
    }

    /**
     * Proceso de validación para cambiar la contraseña
     * @return boolean Resultado del proceso de validación
     */
    private function ValidarChangePass(){
        return !$this->ValidateNewPass(
                        $this->ValidatePass(
                            $this->ValidateEmail(false)));
    }

}
