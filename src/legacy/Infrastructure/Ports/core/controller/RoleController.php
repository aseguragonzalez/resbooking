<?php

	// Incluir el modelo
	include_once("model/RoleModel.php");

	///<summary>
	/// Controlador para la gestión de proyectos
	///</summary>
	class RoleController extends SaasController{

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
				// Instanciar el modelo
				$model = new RoleModel();
				// Cargar todos los roles
				$model->LoadRoles();
				// Renderizar la vista con la info del modelo
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Carga la lista de roles
		///</summary>
		public function GetRoles(){
			try{
				// Instanciar el modelo
				$model = new RoleModel();
				// Cargar todos los roles
				$model->LoadRoles();
				// Renderizar la vista con la info del modelo
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace( "GetRoles", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para la edición del proyecto
		///</summary>
		public function Services($id = 0){
			try{
				// Instanciar el modelo
				$model = new RoleModel();
				// Cargar toda la información para el formulario
				$model->LoadFormData($id);
				// Renderizar la vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace( "Services", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para guardar la información de la entidad via Ajax
		///</summary>
		public function Save(){
			try{
				// Read entity from HttpRequest
				$entity = $this->GetEntity("Role");
				// Instanciar el modelo
				$model = new RoleModel();
				// Proceso de guardado
				$model->Save($entity);
				// retornar el resultado
				return json_encode($model);
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace( "Save", $e);
				// Instanciar el modelo
				$model = new RoleModel();
				// Setear Modelo
				$model->SetAjaxError($e->getMessage());
				// retornar el resultado
				return json_encode($model);
			}
		}

		///<summary>
		/// Acción para la eliminación de un role
		///</summary>
		public function Delete($id = 0){
			try{
				// Instanciar el modelo
				$model = new RoleModel();
				// Eliminar el rol especificado
				$model->Delete($id);
				// Redirigir el flujo de la ejecución
				return $this->RedirectTo( "Index", "Role");
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace( "Delete", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para asociar un servicio
		///</summary>
		public function Add(){
			try{
				// Instanciar el modelo
				$model = new RoleModel();
				// Read entity from HttpRequest
				$entity = $this->GetEntity( "ServiceRole" );
				// Crear asociación
				$model->AddService($entity->IdRole, $entity->IdService);
				// información de retorno
				return json_encode(array( "error" => false , "exception" => false ));
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace("Add", $e->getMessage());
				// retornar json
				return json_encode(array( "error" => true , "exception" => true, "info" => $e->getMessage()));
			}
		}

		///<summary>
		/// Acción para des-asociar un servicio
		///</summary>
		public function Remove(){
			try{
				// Instanciar el modelo
				$model = new RoleModel();
				// Read entity from HttpRequest
				$entity = $this->GetEntity( "ServiceRole" );
				// Eliminar asociación
				$model->RemoveService($entity->IdRole, $entity->IdService);
				// información de retorno
				return json_encode(array( "error" => false , "exception" => false ));
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace("Remove", $e->getMessage());
				// retornar json
				return json_encode(array( "error" => true , "exception" => true , "info" => $e->getMessage()));
			}
		}

	}

?>
