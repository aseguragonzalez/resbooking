<?php

	include_once("model/HomeModel.php");

	///<summary>
	/// Controlador para las acciones públicas
	///</summary>
	class HomeController extends LcmsController{

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// Constructor base
			parent::__construct();
		}

		///<summary>
		/// Cargar la página inicial
		///</summary>
		public function Index(){
			try{
				// Nombre de la vista por defecto
				$view = "Index";
				// Instancia del modelo
				$model = new HomeModel();
				// Validar si está autenticado
				if($model->Username != ""){
					// filtrar proyectos por usuario
					$model->LoadProjects();
					// establecer el nombre de la vista
					$view = "AuthIndex";
				}
				// Renderizar vista
				return $this->Partial($view, $model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Setear el proyecto seleccionado
		///</summary>
		public function SetProject(){
			try{
				// Instancia del modelo
				$model = new HomeModel();
				// Obtener datos de la petición
				$projectInfo = $this->GetEntity( "ProjectInfo" );
				// Obtener Id de proyecto si corresponde
				$id = (is_numeric($projectInfo->Id)) ? intval($projectInfo->Id) : 0;
				// Validar Id
				if($id > 0){
					// Establecer proyecto seleccionado
					$model->SetProject($id);
					// Redirigir el flujo
					return $this->RedirectTo( "Index", "Section" );
				}
				// Si no hay proyecto
				return $this->RedirectTo( "Index", "Home" );
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("SetProject", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Obtiene el formulario con la información del servicio
		///</summary>
		public function About(){
			try{
				// Instancia del modelo
				$model = new HomeModel();
				// Renderizar vista parcial
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->LogErrorTrace("Index", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Obtiene el formulario con la política de privacidad
		///</summary>
		public function Privacity(){
			try{
				// Instancia del modelo
				$model = new HomeModel();
				// Renderizar vista parcial
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Obtiene el formulario sobre la advertencia legal
		///</summary>
		public function Legal(){
			try{
				// Instancia del modelo
				$model = new HomeModel();
				// Renderizar vista parcial
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar el error
				throw $e;
			}
		}

		public function Logout(){
			try{
				session_destroy();
				// Instancia del modelo
				$model = new HomeModel();
					// Redirigir el flujo
				return $this->RedirectTo( "Index", "Home");
			}
			catch(Exception $e){
				// Registrar el error
				$this->LogErrorTrace("Index", $e);
				// Relanzar el error
				throw $e;
			}
		}
	}

?>
