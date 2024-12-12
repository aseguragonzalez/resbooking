<?php

	// Cargar el modelo
	include_once("model/UserModel.php");

	///<summary>
	/// Controlador para la gestión de proyectos
	///</summary>
	class UserController extends SaasController{

		///<summary>
		/// Constructor por defecto
		///</summary>
		public function __construct(){
			parent::__construct();
		}

		///<summary>
		/// Acción por defecto : carga la lista de entidades
		///</summary>
		public function Index(){
			try{
				// Instancia el modelo
				$model = new UserModel();
				// Cargar la lista de usuarios disponibles
				$model->LoadUsers();
				// Renderizar la vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace( "Index", $e);
				// Relanzar la excepción
				throw $e;
			}
		}

		///<summary>
		/// Carga la lista de usuarios
		///</summary>
		public function GetUsers(){
			try{
				// Instancia el modelo
				$model = new UserModel();
				// Cargar la lista de usuarios disponibles
				$model->LoadUsers();
				// Renderizar la vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace( "Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para guardar la información de la entidad
		///</summary>
		public function Save(){
			try{
				// Obtener datos de la entidad
				$entity = $this->GetEntity( "User" );
				// Instancia el modelo
				$model = new UserModel();
				// Guardar datos del usuario
				$model->Save($entity);
				// retornar modelo serializado
				return json_encode($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace( "Save" , $e);
				// Instanciar el modelo
				$model = new UserModel();
				// Setear Modelo
				$model->SetAjaxError($e->getMessage());
				// retornar el resultado
				return json_encode($model);
			}
		}

		///<summary>
		/// Eliminación del usuario seleccionado
		///</summary>
		public function Delete($id = 0){
			try{
				// Instancia el modelo
				$model = new UserModel();
				// Eliminar el usuario seleccionado
				$model->Delete($id);
				// Redirigir el flujo de ejecución
				return $this->RedirectTo( "Index", "User");
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace( "Delete", $e);
				// Relanzar la excepción
				throw $e;
			}
		}

		///<summary>
		/// Reseteo de password de usuario
		///</summary>
		public function Reset($id = 0){
			try{
				// Instancia el modelo
				$model = new UserModel();
				// Resetear la password de usuario
				$model->Reset($id);
				// información de retorno
				return json_encode(array( "error" => false , "exception" => false ));
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("Delete", $e);
				// retornar información del error
				return json_encode(array( "error" => true , "exception" => true, "info" => $e->getMessage()));
			}
		}

		///<summary>
		/// Acción para la edición del proyecto
		///</summary>
		public function Relations($id = 0, $p = 0){
			try{
				// Instancia el modelo
				$model = new UserModel();
				// Cargar los datos de formulario
				$model->LoadRelationsForm($id, $p);
				// Renderizar la vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("Edit", $e);
				// Relanzar la excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para la edición del proyecto
		///</summary>
		public function SetRelation(){
			try{
				// Read entity from HttpRequest
				$entity = $this->GetEntity( "UserRoleServiceProject" );
				// Instancia el modelo
				$model = new UserModel();
				// Cargar los datos de formulario
				$model->SaveRelation($entity);
				// información de retorno
				return json_encode(array( "error" => false , "exception" => false ));
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace( "SetRelations", $e);
				// retornar información del error
				return json_encode(array( "error" => true , "exception" => true, "info" => $e->getMessage()));
			}
		}
	}

?>
