<?php




/**
 * Model para las operaciones sobre cuentas de usuario
 *
 * @author Alfonso
 */
class AccountsModel extends \PanelModel{

    /**
     * Referencia al dto de datos de formulario
     * @var \AccountDTO
     */
    public $AccountDTO = NULL;

    /**
     * Mensaje sobre el resultado de la última operación
     * @var string
     */
    public $eResult = "";

    /**
     * Clase CSS para el mensaje del resultado de la operación
     * @var string
     */
    public $eResultClass = "has-success";

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
     * @var string
     */
    public $ePass = "";

    /**
     * Clase CSS para el mensaje de error de la password actual
     * @var string
     */
    public $ePassClass = "";

    /**
     * Mensaje de error para la nueva password
     * @var string
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
     * Obtener el id del proyecto actual
     * @return int Identidad del proyecto
     */
    private function GetProjectId(){
        $project = ConfigurationManager::GetKey("project-name");
        // configurar el filtro de búsqueda
        $filter = [ "Name" => $project , "Active" => 1 ];
        // Obtener el proyecto resbooking
        $projects = $this->Dao->GetByFilter( "Project", $filter );
        // retornar el Id
        return $projects[0]->Id;
    }

    /**
     * Obtener el id de servicio actual
     * @return int Id del servicio
     */
    private function GetServiceId(){
        $service = ConfigurationManager::GetKey("service-name");
        // configurar el filtro de búsqueda
        $filter = [ "Name" => $service, "Active" => 1 ];
        // Obtener el servicio resbooking
        $services = $this->Dao->GetByFilter( "Service", $filter );
        // retornar el Id
        return $services[0]->Id;
    }

    /**
     * Constructor
     */
    public function __construct(){

        parent::__construct();

        $this->AccountDTO = new \AccountDTO();

        $this->SetCodes();
    }

    /**
     * Proceso de actualización de la contraseña de usuario
     * @param \AccountDTO $dto Referencia al DTO con la información de la solicitud
     * @return boolean Resultado de la operación
     */
    public function SaveNewPassword($dto = NULL){
        $result = FALSE;
        // Calcular los hash
        $dto->Password = (empty($dto->Password))
                ? $dto->Password : hash("sha512", $dto->Password);
        $dto->NewPassword = (empty($dto->NewPassword))
                ? $dto->NewPassword : hash("sha512", $dto->NewPassword);
        $dto->RepeatNewPassword = (empty($dto->RepeatNewPassword))
                ? $dto->RepeatNewPassword : hash("sha512", $dto->RepeatNewPassword);
        if($this->ValidateSaveNewPassword($dto)){
            // Set parámetros actualización contraseña
            $params = [ "email" => $this->Username, "pass" => $dto->Password,
                "newpass" => $dto->NewPassword ];
            // Ejecutar modificación
            $result = UserUtilities::ChangePassword($params);
            // Establecer código de error
            $this->ErrorCodes[] = ($result) ? 0 : -10;
            // Setear nuevo
            $this->AccountDTO = new \AccountDTO();
        }
        // Establecer el resultado de la operación
        $this->TranslateResultCodes();

        return $result;
    }

    /**
     * Proceso de recuperación de contraseñas
     * @param \AccountDTO $dto DTO datos de recuperación
     * @return boolean Resultado de la operación
     */
    public function RecoveryPassword($dto = NULL){
        $result = FALSE;
        if($this->ValidateRecoveryPassword($dto)){
            // Obtener el id de proyecto resbooking
            $project = $this->GetProjectId();
            // Obtener el id servicio resbooking
            $service = $this->GetServiceId();
            // Argumentos para la llamada
            $parametros = [ "email" => $dto->Email, "project" => $project,
                "service" => $service ];
            // Resetear usuario
            $result = UserUtilities::ResetPassword($parametros);
            // Establecer código de error
            $this->ErrorCodes[] = ($result) ? 0 : -10;
        }

        // Establecer el resultado de la operación
        $this->TranslateResultCodes();

        return $result;
    }

    /**
     * Proceso de validación de la dirección de e-mail
     */
    private function ValidateEmail($email = ""){
        if(empty($email)){
            $this->ErrorCodes[] = -2;
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->ErrorCodes[] = -3;
        }
        else{
            $filter = ["Username" => $email];
            $users = $this->Dao->GetByFilter("User", $filter);
            $counts = count($users);
            if($counts == 0){
                $this->ErrorCodes[] = -11;
            }
            else if($counts > 1){
                $this->ErrorCodes[] = -12;
            }
        }
    }

    /**
     * Proceso de validación de la contraseña de acceso
     */
    private function ValidatePassword($password = ""){
        if(empty($password)){
            $this->ErrorCodes[] = -4;
        }

        $user = $this->Dao->Read($this->UserId, "User");

        if($user instanceof \User){
            if($user->Password != $password){
                $this->ErrorCodes[] = -5;
            }
        }
        else{
            $this->ErrorCodes[] = -9;
        }
    }

    /**
     * Proceso de validación de las contraseñas nuevas
     * @param string $newPassword Contraseña de acceso nueva
     * @param string $repeatedNewPassword Repetición de la contraseña de acceso nueva
     */
    private function ValidateNewPassword($newPassword = "", $repeatedNewPassword = ""){
        if(empty($newPassword)){
            $this->ErrorCodes[] = -6;
        }
        else if(empty($repeatedNewPassword)){
            $this->ErrorCodes[] = -7;
        }
        else if($newPassword != $repeatedNewPassword){
            $this->ErrorCodes[] = -8;
        }
    }

    /**
     * Proceso de validación para cambiar la contraseña
     * @param \AccountDTO $dto Referencia al dto con información contraseña
     * @return boolean Resultado del proceso de validación
     */
    private function ValidateSaveNewPassword($dto = NULL ){
        if($dto != NULL){
            $this->ValidatePassword($dto->Password);
            $this->ValidateNewPassword(
                    $dto->NewPassword, $dto->RepeatNewPassword);
        }
        else{
            $this->ErrorCodes[] = -1;
        }
        return count($this->ErrorCodes) == 0;
    }

    /**
     * Proceso de validación para la recuperación de contraseñas
     * @param \AccountDTO $dto Referencia al dto con información de recuperación
     * @return boolean
     */
    private function ValidateRecoveryPassword($dto = NULL){
        if($dto != NULL){
            $this->ValidateEmail($dto->Email);
        }
        else{
            $this->ErrorCodes[] = -1;
        }
        return count($this->ErrorCodes) == 0;
    }

    /**
     * Establece el array de "traducción" de códigos de error
     * @return void
     */
    protected function SetCodes(){
       $this->Codes = [
           0 => [ "name" => "eResult", "msg" => "La operación se ha ejecutado correctamente" ],
           -1 => [ "name" => "eResult", "msg" => "No se ha recuperado la información." ],
           -2 => [ "name" => "eEmail", "msg" => "Debe especificar una dirección de email" ],
           -3 => [ "name" => "eEmail", "msg" => "La dirección de email no es válida" ],
           -4 => [ "name" => "ePass", "msg" => "Debe introducir la contraseña actual" ],
           -5 => [ "name" => "ePass", "msg" => "La contraseña actual es incorrecta" ],
           -6 => [ "name" => "eNewPass", "msg" => "Debe especificar la contraseña nueva"],
           -7 => [ "name" => "eReNewPass", "msg" => "Debe repetir la contraseña nueva"  ],
           -8 => [ "name" => "eReNewPass", "msg" => "Las contraseñas no coinciden" ],
           -9 => [ "name" => "eResult", "msg" => "Usuario no encontrado" ],
           -10 => [ "name" => "eResult", "msg" => "Se ha producido un error interno" ],
           -11 => [ "name" => "eResult", "msg" => "No se han encontrado los datos del usuario" ],
           -12 => [ "name" => "eResult", "msg" => "Se ha producido un error al buscar el usuario." ]
       ];
    }

}
