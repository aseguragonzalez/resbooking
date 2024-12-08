<?php

	class ChangeDTO{
		public $Email = "";
		public $Pass = "";
		public $NewPass = "";
		public $ReNewPass = "";
	}

	///<summary>
	/// Model para las operaciones sobre cuentas de usuario
	///</summary>
	class AccountsModel extends SaasModel{

		///<summary>
		/// E-mail del usuario a resetear
		///</summary>
		public $Email = "";

		///<summary>
		/// Password original (Hash)
		///</summary>
		public $Pass = "";

		///<summary>
		/// Nueva password a utilizar (Hash)
		///</summary>
		public $NewPass = "";

		///<summary>
		/// Repetición password a utilizar (Hash)
		///</summary>
		public $ReNewPass = "";

		///<summary>
		/// Texto resultado de la operación
		///</summary>
		public $Result = "";

		///<summary>
		/// Clase css para el resultado de la operación
		///</summary>
		public $ResultCss = "has-error";

		///<summary>
		/// Propiedades para la gestión de errores
		///</summary>
		public $eEmail = "";
		public $eEmailClass = "";
		public $ePass = "";
		public $ePassClass = "";
		public $eNewPass = "";
		public $eNewPassClass = "";
		public $eReNewPass = "";
		public $eReNewPassClass = "";

		///<summary>
		/// Constructor de la clase
		///</summary>
		public function __construct(){
			// Cargar constructor padre
			parent::__construct();
		}

		///<summary>
		/// Proceso de recuperación de contraseña
		///</summary>
		public function Recovery($email = ""){
			$this->Email = $email;
			$resultado = false;
			// validar datos y lanzar proceso de recuperación
			if($this->ValidarRecovery()){
				// Argumentos para la llamada
				$parametros = array( "email" => $this->Email, "project" => $this->Project, "service" => $this->Service);
				// Resetear usuario
				$resultado = UserUtilities::ResetPassword($parametros);
				if($resultado == true){
					$this->Result = "Sus credenciales han sido reseteadas y notificadas a su cuenta de correo.";
					$this->ResultCss = "has-success";
				}
				else{
					// Mensaje de error estandar
					$this->Result="No ha sido posible resetear las credenciales de usuario. Contacte con el administrador.";
				}
			}
			return $resultado;
		}

		///<summary>
		/// Proceso de modificación de la contraseña
		///</summary>
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
				$parametros = array( "email" => $this->Email, "pass" => $this->Pass, "newpass" => $this->NewPass);
				// Resetear usuario
				$resultado = UserUtilities::ChangePassword($parametros);

				if($resultado == true){
					$this->Result = "Sus credenciales han sido modificadas con éxito.";
					$this->ResultCss = "has-success";
				}
				else{
					// Mensaje de error estandar
					$this->Result="No ha sido posible modificar sus credenciales. Contacte con el administrador.";
				}
			}

			return $resultado;
		}

		private function ValidarRecovery(){
			$error = false;
			if(!isset($this->Email) || $this->Email == ""){
				$this->eEmail = "Debe especificar una dirección de e-mail.";
				$this->eEmailClass = "has-error";
				$error = true;
			}
			return !$error;
		}

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

?>
