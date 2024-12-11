<?php

/**
 * Model para las operaciones sobre cuentas de usuario
 */
class AccountsModel extends CoreModel{

    /**
     * Parámetro para activar el menú
     * @var String
     */
    public $MenuActivo = "Accounts";

    /**
     * E-mail del usuario a resetear
     * @var String
     */
    public $Email = "";

    /**
     * Password original (Hash)
     * @var String
     */
    public $Pass = "";

    /**
     * Nueva password a utilizar (Hash)
     * @var String
     */
    public $NewPass = "";

    /**
     * Repetición password a utilizar (Hash)
     * @var String
     */
    public $ReNewPass = "";

    /**
     * Texto resultado de la operación
     * @var String
     */
    public $Result = "";

    /**
     * Clase css para el resultado de la operación
     * @var String
     */
    public $ResultCss = "has-error";

    /**
     * Mensaje de error e-mail de usuario
     * @var String
     */
    public $eEmail = "";

    /**
     * Clase CSS para el mensaje de error
     * @var String
     */
    public $eEmailClass = "";

    /**
     * Mensaje de error de la contraseña actual
     * @var String
     */
    public $ePass = "";

    /**
     * Clase CSS para el mensaje de error de contraseña actual
     * @var String
     */
    public $ePassClass = "";

    /**
     * Mensaje de error para la nueva contraseña
     * @var String
     */
    public $eNewPass = "";

    /**
     * Clase CSS para el mensaje de error de nueva contraseña
     * @var String
     */
    public $eNewPassClass = "";

    /**
     * Mensaje de error para la nueva contraseña repetida
     * @var String
     */
    public $eReNewPass = "";

    /**
     * Clase CSS para el mensaje de error de la nueva contraseña repetida
     * @var String
     */
    public $eReNewPassClass = "";

    /**
     * Constructor de la clase
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Proceso de recuperación de contraseña
     * @param String $email Email del usuario
     */
    public function Recovery($email = ""){
        $this->Email = $email;
        $resultado = false;
        // validar datos y lanzar proceso de recuperación
        if($this->ValidarRecovery()){
            // Argumentos para la llamada
            $parametros = [ "email" => $this->Email,
                "project" => $this->Project, "service" => $this->Service ];
            // Resetear usuario
            $resultado = UserUtilities::ResetPassword($parametros);
            if($resultado == true){
                $this->Result = "Sus credenciales han sido reseteadas y "
                        . "notificadas a su cuenta de correo.";
                $this->ResultCss = "has-success";
            }
            else{
                // Mensaje de error estandar
                $this->Result="No ha sido posible resetear las "
                        . "credenciales de usuario. Contacte con "
                        . "el administrador.";
            }
        }
        return $resultado;
    }

    /**
     * Proceso de modificación de la contraseña
     * @param \ChangeDTO $dto Referencia al DTO con la información del
     * cambio de datos de autenticación
     */
    public function ChangePass($dto = null){
        $resultado = false;
        if($dto != null){
            $this->Email = $this->Username;
            $this->Pass = $dto->Pass;
            $this->NewPass = $dto->NewPass;
            $this->ReNewPass = $dto->ReNewPass;
        }

        if($this->ValidarChangePass()){
            // Argumentos para la llamada
            $parametros = [ "email" => $this->Email,
                "pass" => $this->Pass, "newpass" => $this->NewPass ];
            // Resetear usuario
            $resultado = UserUtilities::ChangePassword($parametros);

            if($resultado == true){
                $this->Result = "Sus credenciales han sido "
                        . "modificadas con éxito.";
                $this->ResultCss = "has-success";
            }
            else{
                // Mensaje de error estandar
                $this->Result="No ha sido posible modificar sus "
                        . "credenciales. Contacte con el administrador.";
            }
        }
        return $resultado;
    }

    /**
     * Proceso de validación de parámetros para recuperar contraseña
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
     * Proceso de validación de parámetros para el cambio de contraseña
     * @return boolean
     */
    private function ValidarChangePass(){
        $error = false;
        if(!isset($this->Email) || $this->Email == ""){
                $this->eEmail = "Debe especificar una dirección de e-mail.";
                $this->eEmailClass = "has-error";
                $error = true;
        }

        if(!isset($this->Pass) || $this->Pass == ""){
                $this->ePass = "Debe especificar su password actual.";
                $this->ePassClass = "has-error";
                $error = true;
        }

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

        if(isset($this->NewPass)
                && $this->NewPass != ""
                && isset($this->ReNewPass)
                && $this->ReNewPass != ""
                && $this->NewPass != $this->ReNewPass){

                $this->eReNewPass = "La contraseña y su repetición no coinciden.";
                $this->eReNewPassClass = "has-error";
                $error = true;
        }
        return !$error;
    }

}
