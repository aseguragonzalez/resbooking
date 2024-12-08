<?php

	include_once("model/ServiceModel.php");

	///<summary>
	/// Controlador para la gestión de proyectos
	///</summary>
	class ServiceController extends SaasController{

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
				// Instanciar model
				$model = new ServiceModel();
				// Cargar la lista de servicios disponibles
				$model->LoadServices();
				// Renderizar la vista con la info del modelo
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción por defecto : carga la lista de entidades
		///</summary>
		public function GetServices(){
			try{
				// Instanciar model
				$model = new ServiceModel();
				// Cargar la lista de servicios disponibles
				$model->LoadServices();
				// Renderizar la vista con la info del modelo
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para editar las asociaciones de roles
		///</summary>
		public function Roles($id = 0){
			try{
				// Instanciar model
				$model = new ServiceModel();
				// Cargar la información del formulario
				$model->LoadFormData($id);
				// Renderizar la vista con la info del modelo
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("Roles", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para guardar la información de la entidad
		///</summary>
		public function Save(){
			try{
				// Read entity from HttpRequest
				$entity = $this->GetEntity( "Service" );
				// Instanciar model
				$model = new ServiceModel();
				// Proceso de guardado
				$model->Save($entity);
				// retornar el resultado
				return json_encode($model);
			}
			catch(Exception $e){
				// Procesar la información de la excepción
				$this->Log->LogErrorTrace("Save", $e);
				// Instanciar el modelo
				$model = new ServiceModel();
				// Setear Modelo
				$model->SetAjaxError($e->getMessage());
				// retornar el resultado
				return json_encode($model);
			}
		}

		///<summary>
		/// Controlador para la gestión de secciones
		///</summary>
		public function Delete($id = 0){
			try{
				// Instanciar model
				$model = new ServiceModel();
				// Eliminar la entidad
				$model->Delete($id);
				// redirigir el flujo de ejecución
				return $this->RedirectTo("Index","Service");
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("Delete", $e);
				// Relanzar excepción
				throw $e;
			}
		}

		///<summary>
		/// Acción para asociar un role
		///</summary>
		public function AddRole(){
			try{
				// Instanciar model
				$model = new ServiceModel();
				// Read entity from HttpRequest
				$entity = $this->GetEntity( "ServiceRole" );
				// Crear asociación
				$model->AddRole($entity);
				// información de retorno
				return json_encode(array( "error" => false , "exception" => false ));
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("AddRole", $e);
				// Retornar la información del error
				return json_encode(array( "error" => true , "exception" => true, "info" => $e));
			}
		}

		///<summary>
		/// Acción para des-asociar un role
		///</summary>
		public function RemoveRole(){
			try{
				// Instanciar model
				$model = new ServiceModel();
				// Read entity from HttpRequest
				$entity = $this->GetEntity( "ServiceRole" );
				// Eliminar asociación
				$model->RemoveRole($entity);
				// información de retorno
				return json_encode(array( "error" => false , "exception" => false ));
			}
			catch(Exception $e){
				// Generar la traza de error
				$this->Log->LogErrorTrace("RemoveRole", $e);
				// retornar la información del error
				return json_encode(array( "error" => true , "exception" => true , "info" => $e));
			}
		}
	}
?>
