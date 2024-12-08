<?php

	include_once("model/AccountsModel.php");

	///<summary>
	/// Controlador para el login
	///</summary>
	class AccountsController extends Controller{

		///<summary>
		/// Constructor por defecto
		///</summary>
		public function __construct(){
			parent::__construct();
		}

		///<summary>
		/// Formulario de recuperación de contraseña
		///</summary>
		public function Index(){
			try{
				if(isset($_SESSION[ "recoveryModel" ])){
					// Recuperar referencia de la sesión
					$model = json_decode($_SESSION[ "recoveryModel" ]);
					// eliminar referencia
					unset($_SESSION[ "recoveryModel" ]);
				}
				else
					$model = new AccountsModel();

				return $this->PartialView($model);
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para visualizar el formulario de contraseñas
		///</summary>
		public function ChangePass(){
			try{

				if(isset($_SESSION[ "changeModel" ])){
					// Recuperar referencia de la sesión
					$model = json_decode($_SESSION[ "changeModel" ]);
					// eliminar referencia
					unset($_SESSION[ "changeModel" ]);
				}
				else
					$model = new AccountsModel();

				return $this->PartialView($model);
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para modificar contraseña
		///</summary>
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
					return $this->RedirectTo( "ChangePass", "Accounts" );
				}
				return $this->PartialView($model);
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para generar la recuperación de la contraseña
		///</summary>
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
				return $this->RedirectTo( "Index", "Accounts" );
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para iniciar la sessión actual
		///</summary>
		public function Login(){
			try{
				$model = new AccountsModel();

				return $this->RedirectTo("Index", "Home");
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para cerrar la sessión actual
		///</summary>
		public function Logout(){
			try{
				session_destroy();

				return $this->RedirectTo("Index", "Home");
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Index", $e);

				throw $e;
			}
		}

	}

?>
