<?php

require_once("model/ProjectModel.php");

///<summary>
/// Controlador para la gestión de proyectos
///</summary>
class ProjectController extends SaasController
{
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
			$model = new ProjectModel();
			// Cargar los proyectos
			$model->LoadProjects();
			// Renderizar la vista
			return $this->PartialView($model);
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "Index" , $e);
			// Propagar la excepción
			throw $e;
		}
	}

	///<summary>
	/// Carga la lista de entidades
	///</summary>
	public function GetProjects(){
		try{
			// Instanciar el modelo
			$model = new ProjectModel();
			// Cargar los proyectos
			$model->LoadProjects();
			// Renderizar la vista
			return $this->PartialView($model);
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "GetProjects" , $e);
			// Propagar la excepción
			throw $e;
		}
	}


	///<summary>
	/// Guardar la información del proyecto
	///</summary>
	public function Save(){
		try{
			// Read entity from HttpRequest
			$entity = $this->GetEntity( "Project" );
			// Instanciar el modelo
			$model = new ProjectModel();
			// Guardar datos del proyecto
			$model->Save($entity);
			$model->Entity->Fecha = $model->Entity->Date;
			// Retornar el modelo serializado
			return json_encode($model);
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "Save" , $e);
			// Instanciar el modelo
			$model = new ProjectModel();
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
			// Instanciar el modelo
			$model = new ProjectModel();
			// Eliminar el proyecto
			$model->Delete($id);
			// Redirigir el flujo de ejecución
			return $this->RedirectTo( "Index", "Project" );
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "Delete" , $e);
			// Propagar la excepción
			throw $e;
		}
	}


	///<summary>
	/// Obtiene el formulario para asociar servicios al proyecto
	///</summary>
	public function Services($id=0){
		try{
			// Instanciar el modelo
			$model = new ProjectModel();
			// Cargar los proyectos
			$model->LoadServices($id);
			// Renderizar la vista
			return $this->PartialView($model);
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "Services" , $e);
			// Propagar la excepción
			throw $e;
		}
	}

	///<summary>
	/// Acción para asociar un servicio
	///</summary>
	public function AddService(){
		try{
			// Read entity from HttpRequest
			$entity = $this->GetEntity( "ProjectServices" );
			// Instanciar el modelo
			$model = new ProjectModel();
			// Crear asociación
			$model->AddService($entity);
			// información de retorno
			return json_encode(array( "error" => false , "exception" => false ));
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "AddService" , $e);
			// Información del error
			$info = array( "error" => true , "exception" => true, "info" => $e->getMessage());
			// retornar json
			return json_encode($info);
		}
	}

	///<summary>
	/// Acción para des-asociar un servicio
	///</summary>
	public function RemoveService(){
		try{
			// Read entity from HttpRequest
			$entity = $this->GetEntity( "ProjectServices" );
			// Instanciar el modelo
			$model = new ProjectModel();
			// Eliminar asociación
			$model->RemoveService($entity);
			// información de retorno
			return json_encode(array( "error" => false , "exception" => false ));
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "RemoveService" , $e);
			// Información del error
			$info = array( "error" => true , "exception" => true, "info" => $e->getMessage());
			// retornar json
			return json_encode($info);
		}
	}

	///<summary>
	/// Obtiene el formulario para asociar usuarios al proyecto
	///</summary>
	public function Users($id = 0){
		try{
			// Instanciar el modelo
			$model = new ProjectModel();
			// Cargar los usuarios
			$model->LoadUsers($id);
			// Renderizar la vista
			return $this->PartialView($model);
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "Users" , $e);
			// Propagar la excepción
			throw $e;
		}
	}

	///<summary>
	/// Acción para asociar un usuario
	///</summary>
	public function AddUser(){
		try{
			// Read entity from HttpRequest
			$entity = $this->GetEntity( "ProjectUsers" );
			// Instanciar el modelo
			$model = new ProjectModel();
			// Crear asociación
			$model->AddUser($entity);
			// información de retorno
			return json_encode(array( "error" => false , "exception" => false ));
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "AddUser" , $e);
			// Información del error
			$info = array( "error" => true , "exception" => true, "info" => $e->getMessage());
			// retornar json
			return json_encode($info);
		}
	}

	///<summary>
	/// Acción para des-asociar un usuario
	///</summary>
	public function RemoveUser(){
		try{
			// Instanciar el modelo
			$model = new ProjectModel();
			// Read entity from HttpRequest
			$entity = $this->GetEntity( "ProjectUsers" );
			// Eliminar asociación
			$model->RemoveUser($entity);
			// información de retorno
			return json_encode(array( "error" => false , "exception" => false ));
		}
		catch(Exception $e){
			// Generar la traza de error
			$this->Log->LogErrorTrace( "RemoveUser" , $e);
			// Información del error
			$info = array( "error" => true , "exception" => true, "info" => $e->getMessage());
			// retornar json
			return json_encode($info);
		}
	}
}
